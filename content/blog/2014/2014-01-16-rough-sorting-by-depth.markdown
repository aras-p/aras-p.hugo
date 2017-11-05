---
tags:
- code
comments: true
date: 2014-01-16T00:00:00Z
title: Rough sorting by depth
url: /blog/2014/01/16/rough-sorting-by-depth/
---

TL;DR: use some highest bits from a float in your integer sorting key.

In graphics, often you want to sort objects back-to-front (for transparency) or front-to-back
(for occlusion efficiency) reasons. You also want to sort by a bunch of other data (global layer,
material, etc.). Christer Ericson has a [good post on exactly that](http://realtimecollisiondetection.net/blog/?p=86).

There's a question in the comments:

> I have all the depth values in floats, and I want to use those values in the key.
> What is the best way to encode floats into ‘bits’ (or integer) so that I can use
> it as part of the key ?

While "the best way" is hard to answer universally, just taking some highest bits off a float is a
simple and decent approach.

Floating point numbers have a nice property that if you interpret their bits as integers, then larger
numbers result in larger integers - i.e. you can treat float as an integer and compare them just fine
(within same sign). See details at Bruce Dawson's
[blog post](http://randomascii.wordpress.com/2012/01/11/tricks-with-the-floating-point-format/).

And due to the way floats are laid out, you can chop off lowest bits of the mantissa and only lose some precision. For something like front-to-back sorting, we only need a very rough sort. In fact a quantized sort is good, since you do also want to render objects with same material together etc.

Anyhow, for example taking 10 bits. Assuming all numbers are positive (quite common if you're sorting
by "distance from camera"), we can ignore the sign bit which will be always zero. So you end up only using 9 bits for the depth sorting.

``` c
// Taking highest 10 bits for rough sort of positive floats.
// Sign is always zero, so only 9 bits in the result are used.
// 0.01 maps to 240; 0.1 to 247; 1.0 to 254; 10.0 to 260;
// 100.0 to 267; 1000.0 to 273 etc.
unsigned DepthToBits (float depth)
{
	union { float f; unsigned i; } f2i;
	f2i.f = depth;
	unsigned b = f2i.i >> 22; // take highest 10 bits
	return b;
}
```

And that's about it. Put these bits into your sorting key and go sort some stuff!


***Q: But what about negative floats?***

If you pass negative numbers into the above `DepthToBits` function, you will get wrong order. Turned
to integers, negative numbers will be larger than positive ones; and come sorted the wrong way:

```
-10.0 -> 772
-1.0 -> 766
-0.1 -> 759
0.1 -> 247
1.0 -> 254
10.0 -> 260
```

With some bit massaging you can turn floats into still-perfectly-sortable integers, even with
both positive and negative numbers. Michael Herf has an [article on that](http://stereopsis.com/radix.html). Here's the code with his trick, that handles both positive and negative numbers (now uses all 10
bits though):

```
unsigned FloatFlip(unsigned f)
{
	unsigned mask = -int(f >> 31) | 0x80000000;
	return f ^ mask;
}

// Taking highest 10 bits for rough sort of floats.
// 0.01 maps to 752; 0.1 to 759; 1.0 to 766; 10.0 to 772;
// 100.0 to 779 etc. Negative numbers go similarly in 0..511 range.
unsigned DepthToBits (float depth)
{
	union { float f; unsigned i; } f2i;
	f2i.f = depth;
	f2i.i = FloatFlip(f2i.i); // flip bits to be sortable
	unsigned b = f2i.i >> 22; // take highest 10 bits
	return b;
}
```


***Q: Why you need some bits? Why not just sort floats?***

Often you don't want to sort *only* by distance. You also want to sort by material, or mesh, or various other things (much more details in [Christer's post](http://realtimecollisiondetection.net/blog/?p=86)).

Sorting front-to-back on very limited bits of depth has a nice effect that you essentially "bucket" objects into ranges, and within each range you can sort them to reduce state changes.

Packing sorting data tightly into a small integer value allows either writing a very simple comparison operator (just compare two numbers), or using radix sort.

