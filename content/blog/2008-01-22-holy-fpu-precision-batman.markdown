---
categories:
- d3d
- rant
- unity
- work
comments: true
date: 2008-01-22T11:46:38Z
slug: holy-fpu-precision-batman
status: publish
title: Holy FPU precision, Batman!
url: /blog/2008/01/22/holy-fpu-precision-batman/
wordpress_id: "155"
---

_(cross-posted from [blogs.unity3d.com](http://blogs.unity3d.com/2008/01/22/holy-fpu-precision-batman/))_

One of our customers found an interesting bug the other day: embedding Unity Web Player into a web page makes some javascript animation libraries not work correctly. For example, [script.aculo.us](http://script.aculo.us/) or [Dojo Toolkit](http://dojotoolkit.org/) would stop doing some of their tasks. But only on Windows, and only on some browsers (Firefox and Safari).

Wait a moment... Unity plugin makes nice wobbling web page elements not wobble anymore!? Sounds like an _interesting_ issue...

So I prepared for a debug session and tried the usual "divide by two until you locate the problem" approach.





  * Unity Web Player is composed of two parts: a small browser plugin, and the actual "engine" (let's call it "runtime"). First I change the plugin so that it only loads the data, but never loads or starts the runtime. Everything works. So the problem is not in the plugin. _Good_.


  * Load the runtime and do basic initialization (create child window, load Mono, ...), but never actually start playing the content - everything works.


  * Load the runtime and _fully_ initialize everything, but never actually start playing the content - the bug appears! By now I know that the problem is _somewhere_ in the initialization.



Initialization reads some settings from the data file, creates some "manager objects" for the runtime,     initializes graphics device, loads first game "level" and then the game can play.

What of the above could cause _something_ inside browser's JavaScript engine stop working? And do that only on Windows, and only on some browsers? My first guess was the most platform-specific part: intialization of the graphics device, which on Windows usually happens to be Direct3D.

So I continued:




  * Try using OpenGL instead of Direct3D - everything works. By now it's confirmed that initializing Direct3D causes something else in the browser not work.


  * "A-ha!" moment: tell Direct3D to not change floating point precision (via a [create flag][1]). Voilà, everything works!



I don't know how I _actually_ came up with the idea of testing floating point precision flag. Maybe I remembered some related problems we had a while ago, where Direct3D would cause timing calculations be "off", if the user's machine was not rebooted for a couple of weeks or more. That time around we properly changed our timing code to use 64 bit integers, but left Direct3D precision setting intact.



> 
Side note: Intel x86 floating point unit (FPU) can operate in various [precision modes](http://www.stereopsis.com/FPU.html), usually 32, 64 or 80 bit. By default Direct3D 9 sets FPU precision to 32 bit (i.e. single precision). Telling D3D to not change FPU settings _could_ lower performance somewhat, but in my tests it did not have any noticeable impact.




So there it was. A debugging session, one line of change in the code, and fancy javascript webpage animations work on Windows in Firefox and Safari. This is coming out in Unity 2.0.2 update soon.

The moral? Something in one place can affect seemingly _completely_ unrelated things in another place!

[1]: http://msdn2.microsoft.com/en-us/library/bb172527(VS.85).aspx
