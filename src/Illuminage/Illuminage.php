<?php
namespace Illuminage;

use Illuminate\Container\Container;

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
	 * Set up Illuminage
	 *
	 * @param Container $container A base Container to bind onto
	 */
	public function __construct(Container $app)
	{
		$this->app = $app;
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
		return $this->app['config']->get('illuminage::'.$option, $fallback);
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
		return new Image($this->app, $image);
	}

	/**
	 * Resize an image
	 *
	 * @param string  $image
	 * @param integer $width
	 * @param integer $height
	 *
	 * @return Image
	 */
	public function resize($image, $width, $height = null)
	{
		// Fallback size for height
		if (!$height) {
			$height = $width;
		}

		$image = new Image($this->app, $image);
		$image->resize($width, $height);

		return $image;
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
	public function thumb($image, $width, $height = null)
	{
		// Fallback size for height
		if (!$height) {
			$height = $width;
		}

		$image = new Image($this->app, $image);
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
		$endpoint = $this->app['illuminage.cache']->getCachePathOf($image);

		// If the image hasn't yet been processed, do it
		// and then cache it
		if (!$this->app['illuminage.cache']->isCached($image)) {
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
			$this->app['illuminage.cache']->getHashOf($image));
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
