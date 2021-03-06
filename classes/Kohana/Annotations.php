<?php
use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * @author: Vad Skakov <vad.skakov@gmail.com>
 */
class Kohana_Annotations
{
	/** @var \Doctrine\Common\Annotations\CachedReader */
	protected static $_instance;

	static function config($path = NULL, $default = NULL, $delimeter = NULL)
	{
		$config = Kohana::$config->load('annotations')->as_array();

		return NULL === $path
			? $config
			: Arr::path($config, $path, $default, $delimeter);
	}

	/**
	 * @return \Doctrine\Common\Annotations\CachedReader
	 */
	static function instance()
	{
		if (!static::$_instance) {
			AnnotationRegistry::registerLoader(function ($class) {
				$file = str_replace("\\", DIRECTORY_SEPARATOR, $class);

				$path = Kohana::find_file(self::config('directory'), $file);
				if (FALSE !== $path) {
					require_once $path;

					return TRUE;
				}

				return FALSE;
			});

			$cache = new \Doctrine\Common\Cache\ArrayCache;
			$annotationReader = new \Doctrine\Common\Annotations\AnnotationReader;
			static::$_instance = new \Doctrine\Common\Annotations\CachedReader(
				$annotationReader,
				$cache
			);
		}

		return static::$_instance;
	}

	/**
	 * Get class annotations
	 *
	 * @param mixed $class
	 *
	 * @return array<object>
	 */
	static function getClassAnnotations($class)
	{
		return static::instance()->getClassAnnotations(static::getReflectionClass($class));
	}

	/**
	 * Get class annotation
	 *
	 * @param mixed  $class
	 * @param string $annotationName
	 *
	 * @return null|object
	 */
	static function getClassAnnotation($class, $annotationName)
	{
		return static::instance()->getClassAnnotation(static::getReflectionClass($class), $annotationName);
	}

	/**
	 * Get method annotations
	 *
	 * @param mixed $method
	 * @param mixed $class
	 *
	 * @return array<object>
	 */
	static function getMethodAnnotations($method, $class = NULL)
	{
		$method instanceof ReflectionMethod or $method = new ReflectionMethod($class, $method);

		return static::instance()->getMethodAnnotations($method);
	}

	/**
	 * Get method annotation
	 *
	 * @param mixed  $method
	 * @param string $annotationName
	 * @param mixed  $class
	 *
	 * @return null|object
	 */
	static function getMethodAnnotation($method, $annotationName, $class = NULL)
	{
		$method instanceof ReflectionMethod or $method = new ReflectionMethod($class, $method);

		return static::instance()->getMethodAnnotation($method, $annotationName);
	}

	/**
	 * Get annotation object
	 *
	 * @param string $name
	 *
	 * @return object
	 * @throws Doctrine\Common\Annotations\AnnotationException
	 */
	static function annotationClass($name)
	{
		if (!AnnotationRegistry::loadAnnotationClass($name)) {
			throw new \Doctrine\Common\Annotations\AnnotationException('Annotation ' . $name . ' - not exists');
		}

		return new $name;
	}

	/**
	 * @param mixed $class
	 *
	 * @return ReflectionClass
	 */
	protected static function getReflectionClass($class)
	{
		$class instanceof \ReflectionClass or $class = new ReflectionClass($class);

		return $class;
	}

	/**
	 * @param mixed $method
	 * @param mixed $class
	 *
	 * @return ReflectionMethod
	 */
	protected static function getReflectionMethod($method, $class = NULL)
	{
		$method instanceof ReflectionMethod or $method = new ReflectionMethod($class, $method);

		return $method;
	}

	/**
	 * @param mixed $class
	 * @param null  $method
	 *
	 * @return array
	 */
	static function getAnnotations($class, $method = NULL)
	{
		$result = [];
		foreach (Arr::merge(self::getClassAnnotations($class), NULL !== $method
			? self::getMethodAnnotations($method, $class) : []) as $obj) {
			$result[get_class($obj)] = $obj;
		};

		return $result;
	}

	/**
	 * @param string $name
	 * @param mixed  $class
	 * @param null   $method
	 * @param bool   $nullable
	 *
	 * @return null|object
	 */
	static function getAnnotation($name, $class, $method = NULL, $nullable = TRUE)
	{
		try {
			NULL === $method or $annotation = self::getMethodAnnotation($method, $name, $class);
			isset($annotation) or $annotation = self::getClassAnnotation($class, $name);
		} catch (ReflectionException $e) {
			$annotation = NULL;
		}

		return !$nullable && NULL === $annotation
			? self::annotationClass($name)
			: $annotation;
	}

	/**
	 * @param string $name
	 * @param mixed  $class
	 * @param mixed  $method
	 *
	 * @return bool
	 */
	static function hasAnnotation($name, $class, $method = NULL)
	{
		return NULL !== self::getAnnotation($name, $class, $method, TRUE);
	}

	/**
	 * @param string $name
	 * @param string $param
	 * @param mixed  $class
	 * @param mixed  $method
	 * @param mixed  $default
	 *
	 * @return mixed
	 */
	static function getAnnotationParam($name, $param, $class, $method = NULL, $default = NULL)
	{
		$annotation = self::getAnnotation($name, $class, $method, TRUE);

		return NULL === $annotation || !isset($annotation->{$param})
			? $default
			: $annotation->{$param};
	}

	/**
	 * @param string $name
	 * @param mixed  $class
	 * @param mixed  $method
	 * @param mixed  $default
	 *
	 * @return mixed
	 */
	static function getAnnotationValue($name, $class, $method = NULL, $default = NULL)
	{
		return self::getAnnotationParam($name, 'value', $class, $method, $default);
	}

}
