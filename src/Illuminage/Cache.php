<?php
namespace Illuminage;

use App;

/**
 * Handles caching and fetching of images
 */
class Cache
{

  /**
   * Get the cache hash of an image
   *
   * @param Thumb $thumb
   *
   * @return string
   */
  public function getHashOf(Thumb $thumb)
  {
    $filehash  = md5_file($thumb->getImagePath());
    $extension = pathinfo($thumb->getImagePath(), PATHINFO_EXTENSION);

    return md5($filehash.$thumb->getWidth().'x'.$thumb->getHeight()).'.'.$extension;
  }

  /**
   * Get the path where an image will be cached
   *
   * @param Thumb $thumb
   *
   * @return string
   */
  public function getCachePathOf(Thumb $thumb)
  {
    return App::make('illuminage')->getCacheFolder().$this->getHashOf($thumb);
  }

  /**
   * Check if an image is in cache
   *
   * @param Thumb $thumb
   *
   * @return boolean
   */
  public function isCached(Thumb $thumb)
  {
    return file_exists($this->getCachePathOf($thumb));
  }

}
