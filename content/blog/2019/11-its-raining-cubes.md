---
title: "It's Raining Cubes"
date: 2019-11-18T16:30:10+03:00
tags: ['demos']
comments: true
---

So a dozen years ago I wrote "[hey, 4 kilobyte intros are starting to get interesting](/blog/2007/05/19/hey-4-kilobyte-intros-are-starting-to-get-interesting/)". Fast forward to 2019,
and we made an attempt to make a 4KB demo with my team at work. None of us have any previous size-limited
demo experience? ✅ We have no idea what the demo would be about? ✅ Does it have a high chance of being
totally "not good"? ✅ So we did the only thing that made sense in this situation -- try to do it!

We did not follow the modern trend of making 4KB demos that are purely "one giant shader that does
raymarching", and instead did most of the code on the CPU in C++. Physics simulation? Sure why not.
Deferred rendering? Of course. Just write it in regular programming style, without paying
*that much* attention to size coding tricks (see [in4k](https://in4k.github.io/) or
[sizecoding](http://www.sizecoding.org/wiki/Main_Page))? Naturally.

Maybe that's why this did not fit into 4 kilobytes :) and ended up being 6.6KB in size.

**Executable**: [ItsRainingCubes.exe](https://aras-p.info/files/4k/2019/ItsRainingCubes.exe) (6.6KB)<br/>
**Source**: [Zip with VS2019 projects](https://aras-p.info/files/4k/2019/ItsrRainingCubes-source.zip)<br/>
**Pouët**: [Link](https://www.pouet.net/prod.php?which=83740)

[{{<img src="/img/blog/2019/demo/ItsRainingCubes1.jpg" width="360">}}](/img/blog/2019/demo/ItsRainingCubes1.jpg)
[{{<img src="/img/blog/2019/demo/ItsRainingCubes5.jpg" width="360">}}](/img/blog/2019/demo/ItsRainingCubes5.jpg)

Tech details:

* Verlet style physics simulation. Simulates points and springs between them; also approximates each cube
  with a sphere :) and pushes points outside of them.
* Deferred rendering *(world's most pointless deferred usage?)* with colors, normals and the Z-buffer.
  There's one shadowmap for the light source. The whole G-buffer is blurred (including depth and normals too!)
  with an Masaki Kawase style iterative filter and then the lighting is computed. That's what produces the
  bloom-like outlines, soft edges on cubes and weird shadow shapes. It should not have worked at all.
* Music is made in Renoise, using [4klang](https://github.com/hzdgopher/4klang) for playback.
* Executable compressed using [Crinkler](http://www.crinkler.net/), and shaders minified using
  [Shader Minifier](https://github.com/laurentlb/Shader_Minifier).
* Visual Studio 2019, C++, OpenGL (compatibility profile with some GL3/GL4 extensions) was used.


Credits: [Ascentress](https://twitter.com/Ascentress), [shana](https://twitter.com/sh4na), TomasK, [NeARAZ](https://twitter.com/aras_p).

Youtube video capture:

{{< youtube id="_rdtLRe68Yo" >}}

While it's not impressive by any standards, I kinda expected us to achieve even less. Again, no previous experience
in this area whatsoever! Four _(well ok, almost seven...)_ kilobytes is not much, but with tools like Crinkler
(great executable size reporting there, by the way -
[here's an example](/files/4k/2019/ItsRainingCubes-crinkler-report.html)) it's manageable. There's some wrestling with MSVC if you
want to ignore all the default libraries, like you have to make your own implementations of `_fltused`, `_ftol2()`,
`_ftol2_sse()`, `memset()`; load functions like `cos()` manually from the old `msvcrt.dll`, and so on. Funtimes. But once
the basic setup is done, then it's "just programming" really.

_That's it! Go make some demos_.

