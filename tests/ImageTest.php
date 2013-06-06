<?php
class ImageTest extends IlluminageTests
{
  public function testCanGetFullPathToOriginalImage()
  {
    $this->assertEquals('tests/public/foo.jpg', $this->thumb->getOriginalImagePath());
  }

  public function testCanGetFullPathToProcessedImage()
  {
    $this->assertEquals('http://:/'.$this->hash, $this->thumb->getPath());
  }

  public function testCanGetRelativePathToProcessedImage()
  {
    $this->assertEquals($this->hash, $this->thumb->getRelativePath());
  }

  public function testCanRenderThumb()
  {
    $this->assertEquals('<img src="http://:/' .$this->hash. '">', $this->thumb->render());

    unlink($this->cache->getCachePathOf($this->thumb));
  }

  public function testCanRenderThumbOnStringHint()
  {
    $this->assertEquals('<img src="http://:/' .$this->hash. '">', $this->thumb->render());
  }

  public function testThumbsCorrectlyExtendTag()
  {
    $this->thumb->addClass('foo');

    $this->assertEquals('<img class="foo" src="http://:/' .$this->hash. '">', $this->thumb->render());
  }

  public function testCanResizeOnTheFly()
  {
    $this->assertEquals('http://:/a26155986d37968d8f5e0387c9515d6e.jpg', $this->image->resize(200, 250)->getPath());
  }

  public function testCanApplyFilters()
  {
    $image = $this->image->resize(300, 300)->grayscale();

    $this->assertEquals('http://:/e7341bc6ea7a50521508b97591e97c57.jpg', $image->getPath());
  }
}