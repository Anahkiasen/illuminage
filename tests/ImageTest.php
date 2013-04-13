<?php
class ImageTest extends IlluminageTests
{
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
    $this->assertEquals('http://test/public/9aafd0ab864209781d3af94758cd8935.jpg', $this->image->resize(200, 250)->getThumb());
  }

  public function testCanApplyFilters()
  {
    $image = $this->image->resize(300, 300)->grayscale();

    $this->assertEquals('http://test/public/9edc0916aeae4ae44b64be7058328c0d.jpg', $image->getThumb());
  }
}