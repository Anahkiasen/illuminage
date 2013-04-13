<?php
namespace Illuminage;

use Imagine\Image\ImagineInterface;

/**
 * Take an image, and apply an array of Processors to it
 */
class ImageProcessor
{

  /**
   * Build a new ImageProcessor
   *
   * @param Imagine $imagine
   * @param array   $processors An array of Processors
   */
  public function __construct(ImagineInterface $imagine)
  {
    $this->imagine = $imagine;
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// CORE METHODS /////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Process an Image
   *
   * @param string $image      Path to the image
   * @param array  $processors An array of processors
   *
   * @return ImagineInterface
   */
  public function process($image, array $processors)
  {
    $image = $this->imagine->open($image);

    // Apply each method one after the other
    foreach ($processors as $method => $arguments) {
      if (empty($arguments) or isset($arguments[0])) {
        $image = $this->executeMethod($image, $method, $arguments);
      } else {
        foreach ($arguments as $submethod => $arguments) {
          $this->executeSubmethod($image, $method, $submethod, $arguments);
        }
      }
    }

    return $image;
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////////////// HELPERS ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Execute a method()->submethod()
   *
   * @param Imagine $imagine
   * @param string  $method
   * @param string  $submethod
   * @param array   $arguments
   */
  protected function executeSubmethod(&$imagine, $method, $submethod, $arguments)
  {
    switch (sizeof($arguments)) {
      case 0:
        return $imagine->$method()->$submethod();
      case 1:
        return $imagine->$method()->$submethod($arguments[0]);
      case 2:
        return $imagine->$method()->$submethod($arguments[0], $arguments[1]);
    }
  }

  /**
   * Execute a method()
   *
   * @param Imagine $imagine
   * @param string  $method
   * @param array   $arguments
   */
  protected function executeMethod(&$imagine, $method, $arguments)
  {
    switch (sizeof($arguments)) {
      case 0:
        return $imagine->$method();
      case 1:
        return $imagine->$method($arguments[0]);
      case 2:
        return $imagine->$method($arguments[0], $arguments[1]);
    }
  }

}
