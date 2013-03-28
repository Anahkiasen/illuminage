<?php
namespace Illuminage;

use App;
use Closure;
use HtmlObject\Traits\Tag;
use Imagine\Image\Box;
use Imagine\Image\Color;

/**
 * The thumb of an image
 */
class Image extends Tag
{

  /**
   * An array of cache salts to use
   *
   * @var array
   */
  protected $salts = array();

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
  public function __construct($image)
  {
    $this->image      = $image;

    $this->illuminage = App::make('illuminage');
    $this->imagine    = $this->illuminage->bindImagine($this);
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
   * Get the Image's salts
   *
   * @param string $salt A particular salt to get, all if null
   *
   * @return string|array
   */
  public function getSalt($salt = null)
  {
    if ($salt) return $this->salts[$salt];

    return $this->salts;
  }

  /**
   * Get the rendered thumb's path
   *
   * @return string
   */
  public function getThumb()
  {
    return $this->illuminage->cacheAndRender($this);
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
   * Resize an Image
   *
   * @param integer $width
   * @param integer $height
   */
  public function resize($width, $height)
  {
    $this->salts['width']  = $width;
    $this->salts['height'] = $height;

    $this->imagine->resize(new Box($width, $height));

    return $this;
  }

  /**
   * Inverts the colors
   */
  public function negative()
  {
    $this->salts[] = 'negative';
    $this->imagine->effects()->negative();

    return $this;
  }

  /**
   * Makes the image black and white
   */
  public function grayscale()
  {
    $this->salts[] = 'grayscale';
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
    $this->salts['gamma'] = $gamma;
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
    $this->salts['colorize'] = $color;
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
