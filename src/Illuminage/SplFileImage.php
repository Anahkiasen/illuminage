<?php
namespace Illuminage;

use SplFileObject;

/**
 * A rich version of the SplFileObject
 */
class SplFileImage extends SplFileObject
{

	/**
	 * Get the image's dimensions
	 *
	 * @return array
	 */
	public function getDimensions()
	{
		return getimagesize($this->getRealPath());
	}

	/**
	 * Get the image's width
	 *
	 * @return integer
	 */
	public function getWidth()
	{
		$dimensions = $this->getDimensions();

		return $dimensions[0];
	}

	/**
	 * Get the image's height
	 *
	 * @return integer
	 */
	public function getHeight()
	{
		$dimensions = $this->getDimensions();

		return $dimensions[1];
	}

}