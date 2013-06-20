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
    $this->assertEquals('http://:/5773f334688323970fe6c084f70557a8.jpg', $this->image->resize(200, 250)->getPath());
    $this->unlink('5773f334688323970fe6c084f70557a8.jpg');
  }

  public function testCanApplyFilters()
  {
    $image = $this->image->resize(300, 300)->grayscale();

    $this->assertEquals('http://:/d3bc6aa8ca3bf86754e98b34b10ea18f.jpg', $image->getPath());
    $this->unlink('d3bc6aa8ca3bf86754e98b34b10ea18f.jpg');
  }
}
