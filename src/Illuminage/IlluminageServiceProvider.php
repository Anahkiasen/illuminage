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
   * Register the service provider.
   *
   * @return void
   */
  public function register()
  {
    // Register config file
    $this->app['config']->package('anahkiasen/illuminage', __DIR__.'/../config');

    $this->app->bind('illuminage', function($app) {
      return new Illuminage($app);
    });
  }

  /**
   * Bootstrap the application events.
   *
   * @return void
   */
  public function boot()
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
