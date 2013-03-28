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
   * @param Image $image
   *
   * @return string
   */
  public function getHashOf(Image $image)
  {
    // Implode the salts
    $filehash[] = md5_file($image->getImagePath());
    foreach ($image->getSalt() as $name => $salt) {
      $filehash[] = $name.'-'.$salt;
    }

    // Get string hash and extension
    $filehash  = implode('-', $filehash);
    $extension = pathinfo($image->getImagePath(), PATHINFO_EXTENSION);

    return md5($filehash).'.'.$extension;
  }

  /**
   * Get the path where an image will be cached
   *
   * @param Image $image
   *
   * @return string
   */
  public function getCachePathOf(Image $image)
  {
    return App::make('illuminage')->getCacheFolder().$this->getHashOf($image);
  }

  /**
   * Check if an image is in cache
   *
   * @param Image $image
   *
   * @return boolean
   */
  public function isCached(Image $image)
  {
    return file_exists($this->getCachePathOf($image));
  }

}
