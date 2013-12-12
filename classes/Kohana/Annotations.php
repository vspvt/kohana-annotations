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
				}
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
	 * @param mixed $class
	 *
	 * @return ReflectionClass
	 */
	static function reflectionClass($class)
	{
		$class instanceof \ReflectionClass or $class = new ReflectionClass($class);

		return $class;
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
		return static::instance()->getClassAnnotations(static::reflectionClass($class));
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
		return static::instance()->getClassAnnotation(static::reflectionClass($class), $annotationName);
	}

}
