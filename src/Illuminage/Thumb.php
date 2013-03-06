<?php
namespace Illuminage;

use App;
use HtmlObject\Traits\Tag;

/**
 * The thumb of an image
 */
class Thumb extends Tag
{
  /**
   * The thumb width
   *
   * @var integer
   */
  protected $width;

  /**
   * The thumb height
   *
   * @var integer
   */
  protected $height;

  /**
   * Path to the image
   *
   * @var string
   */
  protected $image;

  /**
   * The Illuminage instance
   *
   * @var Illuminage
   */
  protected $illuminage;

  /**
   * The HtmlObject element
   *
   * @var string
   */
  protected $element = 'img';

  /**
   * Set as self-closing element
   *
   * @var boolean
   */
  protected $isSelfClosing = true;

  /**
   * Build a new Thumb
   *
   * @param string  $image  Path to the image
   * @param integer $width
   * @param integer $height
   */
  public function __construct($image, $width, $height)
  {
    $this->image      = $image;
    $this->width      = $width;
    $this->height     = $height;
    $this->illuminage = App::make('illuminage');
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

  ////////////////////////////////////////////////////////////////////
  ////////////////////////// PUBLIC METHODS //////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get the image
   *
   * @return string
   */
  public function getImage()
  {
    return $this->image;
  }

  /**
   * Get the full path to the image
   *
   * @return string
   */
  public function getImagePath()
  {
    return App::make('path.public').'/'.$this->getImage();
  }

  /**
   * Get the thumb's width
   *
   * @return integer
   */
  public function getWidth()
  {
    return $this->width;
  }

  /**
   * Get the thumb's height
   *
   * @return integer
   */
  public function getHeight()
  {
    return $this->height;
  }

  /**
   * Get the rendered thumb's path
   *
   * @return string
   */
  public function getThumb()
  {
    return $this->illuminage->createThumb($this);
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// OUTPUT //////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Renders the image
   *
   * @return string
   */
  public function injectProperties()
  {
    return array(
      'src' => $this->getThumb()
    );
  }
}