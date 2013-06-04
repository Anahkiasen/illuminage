<?php
class ImageTest extends IlluminageTests
{
  public function testCanGetFullPathToOriginalImage()
  {
    $this->assertEquals('tests/public/foo.jpg', $this->thumb->getOriginalImagePath());
  }

  public function testCanGetFullPathToProcessedImage()
  {
    $this->assertEquals('http://:/a9434d21b592a5167dd2a3527f34d73d.jpg', $this->thumb->getPath());
  }

  public function testCanGetRelativePathToProcessedImage()
  {
    $this->assertEquals('a9434d21b592a5167dd2a3527f34d73d.jpg', $this->thumb->getRelativePath());
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
    $this->assertEquals('http://:/a8478c716dbf27bf7c401db9b6d75380.jpg', $this->image->resize(200, 250)->getPath());
  }

  public function testCanApplyFilters()
  {
    $image = $this->image->resize(300, 300)->grayscale();

    $this->assertEquals('http://:/cf0ea92eb1e8bcee543e26f7cc2eb6ff.jpg', $image->getPath());
  }
}