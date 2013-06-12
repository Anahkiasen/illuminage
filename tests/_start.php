<?php
use Illuminage\Facades\Illuminage;
use Illuminate\Container\Container;

abstract class IlluminageTests extends PHPUnit_Framework_TestCase
{

  /**
   * The test image hash
   *
   * @var string
   */
  protected $hash = '71f1bf02c46456f0bdacfef8d55f2cfd.jpg';

  /**
   * The IoC Container
   *
   * @var Container
   */
  protected static $app;

  public static function setUpBeforeClass()
  {
    $app = new Container;

    $app->bind('config', function() {
      return Mockery::mock('config', function($mock) {
        $mock->shouldReceive('get')->with('config.image_engine', '')->andReturn('Gd');
        $mock->shouldReceive('get')->with('config.cache_folder', '')->andReturn('');
      });
    });

    $app['path.public'] = 'tests/public';

    $app->bind('illuminage', function($app) {
      return new \Illuminage\Illuminage($app);
    });

    Illuminage::setFacadeApplication($app);
  }

  public function setUp()
  {
    $this->cache = new \Illuminage\Cache(Illuminage::getFacadeRoot());
    $this->image = Illuminage::image('foo.jpg');
    $this->thumb = Illuminage::thumb('foo.jpg', 100, 100);
  }

  public function tearDown()
  {
    $this->unlink($this->hash);
  }

  protected function unlink($file)
  {
    $path = __DIR__.'/public/'.$file;
    if (file_exists($path)) {
      unlink($path);
    }
  }
}
