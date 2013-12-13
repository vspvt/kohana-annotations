<?php

/**
 * @author: Vad Skakov <vad.skakov@gmail.com>
 */
trait Trait_Annotations
{
	/**
	 * @param string $methodName
	 *
	 * @return array
	 */
	public function getAnnotations($methodName = NULL)
	{
		return NULL === $methodName
			? Annotations::getClassAnnotations($this)
			: Annotations::getMethodAnnotations($methodName, $this);
	}

	/**
	 * @param string $name
	 * @param string $methodName
	 *
	 * @return array
	 */
	public function getAnnotation($name, $methodName = NULL)
	{
		return NULL === $methodName
			? Annotations::getClassAnnotation($this, $name)
			: Annotations::getMethodAnnotation($methodName, $name, $this);
	}

	/**
	 * @param string $name
	 * @param string $methodName
	 *
	 * @return bool
	 */
	public function hasAnnotation($name, $methodName = NULL)
	{
		return NULL !== $this->getAnnotation($name, $methodName);
	}
}
