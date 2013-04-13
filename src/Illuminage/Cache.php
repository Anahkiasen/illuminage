<?php
namespace Illuminage;

/**
 * Handles caching and fetching of images
 */
class Cache
{

  /**
   * The Illuminage instance
   *
   * @var Illuminage
   */
  protected $illuminage;

  /**
   * Create an Illuminage Cache instance
   *
   * @param Illuminage $illuminage
   */
  public function __construct(Illuminage $illuminage)
  {
    $this->illuminage = $illuminage;
  }

  /**
   * Get the cache hash of an image
   *
   * @param Image $image
   *
   * @return string
   */
  public function getHashOf(Image $image)
  {
    $imagePath = $image->getImagePath();

    // Build the salt array
    $salts   = $image->getSalt();
    $salts[] = md5_file($imagePath);
    $salts   = json_encode($salts);

    // Get image extension
    $extension = pathinfo($imagePath, PATHINFO_EXTENSION);

    return md5($salts).'.'.$extension;
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
    return $this->illuminage->getCacheFolder().$this->getHashOf($image);
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
