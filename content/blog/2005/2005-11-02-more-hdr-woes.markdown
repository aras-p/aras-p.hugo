---
tags:
- gpu
comments: true
date: 2005-11-02T11:09:00Z
slug: more-hdr-woes
status: publish
title: More HDR woes
url: /blog/2005/11/02/more-hdr-woes/
wordpress_id: "76"
---

I'm still spending an occasional minute on my [HDR demo](/blog/2005/10/23/jumped-onto-hdr-bandwagon). Now, everything is fine so far, except one thing: I can't get MSAA working on some Radeons (and I don't have a Radeon right now, which makes debugging a lot harder). The main point of my demo is to have MSAA on ordinary hw, so this is bad.

The reason seems to be that on older Radeons [MSAA does not resolve alpha channel](http://www.beyond3d.com/forum/showthread.php?p=611933#post611933), which obsiously messes things up in my case. I'm using RGBE8 encoding for the main rendertarget, and it RGB gets MSAA'd and exponent not - then oh well, no good anti aliasing most of the time.

Of course I could always manually supersample everything, but this would defeat the whole point of the demo. Or I could render everything in two passes, one for RGB and one for exponent - but this also is not very nice...

Probably I'll just release the demo as it is now and wait for possible feedback. Or dig up an old Radeon somewhere and debug more - but replacing the video card in my Shuttle XPC is not an easy task :)

