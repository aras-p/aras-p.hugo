---
categories:
- code
- opengl
- work
comments: true
date: 2007-07-14T21:31:19Z
slug: debugging-story-video-memory-leaks
status: publish
title: 'Debugging story: video memory leaks'
url: /blog/2007/07/14/debugging-story-video-memory-leaks/
wordpress_id: "125"
---

I [ranted](/blog/2007/06/04/opengl-pbuffers-suck) about OpenGL p-buffers a while ago. Time for the whole story!

From time to time I hit some nasty debugging situation, and it always takes _ages_ to figure out, and the path to the solution is always different. This is an example of such a debugging story.

While developing shadow mapping I implemented a "screen space shadows" thing (where cascaded shadow maps are gathered into a screen-space texture and shadow receiver rendering later uses only that texture). Then while being in the editor and maximizing/restoring the window a few times, everything locks up for 3 or 5 seconds, then resumes normally.

So there's a problem: a complete freeze after editor window is being resized after a couple of times (not immediately!), but otherwise everything just works. Where is the bug? What caused it?

Since shadows were working fine before, and I never noticed such lock-ups - it must be the screen-space shadow gathering thing that I just implemented, right? _(Fast-forward answer: no)_ So I try to figure out _where_ the lock-up is happening. Profiling does not give any insights - the lock-up is not even in my process, instead "somewhere". Hm... I insert lots of manual timing code around various code blocks (that deal with shadows). They say the lock-up _most often_ happens when activating a new render texture (an OpenGL p-buffer), specifically, calling a glFlush(). But not always, sometimes it's still somewhere else.

After some head-scratching, a session with OpenGL Driver Profiler reveals what is actually happening - video memory is leaked! Apparently Mac OS X "virtualizes" VRAM, and when it runs out, the OS will still happily create p-buffers and so on, it will just start swapping VRAM contents to AGP/PCIe area. This swapping causes the lock-up. Ok, so now I know _what_ is happening, I just need to find out _why_.

I look at all the code that deals with render textures - it looks ok. And it would be pretty strange if a VRAM leak would be unnoticed for two years since Unity is out in the wild... So that must be the depth render textures that are causing a leak (since they are a new type for the shadows), right? _(Answer: no)_

I build a test case that allocates and deallocates a bunch of depth render textures each frame. No leaks... Huh.

I change my original code so that it gathers screen-space shadows onto the screen directly, instead of the screen-sized texture. No leaks... Hm... So it must be the depth render texture followed by screen-size render texture, that is causing the leaks, right? _(Answer: no)_ Because when I have just the depth render texture, I have no leaks; and when I have no depth render texture, instead I gather shadows "from nothing" into a screen-size texture, I also have no leaks. So it must be the combination!

So far, the theory is that rendering into a depth texture followed by creation of screen-size texture will cause a video memory leak _(Answer: no)_. It looks like it leaks the amount that should be taken by depth texture (I say "it looks" because in OpenGL you never know... it's all abstracted to make my life easier, hurray!). Looks like a fine bug report, time to build a small repro application that is completely separate from Unity.

So I grab some p-buffer sample code from Apple's developer site, change it to also use depth textures and rectangle textures, remove all unused cruft, code the expected bug pattern (render into depth texture followed by rectangle p-buffer creation) and... it does not leak. D'oh.

Ok, another attempt: I take the p-buffer related code out of Unity, build a small application with just that code, code the expected bug pattern and... it does not leak! Huh?

_Now what?_

I compare the OpenGL call traces of Unity-in-test-case (leaks) and Unity-code-in-a-separate-app (does not leak). Of course, the Unity case does a lot more; setting up various state, shaders, textures, rendering actual objects with actual shaders, filtering out redundant state changes and whatnot. So I try to bring in bits of stuff that Unity does into my test application.

After a while I made my test app leak video memory (now that's an achievement)! Turns out the leak happens when doing this:



	
  1. Create depth p-buffer

	
  2. Draw to depth p-buffer

	
  3. Copy it's contents into a depth texture

	
  4. Create a screen-sized p-buffer

	
  5. Draw something into it _using_ the depth texture

	
  6. Release the depth texture and p-buffer

	
  7. Release the screen-sized p-buffer


My initial test app was not doing step 5... Now, _why_ the leaks happens? Is it a bug or something I am doing wrong? And more importantly: how to get rid of it?

My suspicion was that OpenGL context sharing was somehow to blame here _(finally, a correct suspicion)_. We share OpenGL contexts, because, well, it's the only sane thing to do - if you have a texture, mesh or shader somewhere, you really want to have it available both to the screen and when rendering into something else. The documentation on sharing of OpenGL contexts is extremely spartan, however. Like: "yeah, when they are shared, then the resources are shared" - great. Well, the actual text is like this (Apple's [QA1248](http://developer.apple.com/qa/qa2001/qa1248.html)):


> All sharing is peer to peer and developers can assume that shared resources are reference counted and thus will be
maintained until explicitly released or when the last context sharing resources is itself released. It is helpful to think of this in the simplest terms possible and not to assume excess complication.


Ok, _I am_ thinking of this in the simplest terms possible... and it leaks video memory! The docs do not have a single word on _how_ the resources are reference counted and what happens when a context is deleted.

Anyway, armed with my suspicion of context sharing being The Bad Guy here, I tried random things in my small test app. Turns out that unbinding any active textures from a context before switching to new one got rid of the leak. It looks like objects are refcounted by contexts, and they are not actually deleted while they are bound in some context (that is what I expect to happen). However, when a context itself is deleted, it seems as if it does not decrease refcounts of these objects (that is definitely what I don't expect to happen). I am not sure if that's a bug, or just undocumented "feature"...

All happy, I bring in my changes to the full codebase ("unbind any active textures before switching to a new context!")... and the leak is still there. Huh?

After some head-scratching and randomly experimenting with _whatever_, turns out that you have to unbind any active "things" before switching to a new context. Even leaving a vertex buffer object bound can make a depth texture memory be leaked when another context is destroyed. Funky, eh?

So that was some 4 days wasted on chasing the bug that started out as "mysterious 5 second lock-ups", went through "screen-space shadows leak video memory", then through "depth textures followed by screen-size textures leak video memory" and through "unbind textures before switching contexts" to "unbind everything before switching contexts". Would I have guessed it would end up like this? Not at all. I am still not sure if that's the intended behavior or a bug; it looks more like a bug to me.

The take-away for OpenGL developers: **when using shared contexts, unbind active textures, VBOs, shader programs etc. before switching OpenGL contexts**. Otherwise at least on Mac OS X you will hit video memory leaks.

It's somewhat sad that I find myself fighting issues like that most of my development time - not actually implementing some cool new stuff, but _making stuff actually work_. Oh well, I guess that is the difference between making (tech)demos and an actual software product.
