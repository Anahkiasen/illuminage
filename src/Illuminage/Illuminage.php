<?php
namespace Illuminage;

use App;
use Exception;
use Illuminate\Config\Repository as Config;
use Illuminate\Routing\UrlGenerator;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

/**
 * Handles image creation and caching
 */
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
   * The Config instance
   *
   * @var Config
   */
  protected $config;

  /**
   * Set up Illuminage
   *
   * @param Cache        $cache
   * @param UrlGenerator $url
   * @param Imagine      $imagine
   */
  public function __construct(Config $config, Cache $cache, UrlGenerator $url, $imagine)
  {
    $this->config  = $config;
    $this->cache   = $cache;
    $this->imagine = $imagine;
    $this->url     = $url;
  }

  /**
   * Generate a Thumb
   *
   * @param Thumb $thumb
   *
   * @return Imagine
   */
  public function createThumb(Thumb $thumb)
  {
    // Setup Imagine
    $mode  = ImageInterface::THUMBNAIL_OUTBOUND;
    $box   = new Box($thumb->getWidth(), $thumb->getHeight());

    $path = $thumb->getImagePath();
    if (!file_exists($path)) {
      throw new Exception('The image '.$path. ' does not exist');
    }

    // Generate the Imagine instance
    return $this->imagine
      ->open($path)
      ->thumbnail($box, $mode);
  }

  /**
   * Renders the final thumb
   *
   * @param Thumb $thumb
   *
   * @return string Path to the generated image
   */
  public function renderThumb(Thumb $thumb)
  {
    // If the image is in cache, return it
    if ($this->cache->isCached($thumb)) {
      return $this->getUrlTo($thumb);
    }

    // Save the thumb
    $thumb->getImagine()
      ->save($this->cache->getCachePathOf($thumb));

    return $this->getUrlTo($thumb);
  }

  /**
   * Get the URL to an image
   *
   * @param Thumb $thumb
   *
   * @return string
   */
  public function getUrlTo(Thumb $thumb)
  {
    $cache = $this->config->get('illuminage::cache_folder');

    return $this->url->asset($cache.$this->cache->getHashOf($thumb));
  }

  /**
   * Get the cache folder
   *
   * @return string
   */
  public function getCacheFolder()
  {
    return App::make('path.public').'/'.$this->config->get('illuminage::cache_folder');
  }
}
