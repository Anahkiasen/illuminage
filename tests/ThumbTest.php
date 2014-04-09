<?php
namespace Illuminage;

class ThumbTest extends IlluminageTestCase
{
	public function testCanCreateThumb()
	{
		$this->assertInstanceOf('Illuminage\Image', $this->app['illuminage']->thumb('foo.jpg', 200, 300));
	}
}