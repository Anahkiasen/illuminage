<?php
namespace Illuminage;

use Illuminate\Support\ServiceProvider;

class IlluminageServiceProvider extends ServiceProvider
{
  /**
   * Indicates if loading of the provider is deferred.
   *
   * @var bool
   */
  protected $defer = false;

  /**
   * Bootstrap the application events.
   *
   * @return void
   */
  public function boot()
  {
    // Register config file
    $this->app['config']->package('anahkiasen/illuminage', __DIR__.'/../config');

    $this->app->bind('illuminage.cache', 'Illuminage\Cache');

    $this->app->bind('illuminage', function($app) {
      $engine = $app['config']->get('illuminage::image_engine');
      $imagine = '\Imagine\\'.$engine.'\Imagine';

      return new Illuminage($app['config'], $app['illuminage.cache'], $app['url'], new $imagine);
    });
  }

  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register()
  {
    //
  }

  /**
   * Get the services provided by the provider.
   *
   * @return array
   */
  public function provides()
  {
    return array();
  }
}
