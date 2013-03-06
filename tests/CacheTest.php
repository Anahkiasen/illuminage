<?php
include '_start.php';

class CacheTest extends IlluminageTests
{
  public function testCanComputeHashOfThumb()
  {
    $this->assertEquals('d49d9f3c769ee076be9ef21ec23f57d6.jpg', $this->cache->getHashOf($this->thumb));
  }

  public function testCanGetPathToCache()
  {
    $this->assertEquals(__DIR__.'/public/d49d9f3c769ee076be9ef21ec23f57d6.jpg', $this->cache->getCachePathOf($this->thumb));
  }

  public function testCanCheckIfAThumbIsCached()
  {
    $path = $this->cache->getCachePathOf($this->thumb);

    $this->assertFalse($this->cache->isCached($this->thumb));
    file_put_contents($path, 'foo');
    $this->assertTrue($this->cache->isCached($this->thumb));

    unlink($path);
  }
}