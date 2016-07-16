---
categories:
- uncategorized
comments: true
date: 2005-04-09T20:48:00Z
slug: animation-blending-for-walkrun
status: publish
title: Animation blending for walk/run
url: /blog/2005/04/09/animation-blending-for-walkrun/
wordpress_id: "26"
---

I've done some very basic walk/run animation blending. The results are pretty neat!

We have several walk animation cycles, each for different walk/run speed. Now I can smoothly move the character at any speed, and the right animations are selected, synchronized, mixed; and the walk is mixed in/out of another animations.

As a basis I've taken [Tom Forsyth's talk](http://eelpi.gotdns.org/papers/how_to_walk.ppt.zip) from GameTech2004 and [Charles Bloom's rambles](http://cbloom.com/3d/rambles.html) (dated 3-13-03). Of course, our case is somewhat simpler (e.g. we don't have manpower to author all turn left/right, accelerate/decelerate, etc. anims).

Try it yourself - it's good! :)

BTW, I was thinking about putting our full engine/tools/whatever somewhere, in case anyone is interested or maybe would find it useful. Well, I know, the naked sourcecode is not useful at all in the real world, but still :)

