<?php
namespace Illuminage;

use Closure;
use HtmlObject\Traits\Tag;
use Imagine\Image\Box;
use Imagine\Image\Color;
use Imagine\Image\Point;

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
   * The final image's quality
   *
   * @var integer
   */
  protected $quality;

  /**
   * Path to the image
   *
   * @var SplFileImage
   */
  protected $image;

  /**
   * The Processed image
   *
   * @var SplFileImage
   */
  protected $processedImage;

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
    $this->image      = new SplFileImage($this->illuminage->getPublicFolder().$image);
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

  // Salts --------------------------------------------------------- /

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
   * Get the processed image's quality
   *
   * @return integer
   */
  public function getQuality()
  {
    return $this->quality;
  }

  /**
   * Get the salts with the various processed properties
   *
   * @return array
   */
  public function getProcessedSalts()
  {
    $salts = $this->salts;
    $salts = $this->processThumbnail($salts);

    return $salts;
  }

  // Original image informations ----------------------------------- /

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
    $size = $this->image->getDimensions();

    return new Box($size[0], $size[1]);
  }

  /**
   * Get the full path to the image
   *
   * @return string
   */
  public function getOriginalImagePath()
  {
    return $this->image->getPathname();
  }

  // Processed image informations ---------------------------------- /

  /**
   * Get the rendered thumb's path
   *
   * @deprecated This is replaced by ->getPath()
   * @return string
   */
  public function getThumb()
  {
    $this->getProcessedImage();

    return $this->illuminage->getUrlTo($this);
  }

  /**
   * Get the Image's path
   *
   * @return string
   */
  public function getPath()
  {
    return $this->getThumb();
  }

  /**
   * Get the processer Image's relative path
   *
   * @return string
   */
  public function getRelativePath()
  {
    $path = $this->getPath();

    return str_replace($this->illuminage->request->root().'/', null, $path);
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// FILTERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  // Live filters -------------------------------------------------- /

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
    $this->salts['_thumbnail'] = new Box($width, $height);

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

  /**
   * Change the processed image's quality
   *
   * @param  integer $quality
   */
  public function quality($quality)
  {
    $this->quality = $quality;

    return $this;
  }

  // Processed filters --------------------------------------------- /

  /**
   * Process the thumbnail salt
   *
   * @param  array  $salts        An array of salts
   *
   * @return array
   */
  protected function processThumbnail(array $salts)
  {
    if (isset($salts['_thumbnail'])) {
      $box = $salts['_thumbnail'];
      unset($salts['_thumbnail']);

      // Compute thumbnail ratio
      $ratios = array(
        $box->getWidth()  / $this->getOriginalSize()->getWidth(),
        $box->getHeight() / $this->getOriginalSize()->getHeight()
      );
      if     ($this->getOriginalSize()->getWidth()  < $box->getWidth())  $ratio = $ratios[0];
      elseif ($this->getOriginalSize()->getHeight() < $box->getHeight()) $ratio = $ratios[1];
      else   $ratio = max($ratios);

      // Resize this to fit bounds
      $resize = $this->getOriginalSize()->scale($ratio);
      $salts['resize'] = array($resize);

      // Crop image
      $salts['crop'] = array(
        new Point(
          max(0, round(($resize->getWidth()  - $box->getWidth())  / 2)),
          max(0, round(($resize->getHeight() - $box->getHeight()) / 2))
        ),
        $box
      );
    }

    return $salts;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// OUTPUT //////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Process the original image and returns the SplFileImage
   *
   * @return string
   */
  protected function getProcessedImage()
  {
    if (is_null($this->processedImage)) {
      $image = $this->illuminage->process($this);
      $this->processedImage = new SplFileImage($image);
    }

    return $this->processedImage;
  }

  /**
   * Renders the image
   *
   * @return string
   */
  public function injectProperties()
  {
    return array(
      'src' => $this->getPath()
    );
  }

}
