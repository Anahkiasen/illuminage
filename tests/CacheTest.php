<?php

class CacheTest extends IlluminageTests
{
	public function testCanComputeHashOfThumb()
	{
		$this->assertEquals($this->hash, $this->app['illuminage.cache']->getHashOf($this->thumb));
	}

	public function testCanComputeCorrectExtension()
	{
		$thumb = $this->app['illuminage']->thumb('bar.png', 100, 100);

		$this->assertEquals('ffe7af188f778cc3af4faf17b13a3409.png', $this->app['illuminage.cache']->getHashOf($thumb));
	}

	public function testCanGetPathToCache()
	{
		$this->assertEquals('tests/public/'.$this->hash, $this->app['illuminage.cache']->getCachePathOf($this->thumb));
	}

	public function testCanCheckIfAThumbIsCached()
	{
		$path = $this->app['illuminage.cache']->getCachePathOf($this->thumb);

		$this->assertFalse($this->app['illuminage.cache']->isCached($this->thumb));
		file_put_contents($path, 'foo');
		$this->assertTrue($this->app['illuminage.cache']->isCached($this->thumb));

		unlink($path);
	}
}
