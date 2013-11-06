<?php
namespace Illuminage;

use SplFileObject;

/**
 * A rich version of the SplFileObject
 */
class SplFileImage extends SplFileObject
{
  /**
   * The image's dimensions
   *
   * @var array
   */
  private $dimensions = null;

  /**
   * Get the image's dimensions
   *
   * @return array
   */
  public function getDimensions()
  {
    if (is_null($this->dimensions)) {
      $this->dimensions = getimagesize($this->getRealPath());
    }

    return $this->dimensions;
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
