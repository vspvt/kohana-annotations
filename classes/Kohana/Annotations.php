<?php

/**
 * @author: Vad Skakov <vad.skakov@gmail.com>
 */
class Kohana_Annotations
{
	/** @var \Doctrine\Common\Annotations\CachedReader */
	protected static $_instance;

	/**
	 * @return \Doctrine\Common\Annotations\CachedReader
	 */
	static function instance()
	{
		if (!static::$_instance) {
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
