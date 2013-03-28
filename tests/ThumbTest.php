<?php
use Illuminage\Thumb;

class ThumbTest extends IlluminageTests
{
  public function testCanCreateThumb()
  {
    $this->assertInstanceOf('Illuminage\Thumb', Thumb::create('foo.jpg', 200, 300));
  }

  public function testCanCreateThumbViaStaticMethods()
  {
    $thumb = Thumb::square('foo.jpg', 200);

    $this->assertEquals('foo.jpg', $thumb->getImage());
    $this->assertEquals(200, $thumb->getSalt('width'));
    $this->assertEquals(200, $thumb->getSalt('height'));
  }
}