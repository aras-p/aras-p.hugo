---
title: "Daily Pathtracer Part 0: Intro"
date: 2018-03-28T14:24:10+03:00
tags: ['rendering', 'code']
comments: true
---

As [mentioned before](/blog/2018/03/21/Random-Thoughts-on-Raytracing/), I realized I've never done
a path tracer. Given that I suggest everyone else who asks "how should I graphics" start with one,
this sounded wrong. So I started making a super-simple one. When I say *super simple*, I mean it!
It's not useful for anything, think of it as [smallpt] with more lines of code :)

However I do want to make one in C++, in C#, in something else perhaps, and also run into various
LOLs along the way.

If you want to *actually* learn someting about path tracing or raytracing, I'd suggest these:

* "[Physically Based Rendering: From Theory to Implementation](http://www.pbrt.org/)" by Pharr,
  Jakob, Humphreys. It's excellent, and explains pretty much *everything*.
* For a much lighter introduction, "[Ray Tracing in One Weekend](http://in1weekend.blogspot.lt/2016/01/ray-tracing-in-one-weekend.html)"
  and two follow-up minibooks by Shirley are really good (and dirt cheap!).
* "[The Graphics Codex](http://graphicscodex.com/)" app by McGuire is great.
* Presentation [slides on smallpt](http://www.kevinbeason.com/smallpt/#moreinfo) are a good intro too.


[{{<imgright src="/img/blog/2018/rt-pathtracer.png" width="300px">}}](/img/blog/2018/rt-pathtracer.png)

Now, all that said. *Sometimes* it can be useful (or at least fun) to see someone who's clueless
in the area going through parts of it, bumping into things, and going into dead ends or wrong approaches.
This is what I shall do in this blog series!

Let's see where this *path* will lead us.


### Series index

* [Part 1: Initial C++](/blog/2018/03/28/Daily-Pathtracer-Part-1-Initial-C--/) implementation and walkthrough.
* [Part 2: Fix stupid performance issue](/blog/2018/03/28/Daily-Pathtracer-Part-2-Fix-Stupid/).
* [Part 3: C#, Unity, Burst](/blog/2018/03/28/Daily-Pathtracer-Part-3-CSharp-Unity-Burst/).
