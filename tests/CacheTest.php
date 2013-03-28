<?php
include '_start.php';

use Illuminage\Thumb;

class CacheTest extends IlluminageTests
{

  public function testCanComputeHashOfThumb()
  {
    $this->assertEquals($this->hash, $this->cache->getHashOf($this->thumb));
  }

  public function testSameImageWithDifferentNameGetsSameHash()
  {
    $thumb = new Thumb('foocopy.jpg', 100, 100);

    $this->assertEquals($this->hash, $this->cache->getHashOf($thumb));
  }

  public function testCanComputeCorrectExtension()
  {
    $thumb = new Thumb('bar.png', 100, 100);

    $this->assertEquals('d87c9ff7c72ffe49b2f8dec952f4f3b8.png', $this->cache->getHashOf($thumb));
  }

  public function testCanGetPathToCache()
  {
    $this->assertEquals(__DIR__.'/public/'.$this->hash, $this->cache->getCachePathOf($this->thumb));
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