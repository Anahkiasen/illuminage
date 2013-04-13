<?php
namespace Illuminage;

use App;
use Exception;
use Illuminate\Cache\FileStore;
use Illuminate\Config\FileLoader;
use Illuminate\Config\Repository as Config;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Routing\UrlGenerator;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

/**
 * Handles image creation and caching
 */
class Illuminage
{

  /**
   * The IoC Container
   *
   * @var Container
   */
  protected $app;

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
  public function __construct($container = null)
  {
    $this->app = $this->createContainer($container);

    // Bind instances
    $this->config  = $this->app['config'];
    $this->cache   = $this->app['illuminage.cache'];
    $this->imagine = $this->app['imagine'];
  }

  /**
   * Create Illuminage's IoC Container
   *
   * @return Container
   */
  protected function createContainer($container = null)
  {
    $app = $container ?: new Container;
    $me  = $this;

    // System classes ---------------------------------------------- /

    $app->bindIf('Filesystem', 'Illuminate\Filesystem\Filesystem');
    $app->bindIf('FileLoader', function($app) {
      return new FileLoader($app['Filesystem'], __DIR__.'/../../');
    });

    // Core classes ------------------------------------------------ /

    $app->bindIf('config', function($app) {
      return new Repository($app['FileLoader'], 'src/config');
    });

    $app->bindIf('cache', function($app) {
      return new FileStore($app['Filesystem'], __DIR__.'/../../public');
    });

    $app->bindIf('imagine', function($app) use ($me) {
      $engine  = $me->getOption('image_engine');
      $imagine = "\Imagine\\$engine\Imagine";

      return new $imagine;
    });

    // Illuminage classes ------------------------------------------ /

    $app->bindIf('illuminage.processor', function($app) {
      return new ImageProcessor($app['imagine']);
    });

    $app->bindIf('illuminage.cache', function() use ($me) {
      return new Cache($me);
    });

    return $app;
  }

  /**
   * Get an option from the config
   *
   * @param string $option
   * @param string $fallback
   *
   * @return array|string|null
   */
  public function getOption($option, $fallback = null)
  {
    $root = class_exists('App') ? 'illuminage::' : 'config.';

    return $this->config->get($root.$option, $fallback);
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// CONSTRUCTORS /////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Create a new Image
   *
   * @param string $image Path to the image
   *
   * @return Image
   */
  public function image($image)
  {
    return new Image($this, $image);
  }

  /**
   * Create a resized image
   *
   * @param string $image
   * @param integer $width
   * @param integer $height
   *
   * @return Image
   */
  public function thumb($image, $width, $height)
  {
    $image = new Image($this, $image);
    $image->resize($width, $height);

    return $image;
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// IMAGE PROCESSING ///////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Process an Image
   *
   * @param Image $image
   *
   * @return string
   */
  public function process(Image $image)
  {
    // If the image is in cache, return it
    if ($this->cache->isCached($image)) {
      return $this->getUrlTo($image);
    }

    // Apply the various processors to the Image
    $processedImage = $this->app['illuminage.processor']->process(
      $this->getPathOf($image),
      $image->getSalt()
    );

    // Save the final processed image
    $processedImage->save($this->cache->getCachePathOf($image));

    return $this->getUrlTo($image);
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// HELPERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get the path of an image
   *
   * @param Image $image
   *
   * @return string
   */
  protected function getPathOf(Image $image)
  {
    $path = $image->getImagePath();
    if (!file_exists($path)) {
      throw new Exception('The image '.$path. ' does not exist');
    }

    return $path;
  }

  /**
   * Get the URL to an image
   *
   * @param image $image
   *
   * @return string
   */
  protected function getUrlTo(Image $image)
  {
    if (isset($this->app['url'])) {
      return $this->app['url']->asset(
        $this->getOption('cache_folder').
        $this->cache->getHashOf($image)
      );
    }

    return
      $this->getPublicFolder().
      $this->getOption('cache_folder').
      $this->cache->getHashOf($image);
  }

  /**
   * Get the path to the public folder
   *
   * @return string
   */
  public function getPublicFolder()
  {
    return isset($this->app['path.public'])
      ? $this->app['path.public'].'/'
      : './';
  }

  /**
   * Get the cache folder
   *
   * @return string
   */
  public function getCacheFolder()
  {
    return $this->getPublicFolder().$this->getOption('cache_folder');
  }

}
