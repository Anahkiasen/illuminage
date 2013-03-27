Illuminage
==========

Illuminage is a wrapper for the Imagine library to hook into the Laravel framework. It implements elegant shortcuts around Imagine and a smart cache system.

```php
// This will create a cropped 200x300 thumb, cache it, and display it in an image tag
echo Thumb::create('image.jpg', 200, 300)

// Shortcuts
echo Thumb::square('image.jpg', 300)
```

What you get from those calls are not direct HTML strings but objects implementing the [HtmlObject\Tag](https://github.com/Anahkiasen/html-object) abstract, so you can use all sorts of HTML manipulation methods on them :

```php
$thumb = Thumb::square('image.jpg', 200)->addClass('image-wide');
$thumb = $thumb->wrapWith('figure')->id('avatar');

echo $thumb;
// <figure id="avatar"><img class="image-wide" src="pathToThumbnail.jpg"></figure>
```

You can at all time access the original Imagine instance used to render the images :

```php
$thumb = new Thumb('foo.jpg', 200, 200);

echo $thumb->grayscale()->onImage(function($image) {
  $image->flipVertically()->rotate(45);
});
```