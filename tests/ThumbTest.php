<?php

class ThumbTest extends IlluminageTests
{
	public function testCanCreateThumb()
	{
		$this->assertInstanceOf('Illuminage\Image', $this->app['illuminage']->thumb('foo.jpg', 200, 300));
	}
}