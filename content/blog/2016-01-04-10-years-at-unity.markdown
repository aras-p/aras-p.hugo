---
categories:
- unity
- work
comments: true
date: 2016-01-04T00:00:00Z
title: 10 Years at Unity
url: /blog/2016/01/04/10-years-at-unity/
---

Turns out, I started working on this "Unity" thing exactly 10 years ago. I wrote the backstory in
"[2 years later](/blog/2008/01/15/about-two-years-ago/)" and "[4 years later](/blog/2010/01/04/four-years-ago-today/)"
posts, so not worth repeating it here.

A lot of things have happened over these 10 years, some of which are quite an experience.

Seeing the company go through various stages, from just 4 of us back then to, *I dunno*, 750 amazing people by now? is
super interesting. You get to experience the joys & pains of growth, the challenges and opportunities allowed by that
and so on.

Seeing all the [amazing games](http://madewith.unity.com/) made with Unity is extremely motivating. Being a part
this super-popular engine that everyone loves to hate is somewhat less motivating, but hey let's not focus on
that today :)

Having my tiny contributions in all releases from Unity 1.2.2 to *(at the time of writing)* 5.3.1 and 5.4 beta feels good too!


** What now? or "hey, what happened to Optimizing Unity Renderer posts?" **

Last year I did several "Optimizing Unity Renderer" posts ([part 1](/blog/2015/04/01/optimizing-unity-renderer-1-intro/),
[part 2](/blog/2015/04/04/optimizing-unity-renderer-2-cleanups/),
[part 3](/blog/2015/04/27/optimizing-unity-renderer-3-fixed-function-removal/)) and then, when things were about to get
interesting, I stopped. *Wat happend?*

Well, I stopped working on them optimizations; the multi-threaded rendering and other optimizations are done by people
who are way better at it than I am *([@maverikou](https://twitter.com/maverikou),
[@n3rvus](https://twitter.com/n3rvus), [@joeldevahl](https://twitter.com/joeldevahl) and some others who aren't
on twitterverse)*.

*So what I am doing then?*

Since mid-2015 I've moved into kinda-maybe-a-lead-but-not-quite position. Perhaps that's better characterized as
"all seeing evil eye" or maybe "that guy who asks why A is done but B is not". I was the "graphics lead" a number of years
ago, until I decided that I should just be coding instead. Well, now I'm back to "you don't just code" state.

In practice I do several things for past 6 months:

* Reviewing a lot of code, or the "all seeing eye" bit. I've already been doing
[quite a lot](/blog/2013/07/07/reviewing-all-the-code/) of that, but with large amount of new graphics hires in 2015
the amount of graphics-related code changes has gone up massively.
* Part of a small team that does overall "graphics vision", prioretization, planning, roadmapping and stuff.
The "why A is done when B should be done instead" bit. This also means doing job interviews, looking into which areas
are understaffed, onboarding new hires etc.
* Bugfixing, bugfixing and more bugfixing. It's not a secret that stability of Unity could be improved. Or that
"Unity does not do what I think it should do" (which very often is not *technically* "bugs", but it feels like that from
the user's POV) happens a lot.
* Improving workflow for other graphics developers internally. For example trying to reduce the overhead of our
[graphics tests](/blog/2011/06/17/testing-graphics-code-4-years-later/) and so on.
* Work on some stuff or write some documentation when time is left from the above. Not much of actual
coding done so far, largest items I remember are some work on frame debugger improvements (in 5.3), texture arrays / CopyTexture (in 5.4 beta) and a bunch of smaller items.

For the foreseeable future I think I'll continue doing the above.

By now we do have quite a lot of people to work on graphics improvements; my own personal goal is that by mid-2016 there
will be way less internet complaints along the lines of "unity is shit". So, less regressions, less bugs, more stability,
more in-depth documentation etc.

Wish me luck!

