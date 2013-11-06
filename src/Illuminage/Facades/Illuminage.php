<?php
namespace Illuminage\Facades;

use Illuminate\Support\Facades\Facade;
use Illuminage\IlluminageServiceProvider;

/**
 * Static facade for Illuminage
 */
class Illuminage extends Facade
{
  /**
   * Retrieve Illuminage from the Container
   *
   * @return string
   */
  public static function getFacadeAccessor()
  {
    if (!static::$app) {
      static::$app = IlluminageServiceProvider::make();
    }

    return 'illuminage';
  }
}
