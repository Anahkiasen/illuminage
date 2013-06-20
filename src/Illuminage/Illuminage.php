<?php
namespace Illuminage;

use Illuminate\Cache\FileStore;
use Illuminate\Config\FileLoader;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Symfony\Component\Routing\RouteCollection;

/**
 * Handles image creation and caching
 *
 * @property request $request
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
   * @param Container $container A base Container to bind onto
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
   * Get an instance from the Container
   *
   * @param  string $key
   *
   * @return object
   */
  public function __get($key)
  {
    return $this->app->make($key);
  }

  /**
   * Create Illuminage's IoC Container
   *
   * @param Container $container A base Container to bind onto
   *
   * @return Container
   */
  protected function createContainer($container = null)
  {
    $app = $container ?: new Container;
    $me  = $this;

    // System classes ---------------------------------------------- /

    $app->bindIf('Filesystem', 'Illuminate\Filesystem\Filesystem');

    $app->bindIf('request', function() {
      return Request::createFromGlobals();
    });

    // Core classes ------------------------------------------------ /

    $app->bindIf('config', function($app) {
      $fileloader = new FileLoader($app['Filesystem'], __DIR__.'/../../');

      return new Repository($fileloader, 'src/config');
    });

    $app->bindIf('cache', function($app) {
      return new FileStore($app['Filesystem'], __DIR__.'/../../public');
    });

    $app->bindIf('url', function($app) {
      $routeCollection = new RouteCollection;

      return new UrlGenerator($routeCollection, $app['request']);
    });

    // Illuminage classes ------------------------------------------ /

    $app->bindIf('imagine', function($app) use ($me) {
      $engine  = $me->getOption('image_engine');
      $imagine = "\Imagine\\$engine\Imagine";

      return new $imagine;
    });

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
    $image->thumbnail($width, $height);

    return $image;
  }

  /**
   * Create a thumb image
   *
   * @param  string $image
   * @param  integer $size
   *
   * @return Image
   */
  public function square($image, $size)
  {
    return $this->thumb($image, $size, $size);
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
    $endpoint = $this->cache->getCachePathOf($image);

    // If the image hasn't yet been processed, do it
    // and then cache it
    if (!$this->cache->isCached($image)) {
      $quality = $image->getQuality() ?: $this->getOption('quality');
      $processedImage = $this->app['illuminage.processor']->process($image);
      $processedImage->save($endpoint, array('quality' => (int) $quality));
    }

    return $endpoint;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// HELPERS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get the URL to an image
   *
   * @param Image $image
   *
   * @return string
   */
  public function getUrlTo(Image $image)
  {
    return $this->app['url']->asset(
      $this->getOption('cache_folder').
      $this->cache->getHashOf($image));
  }

  /**
   * Get the path to the public folder
   *
   * @return string
   */
  public function getPublicFolder()
  {
    return $this->app['path.public']
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
