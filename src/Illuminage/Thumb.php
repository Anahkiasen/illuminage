<?php
namespace Illuminage;

use App;
use Closure;
use HtmlObject\Traits\Tag;
use Imagine\Image\Color;

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
   * The Imagine instance
   *
   * @var Imagine
   */
  protected $imagine;

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
    $this->imagine    = $this->illuminage->createThumb($this);
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
    return $this->illuminage->renderThumb($this);
  }

  /**
   * Get the Imagine instance
   *
   * @return Imagine
   */
  public function getImagine()
  {
    return $this->imagine;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// FILTERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Applies a Closure to the Imagine instance
   *
   * @param Closure $closure
   */
  public function onImage(Closure $closure)
  {
    $closure($this->imagine);

    return $this;
  }

  /**
   * Inverts the colors
   */
  public function negative()
  {
    $this->imagine->effects()->negative();

    return $this;
  }

  /**
   * Makes the image black and white
   */
  public function grayscale()
  {
    $this->imagine->effects()->grayscale();

    return $this;
  }

  /**
   * Applies a Gamma correction
   *
   * @param integer $gamma The factor
   */
  public function gamma($gamma = 1)
  {
    $this->imagine->effects()->gamma($gamma);

    return $this;
  }

  /**
   * Colorizes the image
   *
   * @param string $color An hexadecimal value
   */
  public function colorize($color)
  {
    $this->imagine->effects()->colorize(new Color($color));

    return $this;
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
