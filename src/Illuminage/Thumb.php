<?php
namespace Illuminage;

use App;

/**
 * The thumb of an image
 */
class Thumb extends Image
{

  /**
   * Build a new Thumb
   *
   * @param string  $image  Path to the image
   * @param integer $width
   * @param integer $height
   */
  public function __construct($image, $width, $height)
  {
    $this->image           = $image;
    $this->salts['width']  = $width;
    $this->salts['height'] = $height;

    $this->illuminage = App::make('illuminage');
    $this->imagine    = $this->illuminage->bindImagine($this);
  }

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
