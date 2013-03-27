<?php
include '_start.php';

use Illuminage\Thumb;

class CacheTest extends IlluminageTests
{
  public function testCanComputeHashOfThumb()
  {
    $this->assertEquals('1734d4ae7f724051dfec8b2796410edb.jpg', $this->cache->getHashOf($this->thumb));
  }

  public function testSameImageWithDifferentNameGetsSameHash()
  {
    $thumb = new Thumb('foocopy.jpg', 100, 100);

    $this->assertEquals('1734d4ae7f724051dfec8b2796410edb.jpg', $this->cache->getHashOf($thumb));
  }

  public function testCanComputeCorrectExtension()
  {
    $thumb = new Thumb('bar.png', 100, 100);

    $this->assertEquals('a9513750729d8cbd72bdfa624b7863e2.png', $this->cache->getHashOf($thumb));
  }

  public function testCanGetPathToCache()
  {
    $this->assertEquals(__DIR__.'/public/1734d4ae7f724051dfec8b2796410edb.jpg', $this->cache->getCachePathOf($this->thumb));
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