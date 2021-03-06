# DEPRECATED: Use [Glide](http://glide.thephpleague.com/) instead

# Illuminage

[![Build Status](https://travis-ci.org/Anahkiasen/illuminage.png?branch=master)](https://travis-ci.org/Anahkiasen/illuminage)
[![Latest Stable Version](https://poser.pugx.org/anahkiasen/illuminage/v/stable.png)](https://packagist.org/packages/anahkiasen/illuminage)
[![Total Downloads](https://poser.pugx.org/anahkiasen/illuminage/downloads.png)](https://packagist.org/packages/anahkiasen/illuminage)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Anahkiasen/illuminage/badges/quality-score.png?s=d542035e5d3e51442563d09c1fa28575f241a070)](https://scrutinizer-ci.com/g/Anahkiasen/illuminage/)
[![Code Coverage](https://scrutinizer-ci.com/g/Anahkiasen/illuminage/badges/coverage.png?s=221ca0a5ff4b625fb6756c3de3bad218fbe2144b)](https://scrutinizer-ci.com/g/Anahkiasen/illuminage/)

## Setup

First do `composer require anahkiasen/illuminage:dev-master`.

Then if you're on a Laravel app, add the following to the `providers` array in `app/config/app.php` :

```php
'Illuminage\IlluminageServiceProvider',
```

And this in the `facades` array in the same file :

```php
'Illuminage' => 'Illuminage\Facades\Illuminage',
```

And then do `artisan asset:publish anahkiasen/illuminage`.

## Usage

Illuminage is a wrapper for the Imagine library to hook into the Laravel framework. It implements elegant shortcuts around Imagine and a smart cache system.

```php
// This will create a cropped 200x300 thumb, cache it, and display it in an image tag
echo Illuminage::thumb('image.jpg', 200, 300)
// or
echo Illuminage::image('image.jpg')->thumbnail(200, 300)

// Shortcuts
echo Illuminage::square('image.jpg', 300)
```

What you get from those calls are not direct HTML strings but objects implementing the [HtmlObject\Tag](https://github.com/Anahkiasen/html-object) abstract, so you can use all sorts of HTML manipulation methods on them :

```php
$thumb = Illuminage::square('image.jpg', 200)->addClass('image-wide');
$thumb = $thumb->wrapWith('figure')->id('avatar');

echo $thumb;
// <figure id="avatar"><img class="image-wide" src="pathToThumbnail.jpg"></figure>
```

You can at all time access the original Imagine instance used to render the images :

```php
$thumb = Illuminage::image('foo.jpg')->thumbnail(200, 200);

echo $thumb->grayscale()->onImage(function($image) {
  $image->flipVertically()->rotate(45);
});
```
