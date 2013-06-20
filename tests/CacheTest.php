<?php
include '_start.php';

use Illuminage\Facades\Illuminage;

class CacheTest extends IlluminageTests
{

  public function testCanComputeHashOfThumb()
  {
    $this->assertEquals($this->hash, $this->cache->getHashOf($this->thumb));
  }

  public function testCanComputeCorrectExtension()
  {
    $thumb = Illuminage::thumb('bar.png', 100, 100);

    $this->assertEquals('f3238bdc5038ef68c68528dde8ca91b9.png', $this->cache->getHashOf($thumb));
  }

  public function testCanGetPathToCache()
  {
    $this->assertEquals('tests/public/'.$this->hash, $this->cache->getCachePathOf($this->thumb));
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
