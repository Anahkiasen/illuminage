<?php
use Illuminate\Container\Container;
use Illuminage\Facades\Illuminage;

abstract class IlluminageTests extends PHPUnit_Framework_TestCase
{
  protected $hash = '4169d8194bc52a4134b8bf365bc4f502.jpg';

  public static function setUpBeforeClass()
  {
    $app = new Container;
    $app->bind('illuminage', 'Illuminage\Illuminage');

    Illuminage::setFacadeApplication($app);
  }

  public function setUp()
  {
    $this->image = Illuminage::image('foo.jpg');
    $this->thumb = Illuminage::thumb('foo.jpg', 100, 100);
  }

  public function tearDown()
  {
    $testThumb = __DIR__.'/public/'.$this->hash;
    if (file_exists($testThumb)) unlink($testThumb);
  }
}