---
categories:
- games
- gpu
- mobile
- unity
comments: true
date: 2013-04-07T00:00:00Z
title: Mobile Hardware Stats (and more)
url: /blog/2013/04/07/mobile-hardware-stats-and-more/
---

{%img right /img/blog/2013-04/hwstats.png %}

Short summary: Unity's [**hardware stats page**](http://stats.unity3d.com/) now has a "mobile" section. Which is exactly what it says, hardware statistics of people playing Unity games on iOS & Android. Go to [stats.unity3d.com](http://stats.unity3d.com/) and enjoy.

*Some interesting bits:*

**Operating systems**

[iOS uptake](http://stats.unity3d.com/mobile/os-ios.html) is crazy high: 98% of the market has iOS version that's not much older than one year (iOS 5.1 was released in 2012 March). You'd be quite okay targetting just 5.1 and up!

[Android uptake](http://stats.unity3d.com/mobile/os.html) is... slightly different. 25% of the market is still on Android 2.3, which is almost two and a half years old (2010 December). Note that for all practical reasons Android 3.x does not exist ;)

[Windows XP](http://stats.unity3d.com/web/os.html) in the Web Player is making a comeback at 48% of the market. Most likely explained by "Asia", see geography below.

* Windows Vista could be soon dropped, almost no one is using it anymore. XP... not dropping that just yet :(
* 64 bit Windows is still *not* the norm.


** Geography **

 [Android](http://stats.unity3d.com/mobile/os.html) is big in United States (18%), China (13%), Korea (12%), Japan (6%), Russia (4%), Taiwan (4%) -- mostly Asia.

 [iOS](http://stats.unity3d.com/mobile/os-ios.html) is big in United States (30%), United Kingdom (10%), China (7%), Russia (4%), Canada (4%), Germany (4%) -- mostly "western world".

 Looking at [Web Player](http://stats.unity3d.com/web/os.html), China is 28% while US is only 12%! 


** GPU **

[GPU makers](http://stats.unity3d.com/mobile/gpu.html) on Android: Qualcomm 37%, ARM 32%, Imagination 22%, NVIDIA 6%.

* You wouldn't guess NVIDIA is in the distant 4th place, would you?
* ARM share is almost entirely Mali 400. Strangely enough, almost no latest generation (Mali T6xx) devices.
* OpenGL ES 3.0 capable devices are 4% right now, almost exclusively pulled forward by Qualcomm Adreno 320.
* On iOS, Imagination is 100% of course...

No big changes [on the PC](http://stats.unity3d.com/web/gpu.html):

* Intel slowly rising, NVIDIA & AMD flat, others that used to exist (S3 & SIS) don't exist anymore.
* GPU capabilities increasing, though shader model 5.0 uptake seems slower than SM4.0 was.
* Due to rise of Windows XP, "can actually use DX10+" is *decreasing* :(


** Devices **

On Android, [Samsung is king](http://stats.unity3d.com/mobile/device.html) with 55% of the market. No wonder it takes majority of the Android profits I guess. The rest is split by umpteen vendors (Sony, LG, HTC, Amazon etc.).

Most popular devices are various Galaxy models. Out of non-Samsung ones, Kindle Fire (4.3%), Nexus 7 (1.5%) and then it goes into *"WAT? I guess Asia"* territory with Xiaomi MI-One (1.2%) and so on.

On iOS, [Apple has 100% share](http://stats.unity3d.com/mobile/device-ios.html) *(shocking right?)*. There's no clear leader in device model; iPhone 4S (18%), iPhone 5 (16%), iPad 2 (16%), iPhone 4 (14%), iPod Touch 4 (10%).

Interesting that first iPad can be pretty much ignored now (1.5%), whereas iPad 2 is still more popular than any of the later iPad models.


** CPU **

Single core CPUs are about 27% on both Android & iOS. The [rest on iOS](http://stats.unity3d.com/mobile/cpu-ios.html) is all dual-core CPUs, whereas almost a [quarter of Androids](http://stats.unity3d.com/mobile/cpu.html) have four cores!

ARMv6 can be quite safely ignored. Good.

On PC, the "lots and lots of cores!" future [did not happen](http://stats.unity3d.com/web/cpu.html) - majority are dual core, and 4 core CPU growth seemingly stopped at 23% (though again, maybe explained by rise of Asia?).


** FAQ **

> How big is this data set exactly?

Millions and millions. We track the data at quarterly granularity, and in the last quarter mobile has been about 200 million devices *(yes really!)*; whereas web player has been 36 million machines.

> Why no "All" section in mobile pages, with both Android & iOS?

We've added hardware stats tracking on Android earlier, so there are more Unity games made with it out there. Would be totally unfair "market share" - right now, 250 million Android devices and "only" 4 million iOS devices are represented in the stats. As more developers move to more recent Unity versions, the market share will level out and then we'll add "All" section.

> Nice charts, what did you use?

[Flot](http://www.flotcharts.org/). It is nice! I added "[track by area](https://github.com/flot/flot/pull/867)" option to it.

> How often is stats.unity3d.com page updated?

Roughly once a month.

