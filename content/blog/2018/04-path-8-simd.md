---
title: "Daily Pathtracer 8: SSE HitSpheres"
date: 2018-04-11T21:38:20+03:00
tags: ['rendering', 'code']
comments: true
---

*Introduction and index of this series [is here](/blog/2018/03/28/Daily-Pathtracer-Part-0-Intro/)*.

In the [previous post](/blog/2018/04/10/Daily-Pathtracer-Part-7-Initial-SIMD/), I talked about concept
of SIMD, structure-of-arrays layout, and one (not good) approach of "let's use SSE for `float3` struct".
Just rearranging our sphere data into SoA layout for
[`HitSpheres` function](https://github.com/aras-p/ToyPathTracer/blob/07-simd/Cpp/Source/Maths.cpp#L50)
gave a nice speed boost. Now that the data is all nice let's try to use actual SSE SIMD code for that.

> Note: I don't have much experience with SIMD; this is a learning exercise for me too.
> Things I do might be totally stupid. You've been warned!


### SIMD helpers

At least right now while I don't have much experience, I do find SSE intrinsic functions "a bit"
(mildly said) unreadable and scary looking. Too many underscores, too cryptic names, and overall half of instructions
I'm sure (I hope?) makes total sense from hardware perspective, but not so much from a "typical programmer"
perspective. Or maybe that's just me.

Anyway, I added a tiny [`float4` struct & helpers](https://github.com/aras-p/ToyPathTracer/blob/08-simd/Cpp/Source/MathSimd.h#L17)
to help my eyes a bit. So that I can write `float4` and get the `__m128` underneath, or likewise, just "add"
two float4s and have that turn into an `_mm_add_ps`, and so on.


### HitSpheres with SSE

Without further ado, my `HitSpheres` function with SSE [**implementation is here**](https://github.com/aras-p/ToyPathTracer/blob/08-simd/Cpp/Source/Maths.cpp#L50).
It's very likely that it has some, *errr*, "not very efficient" things in there, please do let me know!

SSE version comes out at ~100 lines, compared with [~40 lines](https://github.com/aras-p/ToyPathTracer/blob/08-simd/Cpp/Source/Maths.cpp#L162) for C++ one.
So it's quite a bit more verbose, and somewhat less readable (for me right now...), but not terribly
cryptic. Here's what it does, step by step, with some non-essential parts skipped.

We'll be doing intersection checking of one ray against all spheres, 4 spheres at a time.
In the main loop of the function, we'll load data for 4 spheres into SSE `float4` type variables,
and do intersection checks against the ray; with ray data duplicated ("splatted") identically
into all 4 lanes of `float4` variables. At the end, we'll have up to 4 intersection results
and will pick closest one.

Prepare data for the ray and min/max distances. Duplicate into 4-wide variables:
```c++
float4 rOrigX = SHUFFLE4(r.orig, 0, 0, 0, 0);
float4 rOrigY = SHUFFLE4(r.orig, 1, 1, 1, 1);
float4 rOrigZ = SHUFFLE4(r.orig, 2, 2, 2, 2);
float4 rDirX = SHUFFLE4(r.dir, 0, 0, 0, 0);
float4 rDirY = SHUFFLE4(r.dir, 1, 1, 1, 1);
float4 rDirZ = SHUFFLE4(r.dir, 2, 2, 2, 2);
float4 tMin4 = float4(tMin);
float4 tMax4 = float4(tMax);
```
We'll be storing current closest hit (position, normal, `t` value, sphere index) for each "lane"
here. Sphere index is directly in `__m128i` type since I don't have something like an `int4`
helper struct.
```c++
float4 hitPosX, hitPosY, hitPosZ;
float4 hitNorX, hitNorY, hitNorZ;
float4 hitT = float4(tMax);
__m128i id = _mm_set1_epi32(-1);
```
The loop goes over all spheres, 4 at a time. The calling code already makes sure that if total
amount of spheres is not a multiple by 4, then extra "fake" entries are present, with "impossible data"
(zero radius etc.). We can just go over them and the ray will never hit them.
At start of loop, I just load center & squared radius for 4 spheres. Right now my "load"
implementation uses [unaligned load](https://software.intel.com/sites/landingpage/IntrinsicsGuide/#text=_mm_loadu_ps&expand=3153);
I should perhaps switch to aligned instead.
```c++
for (int i = 0; i < spheres.simdCount; i += kSimdWidth)
{
    // load data for 4 spheres
    float4 sCenterX = float4(spheres.centerX + i);
    float4 sCenterY = float4(spheres.centerY + i);
    float4 sCenterZ = float4(spheres.centerZ + i);
    float4 sSqRadius = float4(spheres.sqRadius + i);
```
Next up is basic math, does exactly what it says; just for 4 spheres at once:
```c++
float4 ocX = rOrigX - sCenterX;
float4 ocY = rOrigY - sCenterY;
float4 ocZ = rOrigZ - sCenterZ;
float4 b = ocX * rDirX + ocY * rDirY + ocZ * rDirZ;
float4 c = ocX * ocX + ocY * ocY + ocZ * ocZ - sSqRadius;
float4 discr = b * b - c;
```
Now comes a branch that says "is discriminant for any of 4 spheres positive?":
```c++
bool4 discrPos = discr > float4(0.0f);
// if ray hits any of the 4 spheres
if (any(discrPos))
{
```
> In SIMD programming, quite similar to GPU shader programming, it's common to use branch-less code,
> i.e. compute both sides of some check, and "select" one or another based on a condition.
> Branches *are* possible of course, and can be beneficial when all or most "lanes" tend to take the same
> side of the branch, or if it allows saving a large amount of calculations.

Here, `bool4` is actually exactly the same as `float4`; it holds 128 bits worth of data. Comparison operator
([`_mm_cmpgt_ps` instruction](https://software.intel.com/sites/landingpage/IntrinsicsGuide/#text=_mm_cmpgt_ps&expand=927))
sets all bits of a 32-bit "lane" to 1 or 0 accordingly. `any` is implemented via
[`_mm_movemask_ps` instruction](https://software.intel.com/sites/landingpage/IntrinsicsGuide/#text=_mm_movemask_ps&expand=3614),
which returns a regular integer with a bit for every highest bit of an SSE register lane. If that returns
non-zero, it means that *some* of the four spheres have a positive discriminant here. Otherwise, none
of the spheres are hit by this ray, and we can move onto the next batch of 4 spheres.

Next up is computing `t` values for two possible ray-sphere intersection points (remember: for 4 spheres at once),
and checking which ones of those satisfy conditions. The conditions are:

* Must have had a positive discriminant earlier (we are executing this code if *any* sphere in a batch
  intersects a ray, but some of them might not),
* `t` must be larger than minimum distance passed as this function argument,
* `t` must be smaller than maximum distance (passed as argument, and kept on decreasing inside this function
  as intersections are found)  

```c++
// ray could hit spheres at t0 & t1
float4 t0 = -b - discrSq;
float4 t1 = -b + discrSq;
bool4 msk0 = discrPos & (t0 > tMin4) & (t0 < tMax4);
bool4 msk1 = discrPos & (t1 > tMin4) & (t1 < tMax4);
```
Now we have a "mask" (all bits set) for which intersections are "good" at `t0` (mask `msk0`), and similar for `t1`.
We need the closer "good" one, so whenever `t0` is "good" we should take that, otherwise take `t1`. Recall
that "SIMD prefers branch-less select-style programming"? This is our first occurrence of that, essentially doing
a `t = msk0 ? t0 : t1`, for each of the four lanes. And at the end the final "good" intersections
were the ones where either `t0` or `t1` was suitable; a union of the masks achieves that.
```c++
// where sphere is hit at t0, take that; elsewhere take t1 hit
float4 t = select(t1, t0, msk0);
bool4 msk = msk0 | msk1;
```
> Given that the `result = mask ? this : that` type of operation seems to be very common
> in SIMD programming, you might think that SSE would have a built-in instruction for that.
> But noooo, it took all until SSE4.1 to add the
> [`_mm_blend_ps` instruction](https://software.intel.com/sites/landingpage/IntrinsicsGuide/#text=_mm_blend_ps&expand=427).
> If you need to target earlier CPUs, you have to do [funky bit logic dance](https://github.com/aras-p/ToyPathTracer/blob/08-simd/Cpp/Source/MathSimd.h#L63)
> to achieve the same result. "[SSE: mind the gap!](https://fgiesen.wordpress.com/2016/04/03/sse-mind-the-gap/)"
> by Fabian Giesen has this and a lot more tricks to deal with SSE oddities.

Next up, if any sphere got actually hit, we compute the intersection point, normal and so on,
and update our "best so far" (four of them) variables with that, using `select` with a mask:
```c++
if (any(msk))
{
    // compute intersection at t (id, position, normal), and overwrite current best
    // results based on mask.
    // also move tMax to current intersection
    id = select(id, curId, msk);
    tMax4 = select(tMax4, t, msk);
    float4 posX = rOrigX + rDirX * t;
    float4 posY = rOrigY + rDirY * t;
    float4 posZ = rOrigZ + rDirZ * t;
    hitPosX = select(hitPosX, posX, msk);
    // ...similar for other hitFoo variables
```

That's the "check all spheres loop" done. Now after the loop, we're left with up to 4 sphere intersections,
and have to pick closest one. This is the part where **I'm sure my "solution" is all kinds of suboptimal**,
suggestions welcome :) I suspect it's suboptimal because: 1) it's a lot of repetitive code, and
2) it's a lot of scalar-looking and branchy-looking code too.

Out of four intersection `t` values, find the smallest one via a "horizontal minimum" helper:
```c++
float minT = hmin(hitT);
```
How is `hmin` implemented? To find minimum in an N-wide SSE variable, we need to do
logN steps of shuffling and "minimum" operation. With SSE, width is 4, so minimum can be found in
two shuffles and two `min`.
```c++
float hmin(float4 v)
{
    v = min(v, SHUFFLE4(v, 2, 3, 0, 0));
    v = min(v, SHUFFLE4(v, 1, 0, 0, 0));
    return v.getX();
}
```
And then once I know the minimum `t` distance out of all four results, I just check each of them
one by one, *"was this the minimum? extract and return data if so"*, e.g. for the first lane:
```c++
if (hitT.getX() == minT)
{
    outHit.pos = float3(hitPosX.getX(), hitPosY.getX(), hitPosZ.getX());
    outHit.normal = float3(hitNorX.getX(), hitNorY.getX(), hitNorZ.getX());
    outHit.t = hitT.getX();
    return (int16_t)_mm_extract_epi16(id, 0);
}
```
The second lane is exactly the same, just `getY` component is used, etc. Repeat for all four SSE
lanes.

*...and that's the SSE implementation of HitSpheres!*

> Note that I could have used no branches inside the loop at all; just do everything with masked selects. In
> my test having the branches there is actually a bit faster, so I left them in.


### Ok, what's the performance?

* PC (AMD ThreadRipper, 16 threads): 186 -> 194 Mray/s.
* Mac (MacBookPro, 8 threads): 49.8 -> 55.2 Mray/s.

Hmpft. It is faster, but not awesomely faster. What's going on? Two things:

1. HitSpheres is not *all* the work that the path tracer is doing; just a part of it,
1. We only have 9 spheres in the whole scene. All this effort to process four spheres
   at once, when in total there's only nine of them... yeah.

### Time for a larger scene then!

[{{<img src="/img/blog/2018/rt-simd-bigscene.jpg">}}](/img/blog/2018/rt-simd-bigscene.jpg)

46 spheres now, with two of them being light-emitting surfaces. I know, that's not a
"properly complex" scene, but on the other hand, I don't have any ray intersection
acceleration structure either; each ray is checking *all* the spheres.

With this larger scene, here are some updated numbers:

* This post, HitSpheres with SSE: **PC 107, Mac 30.1 Mray/s**.
* [Previous post](/blog/2018/04/10/Daily-Pathtracer-Part-7-Initial-SIMD/):
  * HitSheres with SoA layout: PC 78.8, Mac 17.4 Mray/s.
  * Before any SIMD/SoA stuff: PC 48.0, Mac 12.3 Mray/s.

So this & previous post combined, by optimizing only "ray against N spheres" part, so far
got **2-2.5x total speedup**. Not too bad!

For reference, (still unoptimized at all) GPU compute shader implementation on this larger scene:

* PC GeForce 1080 Ti, DX11: 648 Mray/s,
* Mac Intel Iris Pro, Metal: 41.8 Mray/s.

*...and that's it for today :)* The above changes are in [this PR](https://github.com/aras-p/ToyPathTracer/pull/9/commits),
or at [`08-simd` tag](https://github.com/aras-p/ToyPathTracer/tree/08-simd/Cpp/Source).



### What's next

I have done a "one ray vs N spheres" SIMD with SSE part. Quite likely that's not the best approach
*(fairly isolated though: just one function)*. Doing "N rays" type of SIMD might make more
sense for performance.

So maybe that's next, or maybe I'll look into a "stream/buffer" oriented setup instead. Or [neither of the
above](/blog/2018/04/13/Daily-Pathtracer-9-A-wild-ryg-appears/) :) Stay tuned!
