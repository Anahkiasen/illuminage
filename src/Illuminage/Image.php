<?php
namespace Illuminage;

use Closure;
use HtmlObject\Traits\Tag;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\Color;
use Imagine\Image\ImageInterface;

/**
 * A basic image
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
   * Build a new Image
   *
   * @param Illuminage $illuminage
   * @param string     $image       Path to the image
   */
  public function __construct(Illuminage $illuminage, $image)
  {
    $this->illuminage = $illuminage;
    $this->image      = $image;
  }

  /**
   * Delegate methods to the Imagine instance
   *
   * @param  string $method
   * @param  array $arguments
   *
   * @return Image
   */
  public function __call($method, $arguments)
  {
    if (method_exists('Imagine\Gd\Image', $method)) {
      $this->salts[$method] = $arguments;
    }

    return parent::__call($method, $arguments);
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
   * Get the original size of the image
   *
   * @return Box
   */
  public function getOriginalSize()
  {
    $size = getimagesize($this->getImagePath());

    return new Box($size[0], $size[1]);
  }

  /**
   * Get the full path to the image
   *
   * @return string
   */
  public function getImagePath()
  {
    return $this->illuminage->getPublicFolder().$this->image;
  }

  /**
   * Get the Image's salts
   *
   * @return string|array
   */
  public function getSalts()
  {
    return $this->salts;
  }

  /**
   * Get the rendered thumb's path
   *
   * @return string
   */
  public function getThumb()
  {
    return $this->illuminage->process($this);
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// FILTERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Applies a Closure to the Imagine instance
   *
   * @param Closure $closure
   */
  private function onImage(Closure $closure)
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
    $this->salts['resize'] = array(
      new Box($width, $height)
    );

    return $this;
  }

  /**
   * Resize an Image, thumbnail style
   *
   * @param integer $width
   * @param integer $height
   */
  public function thumbnail($width, $height)
  {
    // Compute thumbnail ratio
    if     ($this->getOriginalSize()->getWidth()  < $width)  $ratio = $width  / $this->getOriginalSize()->getWidth();
    elseif ($this->getOriginalSize()->getHeight() < $height) $ratio = $height / $this->getOriginalSize()->getHeight();
    else   $ratio = $width / $this->getOriginalSize()->getWidth();

    // Resize image to fit bounds
    $resize = new Box($this->getOriginalSize()->getWidth(), $this->getOriginalSize()->getHeight());
    $resize = $resize->scale($ratio);
    $this->salts['resize'] = array($resize);

    // Crop image
    $this->salts['crop'] = array(
      new Point(
        max(0, round(($resize->getWidth()  - $width)  / 2)),
        max(0, round(($resize->getHeight() - $height) / 2))
      ),
      new Box($width, $height)
    );

    return $this;
  }

  /**
   * Inverts the colors
   */
  public function negative()
  {
    $this->salts['effects']['negative'] = array();

    return $this;
  }

  /**
   * Makes the image black and white
   */
  public function grayscale()
  {
    $this->salts['effects']['grayscale'] = array();

    return $this;
  }

  /**
   * Applies a Gamma correction
   *
   * @param integer $gamma The factor
   */
  public function gamma($gamma = 1)
  {
    $this->salts['effects']['gamma'] = $gamma;

    return $this;
  }

  /**
   * Colorizes the image
   *
   * @param string $color An hexadecimal value
   */
  public function colorize($color)
  {
    $this->salts['effects']['colorize'] = new Color($color);

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
