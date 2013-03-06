<?php
namespace Illuminage;

use App;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Illuminate\Routing\UrlGenerator;

class Illuminage
{
  /**
   * The UrlGenerator instance
   *
   * @var UrlGenerator
   */
  protected $url;

  /**
   * The Imagine instance
   *
   * @var Imagine
   */
  protected $imagine;

  /**
   * The Cache instance
   *
   * @var Cache
   */
  protected $cache;

  /**
   * Set up Illuminage
   *
   * @param Cache        $cache
   * @param UrlGenerator $url
   * @param Imagine      $imagine
   */
  public function __construct(Cache $cache, UrlGenerator $url, $imagine)
  {
    $this->cache   = $cache;
    $this->url     = $url;
    $this->imagine = $imagine;
  }

  /**
   * Generate a Thumb
   *
   * @param Thumb $thumb
   *
   * @return string Path to the generated image
   */
  public function createThumb(Thumb $thumb)
  {
    // If the image is in cache, return it
    if ($this->cache->isCached($thumb)) {
      return $this->url->asset($this->cache->getHashOf($thumb));
    }

    // Setup Imagine
    $mode  = ImageInterface::THUMBNAIL_OUTBOUND;
    $box   = new Box($thumb->getWidth(), $thumb->getHeight());

    $path = $thumb->getImagePath();
    if (!file_exists($path)) return false;

    // Generate the thumbnail
    $this->imagine
      ->open($path)
      ->thumbnail($box, $mode)
      ->save($this->cache->getCachePathOf($thumb));

    return $this->url->asset($this->cache->getHashOf($thumb));
  }
}