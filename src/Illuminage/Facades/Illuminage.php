<?php
namespace Illuminage\Facades;

use Illuminate\Support\Facades\Facade;

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
  	return 'illuminage';
  }

}
