<?php

/**
 * @author: Vad Skakov <vad.skakov@gmail.com>
 */
trait Trait_Annotations
{
	/**
	 * @param string $methodName
	 *
	 * @return array<object>
	 */
	public function getAnnotations($methodName = NULL)
	{
		return Annotations::getAnnotations($this, $methodName);
	}

	/**
	 * @param string $name
	 * @param string $methodName
	 * @param bool   $nullable
	 *
	 * @return null|object
	 */
	public function getAnnotation($name, $methodName = NULL, $nullable = TRUE)
	{
		return Annotations::getAnnotation($name, $this, $methodName, $nullable);
	}

	/**
	 * @param string $name
	 * @param string $methodName
	 *
	 * @return bool
	 */
	public function hasAnnotation($name, $methodName = NULL)
	{
		return NULL !== Annotations::hasAnnotation($name, $this, $methodName);
	}

	/**
	 * @param string $name
	 * @param string $param
	 * @param string $methodName
	 * @param mixed  $default
	 *
	 * @return mixed
	 */
	public function getAnnotationParam($name, $param, $methodName = NULL, $default = NULL)
	{
		return Annotations::getAnnotationParam($name, $param, $this, $methodName, $default);
	}
}
