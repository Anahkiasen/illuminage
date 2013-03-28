<?php
use Illuminage\Image;

class ImageTest extends IlluminageTests
{
  public function testCanAccessSalts()
  {
    $this->assertEquals('foo.jpg', $this->thumb->getImage());
    $this->assertEquals(100, $this->thumb->getSalt('width'));
    $this->assertEquals(100, $this->thumb->getSalt('height'));
  }

  public function testCanGetFullPathToImage()
  {
    $this->assertEquals(__DIR__.'/public/foo.jpg', $this->thumb->getImagePath());
  }

  public function testCanRenderThumb()
  {
    $this->assertEquals('<img src="http://test/public/' .$this->hash. '">', $this->thumb->render());

    unlink($this->cache->getCachePathOf($this->thumb));
  }

  public function testCanRenderThumbOnStringHint()
  {
    $this->assertEquals('<img src="http://test/public/' .$this->hash. '">', (string) $this->thumb);
  }

  public function testThumbsCorrectlyExtendTag()
  {
    $this->thumb->addClass('foo');

    $this->assertEquals('<img class="foo" src="http://test/public/' .$this->hash. '">', (string) $this->thumb);
  }

  public function testCanResizeOnTheFly()
  {
    $image = $this->image->resize(200, 250)->render();

    $this->assertEquals('73758343ac4ba018cbeaa75b500ff73c', md5_file(__DIR__.'/public/9aafd0ab864209781d3af94758cd8935.jpg'));
  }

  public function testCanApplyFilters()
  {
    $image = $this->image->resize(300, 300)->grayscale()->render();

    $this->assertEquals('a1e78fdd1ad6f9dad14423cd90748f4c', md5_file(__DIR__.'/public/9edc0916aeae4ae44b64be7058328c0d.jpg'));
  }
}