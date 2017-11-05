---
tags:
- code
- work
comments: true
date: 2007-05-28T14:11:07Z
slug: now-thats-what-i-call-a-good-api-stb_image
status: publish
title: Now that's what I call a good API (stb_image)
url: /blog/2007/05/28/now-thats-what-i-call-a-good-api-stb_image/
wordpress_id: "119"
---

The other day at work I needed a command line tool to compare some images (whether they mostly match, used in unit/functional tests). For unknown reason I could not get ImageMagick's [compare](http://www.imagemagick.org/script/compare.php) to work like I wanted, so I just wrote my own.

I used [stb_image](http://nothings.org/stb_image.c) library from [Sean Barrett](http://nothings.org) - and it just rocks! Here's the code to load a PNG image from file:


     int width, height, bpp;
     unsigned char* rgb = stbi_load( "myimage.png", &width, &height, &bpp, 3 );
     // rgb is now three bytes per pixel, width*height size. Or NULL if load failed.
     // Do something with it...
     stbi_image_free( rgb );
 

That's it! Basically a single line to load the image (and of course the library has similar functions to load from a block of memory, etc.). And the whole "library" is a single file - just add to your project and there it is. In comparison, loading a PNG file using de-facto [libpng](http://www.libpng.org/pub/png/libpng.html) takes more than 100 lines of code (and some time to read the docs).

Small is beautiful.

...and the way we do graphics related unit/functional/compatibility testing deserves a separate article. Sometime in the future!
