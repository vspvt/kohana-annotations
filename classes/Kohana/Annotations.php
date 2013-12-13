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
	 * Get annotations for class
	 *
	 * @param \ReflectionClass|string|object $class
	 *
	 * @return array
	 */
	static function getClassAnnotations($class)
	{
		return static::instance()->getClassAnnotations(static::getReflectionClass($class));
	}

	/**
	 * Get selected annotation for class
	 *
	 * @param \ReflectionClass|string|object $class
	 * @param string                         $annotationName
	 *
	 * @return null
	 */
	static function getClassAnnotation($class, $annotationName)
	{
		return static::instance()->getClassAnnotation(static::getReflectionClass($class), $annotationName);
	}

	/**
	 * @param \ReflectionMethod|string $method
	 * @param null                     $class
	 *
	 * @return array
	 */
	static function getMethodAnnotations($method, $class = NULL)
	{
		$method instanceof ReflectionMethod or $method = new ReflectionMethod($class, $method);

		return static::instance()->getMethodAnnotations($method);
	}

	/**
	 * @param      $method
	 * @param      $annotationName
	 * @param null $class
	 *
	 * @return null
	 */
	static function getMethodAnnotation($method, $annotationName, $class = NULL)
	{
		$method instanceof ReflectionMethod or $method = new ReflectionMethod($class, $method);

		return static::instance()->getMethodAnnotation($method, $annotationName);
	}

	/**
	 * @param $name
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

}
