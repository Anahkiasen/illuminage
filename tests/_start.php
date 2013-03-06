<?php
use Illuminage\Cache;
use Illuminage\Thumb;

abstract class IlluminageTests extends PHPUnit_Framework_TestCase
{
  public static function setUpBeforeClass()
  {
    $illuminage = static::getIlluminage();

    if (!class_exists('App')) Mockery::mock('alias:App', function($mock) use ($illuminage) {
      $mock->shouldReceive('make')->with('path.public')->andReturn(__DIR__.'/public');
      $mock->shouldReceive('make')->with('illuminage')->andReturn($illuminage);
    });
  }

  public function setUp()
  {
    $this->cache = new Cache;
    $this->thumb = new Thumb('foo.jpg', 100, 100);
  }

  protected static function getIlluminage()
  {
    $url = Mockery::mock('Illuminate\Routing\UrlGenerator', function($mock) {
      $mock->shouldReceive('asset')->andReturnUsing(function($image) {
        return 'http://test/public/'.$image;
      });
    });
    $cache = new Illuminage\Cache;
    $imagine = new Imagine\Gd\Imagine;

    return new Illuminage\Illuminage($cache, $url, $imagine);
  }
}