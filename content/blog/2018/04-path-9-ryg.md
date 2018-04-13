---
title: "Daily Pathtracer 9: A wild ryg appears"
date: 2018-04-13T16:15:20+03:00
tags: ['rendering', 'code']
comments: true
---

*Introduction and index of this series [is here](/blog/2018/03/28/Daily-Pathtracer-Part-0-Intro/)*.

In the [previous post](/blog/2018/04/11/Daily-Pathtracer-8-SSE-HitSpheres/), I did a basic SIMD/SSE
implementation of the "hit a ray against all spheres"
[function](https://github.com/aras-p/ToyPathTracer/blob/08-simd/Cpp/Source/Maths.cpp#L50). And then
of course, me being a n00b at SIMD, I did some stupid things (and had other inefficiencies I knew about
outside the SIMD function, that I planned to fix later). And then this happened:

> You used the ultimate optimization technique: nerd sniping rygorous into doing it for you =)
> ([via](https://twitter.com/TheEpsylon/status/984200797272584198))

i.e. Fabian Giesen himself did a bunch of optimizations and submitted a
[pull request](https://github.com/aras-p/ToyPathTracer/pull/10). Nice work, ryg!

His changes got performance of **107 -> 187 Mray/s on PC, and 30.1 -> 41.8 Mray/s on a Mac**.
That's not bad at all for relatively simple changes!


### ryg's optimizations

Full list of changes can be seen in the [pull request](https://github.com/aras-p/ToyPathTracer/pull/10/commits),
here are the major ones:

**Use [_mm_loadu_ps](https://software.intel.com/sites/landingpage/IntrinsicsGuide/#text=_mm_loadu_ps&expand=3153)
to load memory into a SIMD variable** ([commit](https://github.com/aras-p/ToyPathTracer/pull/10/commits/ba4b3bf4a3cf3b9a892b088b927cde6064824111)).
On Windows/MSVC that got a massive speedup; no change on Mac/clang since clang was already generating
movups instruction there.

**Evaluate ray hit data only once** ([commit](https://github.com/aras-p/ToyPathTracer/pull/10/commits/5dd6e20effadc631e71e611e22fd4fa48327202a)).
My original code was evaluating hit position & normal for each closer sphere it had hit so far; this
change only remembers `t` value and sphere index instead, and calculates position & normal for the final
closest sphere. It also has one possible
[approach](https://github.com/aras-p/ToyPathTracer/pull/10/commits/5dd6e20effadc631e71e611e22fd4fa48327202a#diff-b086a7875c0a62a622cf41b795ae0170R121)
in how to do "find minimum value and index of it in an SSE register", that I was wondering about.

**"Know" which objects emit light instead of searching every time** ([commit](https://github.com/aras-p/ToyPathTracer/pull/10/commits/f9dae06ce3f0cf5396e06532ca386e553f1c6f3d)).
This one's not SIMD at all, and super obvious. In the explicit light sampling loop, for each ray bounce
off a diffuse surface, I was going through all spheres, checking "hey, do you emit light?". But only
a couple of all of them do! So instead, have an explicit array of light-emitting sphere indices,
and only go through that. This was another massive speedup.

**Several small simplifications** ([commit](https://github.com/aras-p/ToyPathTracer/pull/10/commits/198a8b700c114114bd17f6bd09e57af4403459c7),
[commit](https://github.com/aras-p/ToyPathTracer/pull/10/commits/fc7127c16df47e522e1d139a9279663cbc0d2f90),
[commit](https://github.com/aras-p/ToyPathTracer/pull/10/commits/03d0d65b3033baf6c1cc055b53c38e61fd18e2c1),
[commit](https://github.com/aras-p/ToyPathTracer/pull/10/commits/d03f5d2a4957e585cce8b27a9a5da264266e6352),
[commit](https://github.com/aras-p/ToyPathTracer/pull/10/commits/66958597fbd388fdf395a692755496fae197a5ec)).
Each one self-explanatory.


### What's next

I want to apply some of the earlier & above optimizations to the C#, C#+Burst and GPU implementations too,
just so that all versions are on the same ground again. Maybe I'll do that next!
