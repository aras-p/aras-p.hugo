---
title: "Crank the World: Playdate demo"
date: 2024-05-20T20:18:10+03:00
tags: ['demos']
comments: true
---

You know [Playdate](https://play.date/), the cute yellow console with a crank? I think I saw it in person last
year via [Inês](https://www.inesalmeida.com/), and early this year they started to have Lithuania as a shipping
destination, so I got one. And then what would I do with it? Of course, try to make a demoscene demo :)

### First impressions

The device is cute. The [SDK](https://play.date/dev/) is simple and no-nonsense ([see docs](https://sdk.play.date/inside-playdate-with-c)).
I only used the C part of SDK
(there's also Lua, which I did not use). You install it, as well as the official gcc based toolchain from ARM,
and there you go. SDK provides simple things like "here's the bitmap for the screen" or "here's which buttons
are pressed" and so on.

The hardware underneath *feels* similar to "about the first Pentiums" era - single core CPU at 180MHz
(ARM Cortex-M7F), with hardware floating point (but no VFP/NEON), 16MB RAM, there's no GPU. Screen is 400x240 pixels,
1 bit/pixel -- kinda Kindle e-ink like, except it refreshes *way* faster (can go up to 50 FPS). Underlying operating
system seems to be [FreeRTOS](https://en.wikipedia.org/wiki/FreeRTOS) but nothing about it is exposed directly;
you just get what the SDK provides.

At first I tried checking out how many polygons can the device rasterize while keeping 30 FPS:
{{<video src="/img/blog/2024/cranktheworld-playdate.mp4" width="300px">}}

But in the end, going along with wise words of [kewlers and mfx](https://www.youtube.com/watch?v=2mtctbodNXY),
the demo chose to use zero polys (and... zero shaders).

Packaging up the "final executable" of the demo felt like a breath of fresh air. You just... zip up the folder. That's it.
And then *anyone* with a device can sideload it from anywhere. At first I could not believe that it would actually
work, without some sort of dark magic ritual that keeps on changing every two months. Very nice.

> By the way, check out the
> "[The Playdate Story: What Was it Like to Make Handheld Video Game System Hardware?](https://gdcvault.com/play/1034707/The-Playdate-Story-What-Was)"
> talk by Cabel Sasser from GDC 2024. It is most excellent.

### The demo

I wanted to code some oldskool demo effects that I never did back when 30 years ago everyone else was doing them.
You know: plasma, kefren bars, blobs, starfield, etc.

Also wanted to check out how much of shadertoy-like raymarching could a Playdate do. Turns out, "not a lot", lol.

And so the demo is just that: a collection of random scenes with some "effects". Music is an old GarageBand experiment
that my kid did some years ago.

{{< youtube id="QjAKiwQxrQI" >}}

<p></p>

* **Video**: device playing it ([youtube](https://www.youtube.com/watch?v=QjAKiwQxrQI) / [mp4 file](/files/demos/2024/Nesnausk_CrankTheWorld_20240421.mp4)), just the screen ([youtube](https://www.youtube.com/watch?v=3NjHOVtTPjY) / [mp4 file](/files/demos/2024/Nesnausk_CrankTheWorld_screen20240421.mp4))
* **Playdate build**: [Nesnausk_CrankTheWorld.pdx.zip](/files/demos/2024/Nesnausk_CrankTheWorld-20240421.zip) (3MB), should also work in Playdate Simulator on Windows.
* **pouët**: https://www.pouet.net/prod.php?which=96955
* **Source code**: https://github.com/aras-p/demo-pd-cranktheworld
* Took 4th place at [Outline 2024](https://outlinedemoparty.nl/) "Newskool Demo" category.
* Credits: everything except music -- NeARAZ, music -- stalas001.


[{{<img src="/img/blog/2024/cranktheworld-01.png" width="365px">}}](/img/blog/2024/cranktheworld-01.png)
[{{<img src="/img/blog/2024/cranktheworld-02.png" width="365px">}}](/img/blog/2024/cranktheworld-02.png)
[{{<img src="/img/blog/2024/cranktheworld-03.png" width="365px">}}](/img/blog/2024/cranktheworld-03.png)
[{{<img src="/img/blog/2024/cranktheworld-04.png" width="365px">}}](/img/blog/2024/cranktheworld-04.png)
[{{<img src="/img/blog/2024/cranktheworld-05.jpg" width="365px">}}](/img/blog/2024/cranktheworld-05.jpg)
[{{<img src="/img/blog/2024/cranktheworld-06.jpg" width="365px">}}](/img/blog/2024/cranktheworld-06.jpg)

### Tech bits

Playdate uses 1 bit/pixel screen, so to represent "shades of gray" for 3D effects I went the simple way
and just used a static screen-size blue noise texture (from [here](https://github.com/Calinou/free-blue-noise-textures)).
So "the code" produces a screen-sized "grayscale" image with one byte per pixel, and then it is dithered through
the blue noise texture into the device screen bitmap. It works way better than I thought it would! \
[{{<img src="/img/blog/2024/cranktheworld-bluenoise.png">}}](/img/blog/2024/cranktheworld-bluenoise.png)

All the raymarched/raytraced scenes are way too slow to calculate each pixel every frame (too slow
with my code, that is). So instead, calculate only every Nth pixel each frame, with update pattern similar to ordered
dithering tables.
- Raytraced spheres: update 1 out of 6 pixels every frame (in 3x2 pattern),
- Raymarched tunnel/sponge/field: update 1 out of 4 pixels every frame (in 2x2 pattern), *and*
  run everything at 2x2 lower resolution too, "upscaling" the rendered grayscale image before
  dithering. So effectively, raymarching 1/16th the amount of screen pixels each frame.
- Other simpler scenes: update 1 out of 4 pixels every frame.

You say "cheating", I say "free motion blur" or "look, this is a spatial and temporal filter just like DLSS, right?" :)

For the raymarched parts, I tried to make them "look like something" while keeping the number of march iterations *very*
limited, and doing other cheats like using too large ray step size which leads to artifacts but hey no one knows
what is it supposed to look like anyway.

All in all, most of the demo runs at 30 FPS, with some parts dropping to about 24 FPS.

Size breakdown: demo is 3.1MB in size, out of which 3MB is the music track :) And that is because it is just
an ADPCM WAV file. The CPU cost of doing something like MP3 playback was too high, and I did not go the MIDI/MOD/XM
route since the music track comes from GarageBand.

Some of the scenes/effects are ~~ripped off~~ *inspired* by other shadertoys or demos:
- [twisty cuby](https://www.shadertoy.com/view/MtdyWj) by DJDoomz
- [Ring Twister](https://www.shadertoy.com/view/Xt23z3) by Flyguy
- [Pretty Hip](https://www.shadertoy.com/view/XsBfRW) by Fabrice Neyret
- [Xor Towers](https://www.shadertoy.com/view/7lsXR2) by Greg Rostami
- [Menger Sponge Variation](https://www.shadertoy.com/view/ldyGWm) by Shane
- [Puls](https://www.pouet.net/prod.php?which=53816) by Řrřola

When the music finishes, the demo switches to "interactive mode" where you can switch between the effects/scenes with
Playdate A/B buttons. You can also use the crank to orbit/rotate the camera or change some other scene parameter.
Actually, you can use the crank to control the camera during the regular demo playback as well.

*All in all, this was quite a lot of fun! Maybe I should make another demo sometime*.
