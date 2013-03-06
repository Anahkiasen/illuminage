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

  public function tearDown()
  {
    $testThumb = __DIR__.'/public/d49d9f3c769ee076be9ef21ec23f57d6.jpg';
    if (file_exists($testThumb)) unlink($testThumb);
  }

  protected static function getIlluminage()
  {
    $url = Mockery::mock('Illuminate\Routing\UrlGenerator', function($mock) {
      $mock->shouldReceive('asset')->andReturnUsing(function($image) {
        return 'http://test/public/'.$image;
      });
    });

    $config = Mockery::mock('Illuminate\Config\Repository', function($mock) {
      $mock->shouldReceive('get')->with('illuminage::cache_folder')->andReturn('');
    });

    $cache = new Illuminage\Cache;
    $imagine = new Imagine\Gd\Imagine;

    return new Illuminage\Illuminage($config, $cache, $url, $imagine);
  }
}