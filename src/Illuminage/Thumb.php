<?php
namespace Illuminage;

use App;

/**
 * The thumb of an image
 */
class Thumb extends Image
{

  /**
   * Static alias for the constructor
   */
  public static function create($image, $width, $height)
  {
    return new static($image, $width, $height);
  }

  /**
   * Static alias to create a square thumb
   */
  public static function square($image, $size)
  {
    return new static($image, $size, $size);
  }

}
