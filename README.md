Illuminage
==========

Illuminage is a wrapper for the Imagine library to hook into the Laravel framework. It implements elegant shortcuts around Imagine and a smart cache system.

```php
// This will create a cropped 200x300 thumb, cache it, and display it in an image tag
echo Thumb::create('image.jpg', 200, 300)

// Shortcuts
echo Thumb::square('image.jpg', 300)
```