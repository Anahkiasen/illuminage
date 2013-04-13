<?php
use Illuminage\Facades\Illuminage;

class ThumbTest extends IlluminageTests
{
  public function testCanCreateThumb()
  {
    $this->assertInstanceOf('Illuminage\Image', Illuminage::thumb('foo.jpg', 200, 300));
  }
}