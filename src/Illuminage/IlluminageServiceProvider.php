<?php
namespace Illuminage;

use Illuminate\Cache\FileStore;
use Illuminate\Config\FileLoader;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\RouteCollection;

/**
 * Service Provider for Laravel 4
 */
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
		$this->app = static::make($this->app);
	}

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		// ...
	}

	////////////////////////////////////////////////////////////////////
	/////////////////////////// CLASS BINDINGS /////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Make a Rocketeer container
	 *
	 * @return Container
	 */
	public static function make($app = null)
	{
		if (!$app) {
			$app = new Container;
		}

		// Bind classes
		$serviceProvider = new static($app);
		$app = $serviceProvider->bindCoreClasses($app);
		$app = $serviceProvider->bindClasses($app);

		return $app;
	}

	/**
	 * Bind the core classes
	 *
	 * @param  Container $app
	 *
	 * @return Container
	 */
	public function bindCoreClasses(Container $app)
	{
		if ($app->bound('events')) {
			return $app;
		}

		$app->bindIf('files', 'Illuminate\Filesystem\Filesystem');

		$app->bindIf('request', function() {
			return Request::createFromGlobals();
		});

		$app->bindIf('config', function($app) {
			$fileloader = new FileLoader($app['files'], __DIR__.'/../config');

			return new Repository($fileloader, 'config');
		}, true);

		$app->bindIf('cache', function($app) {
			return new FileStore($app['Filesystem'], __DIR__.'/../../public');
		});

		$app->bindIf('url', function($app) {
			$routeCollection = new RouteCollection;

			return new UrlGenerator($routeCollection, $app['request']);
		});

		// Register config file
		$app['config']->package('anahkiasen/illuminage', __DIR__.'/../config');

		return $app;
	}

	/**
	 * Bind Illuminage's classes
	 *
	 * @param Container $app
	 *
	 * @return Container
	 */
	public function bindClasses(Container $app)
	{
		$app->bindIf('illuminage', function($app) {
			return new Illuminage($app);
		});

		$app->bindIf('imagine', function($app) {
			$engine  = $app['illuminage']->getOption('image_engine');
			$imagine = "\Imagine\\$engine\Imagine";

			return new $imagine;
		});

		$app->bindIf('illuminage.processor', function($app) {
			return new ImageProcessor($app['imagine']);
		});

		$app->bindIf('illuminage.cache', function($app) {
			return new Cache($app['illuminage']);
		});

		// Set cache folder if non existing
		$cache = $app['illuminage']->getPublicFolder().'/'.$app['config']->get('illuminage::cache_folder');
		if (!file_exists($cache)) {
			$app['config']->set('illuminage::cache_folder', 'public/');
		}

		return $app;
	}
}
