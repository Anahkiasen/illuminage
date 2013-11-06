<?php
include __DIR__.'/../vendor/autoload.php';

abstract class IlluminageTests extends PHPUnit_Framework_TestCase
{
  /**
   * The test image hash
   *
   * @var string
   */
  protected $hash = '18e0bfde0662e1cabf3a2beeb79db265.jpg';

  /**
   * The Container
   *
   * @var Container
   */
  protected $app;

  /**
   * Setup the tests
   */
  public function setUp()
  {
    $this->app = Illuminage\IlluminageServiceProvider::make();
    $this->app['path.public'] = 'tests/public';

    // Bind mocked config
    $this->app->bind('config', function() {
      return Mockery::mock('config', function($mock) {
        $mock->shouldReceive('get')->with('illuminage::image_engine', '')->andReturn('Gd');
        $mock->shouldReceive('get')->with('illuminage::quality', '')->andReturn(75);
        $mock->shouldReceive('get')->with('illuminage::cache_folder', '')->andReturn('');
      });
    });

    // Create some dummy instances
    $this->image = $this->app['illuminage']->image('foo.jpg');
    $this->thumb = $this->app['illuminage']->thumb('foo.jpg', 100, 100);
  }

  /**
   * Cleanup remaining images
   *
   * @return [type] [description]
   */
  public function tearDown()
  {
    $this->unlink($this->hash);
  }

  /**
   * Remove an image safely
   *
   * @param string $file
   *
   * @return void
   */
  protected function unlink($file)
  {
    $path = __DIR__.'/public/'.$file;
    if (file_exists($path)) {
      unlink($path);
    }
  }
}
