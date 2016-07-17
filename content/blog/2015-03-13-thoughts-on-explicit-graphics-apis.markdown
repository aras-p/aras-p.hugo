---
tags:
- d3d
- gpu
- opengl
- rant
comments: true
date: 2015-03-13T00:00:00Z
title: Random Thoughts on New Explicit Graphics APIs
url: /blog/2015/03/13/thoughts-on-explicit-graphics-apis/
---

Last time I wrote about graphics APIs was
[almost a year ago](/blog/2014/05/31/rant-about-rants-about-opengl/). Since then,
[Apple Metal](https://developer.apple.com/metal/) was unveiled and shipped in iOS 8;
as well as [Khronos Vulkan](https://www.khronos.org/vulkan) was announced *(which is very much
[AMD Mantle](http://community.amd.com/community/amd-blogs/amd-gaming/blog/2015/03/02/on-apis-and-the-future-of-mantle),
improved to make it cross-vendor)*. [DX12](http://blogs.msdn.com/b/directx/archive/2015/03/10/directx-12-looking-back-at-gdc-2015-and-a-year-of-amazing-progress.aspx)
continues to be developed for Windows 10.

[@promit_roy](https://twitter.com/promit_roy) has a
[**very good post on gamedev.net**](http://www.gamedev.net/topic/666419-what-are-your-opinions-on-dx12vulkanmantle/#entry5215019)
about why these new APIs are needed and what problems do they solve. Go read it now, it's good.

Just a couple more thoughts I'd add.


** Metal experience **

When I wrote the previous [OpenGL rant](/blog/2014/05/31/rant-about-rants-about-opengl/), we
already were working on Metal and had it "basically work in Unity". I've only ever worked on
PC/mobile graphics APIs before (D3D9, D3D11, OpenGL/ES, as well as D3D7-8 back in the day),
so Metal was the first of these "more explicit APIs" I've experienced (I never actually did
anything on consoles before, besides just seeing some code).

*ZOMG what a breath of fresh air*.

Metal is super simple and very, very clear. I was looking at the header files and was amazed at
how small they are -- "these few short files, and that's it?! wow." A world of difference
compared to how much accumulated stuff is in OpenGL core & extension headers (to a lesser degree
in OpenGL ES too).

Conceptually Metal is closer to D3D11 than OpenGL ES (separate shader stages; constant buffers;
same coordinate conventions), so "porting Unity to it" was mostly taking D3D11 backend
(which I did back in the day too, so familiarity with the code helped), changing the API
calls and generally *removing stuff*.

Create a new buffer (vertex, index, constant - does not matter) -- one line of code
([MTLDevice.newBuffer*](https://developer.apple.com/library/ios/documentation/Metal/Reference/MTLDevice_Ref/index.html#//apple_ref/occ/intfm/MTLDevice/newBufferWithLength:options:)).
Then just get a pointer to data and do whatever you want. No mapping, no locking, no staging
buffers, no trying to nudge the API into doing the exact type of buffer update you want
(on the other hand, data synchronization is your own responsibility now).

And even with the very early builds we had, everything more or less "just worked", with a super
useful debug & validation layer. Sure there were issues and there were occasional missing things
in the early betas, but nothing major and the issues got fixed fast.

To me Metal showed that a new API that gets rid of the baggage and exposes platform strengths
is a very pleasant thing to work with. Metal is essentially just several key ideas (many of which
are shared by other "new APIs" like Vulkan or DX12):

* Command buffer creation is separate from submission; and creation is mostly stateless (do that from any thread).
* Whole rendering pipeline state is specified, thus avoiding "whoops need to revalidate & recompile shaders" issues.
* Unified memory; just get a pointer to buffer data. Synchronization is your own concern.

Metal very much keeps the existing resource binding model from D3D11/OpenGL - you bind
textures/buffers to shader stage "resource slots".

I think of *all* public graphics APIs (old ones like OpenGL/D3D11 and new ones like Vulkan/DX12),
Metal is probably the easiest to learn. Yes it's very much platform specific, but again, *OMG so easy*.

Partially because it keeps the traditional binding model -- while that means Metal
can't do fancy things like bindless resources, it also means the binding model is simple.
I would *not* want to be a student learning graphics programming, and having to understand
Vulkan/DX12 resource binding.


** Explicit APIs and Vulkan **

This bit from [Promit's post](http://www.gamedev.net/topic/666419-what-are-your-opinions-on-dx12vulkanmantle/#entry5215019),

> But there's a very real message that if these APIs are too challenging to
> work with directly, well the guys who designed the API also happen to run very
> full featured engines requiring no financial commitments.

I love conspiracy theories as much as the next guy, but I don't think that's quite true.
If I'd put my cynical hat on, then sure: making graphics APIs hard to use is an interest
of middleware providers. You could also say that making sure there are *lots of different APIs*
is an interest of middleware providers too! The harder it is to make things, the better for
them, right.

In practice I think we're all too much hippies to actually think that way. I can't speak
for Epic, Valve or Frostbite of course, but on Unity side it was mostly
[Christophe](http://www.g-truc.net/) being involved in Vulkan, and if you think his motivation is
commercial interest then you don't know him :) I and others from graphics team were only very
casually following the development -- I would have loved to be more involved, but was 100%
busy on [Unity 5.0](https://unity3d.com/unity/whats-new/unity-5.0) development all the time.

So there.

That said, to some extent the explicit APIs (both Vulkan and DX12) are harder to use. I think
it's mostly due to more flexible (but much more complicated) resource binding model. See Metal above -
my opinion is that stateless command buffer creation and fully specified pipeline state actually
make it *easier* to use the API. But new way of resource binding and to some extent ability to
reuse & replay command buffer chunks (which Metal does not have either) does complicate things.

However, I think this is worth it. The actual lowest level of API should be very efficient, even
if somewhat "hard to use" (or require an expert to use it). Additional "ease of use" layers can be
put on top of that! The problem with OpenGL was that it was trying to do both at once, with a
very sad side effect that everyone and their dog had to implement all the layers (in often
subtly incompatible ways).

I think there's plenty of space for "somewhat abstacted" libraries or APIs to be layered on top of
Vulkan/DX12. Think XNA back when it was a thing; or three.js in WebGL world. Or
[bgfx](https://github.com/bkaradzic/bgfx) in the C++ world. These are all good and awesome.

Let's see what happens!
