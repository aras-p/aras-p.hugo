---
title: "More Blender VSE stuff for 4.2"
date: 2024-07-22T19:45:10+03:00
tags: ['blender', 'code', 'performance']
---

*I did a bunch of work for [Blender 4.1 video sequence editor](/blog/2024/02/06/I-accidentally-Blender-VSE/),
and since no one revoked my commit access, I continued in the same area for Blender 4.2.
Are you one of the approximately seven Blender VSE users? Read on then!*

Blender [4.2](https://www.blender.org/download/releases/4-2/) has just shipped, and everything related to Video Sequence
Editor is [listed on this page](https://developer.blender.org/docs/release_notes/4.2/sequencer/). Probably half of
that is my ~~fault~~ *work*, and since 4.2 is a long term support (LTS) release, this means I'll have to fix any
bugs or issues about that for three more years, yay :)

### Visual updates, and rendering strip widgets in a shader

What started out as a casual "*hey could we have rounded corners?*" question in some chat or a design task, snowballed into,
well, rounded corners. Corner rounding for UI controls and widgets is so ubiquitous these days, that it *feels* like
it is a trivial thing to do. And it would... if you had a full vector graphics renderer with anti-aliased clipping/masking
lying around. But I do not.

The VSE timeline "widgets" in Blender 4.1 and earlier are pretty much just "rectangles and lines". The "widget control"
is surprisingly complex and can have many parts in there -- besides the obvious ones like image thumbnail, audio waveform
or title text, there's background, color tag overlay, animation curve (volume or transition) overlay, fade transition triangle,
retiming keys, transformation handles, meta strip content rectangles, "locked" diagonal stripes and some others.
Here's a test timeline showing most of the possible options, in Blender 4.1: \
[{{<img src="/img/blog/2024/blender_vse_widgets_41.png">}}](/img/blog/2024/blender_vse_widgets_41.png)

Thumbnails, waveforms, curves, locked stripes and texts are drawn in their own ways, but everything else is pretty much just
a bunch of "blend a semitransparent rectangle" or "blend a semitransparent line".

How do you make "rounded corners" then? Well, "a rectangle" would need to gain rounded corners in *some way*. You could do that
by replacing rectangle (two triangles) with a more complex geometry shape, but then you also want the rounded corners to
be nicely anti-aliased. What is more complicated, is that you want "everything else" to get rounded too (e.g. strip outline or
border), or masked by the rounded corner (e.g. diagonal stripes to indicate "locked" state, or the image thumbnail should not
"leak" outside of the rounded shape).

Another way to do all of this, ever since Inigo Quilez popularized [Signed Distance Fields](https://iquilezles.org/articles/distfunctions2d/),
would be to draw each widget as a simple rectangle, and do all the "rounded corners" evaluation, masking and anti-aliasing inside
a shader. I wanted to play around with moving all (or most) of strip "widget" into a dedicated shader for a while, and so this was
an excuse to do exactly that. The process looked like this:

1. Stall and procrastinate for a month, thinking how scary it will be to untangle all the current VSE widget drawing code and move
   that into a shader, *somehow*. I kept on postponing or finding excuses to not do it for a long time.
1. Actually try to do it, and turns out that was only a day of work ([#122576](https://projects.blender.org/blender/blender/pulls/122576)).
   Easy!
1. Spend the next month making a dozen more pull requests
([#122764](https://projects.blender.org/blender/blender/pulls/122764), 
 [#122890](https://projects.blender.org/blender/blender/pulls/122890), 
 [#123013](https://projects.blender.org/blender/blender/pulls/123013), 
 [#123065](https://projects.blender.org/blender/blender/pulls/123065), 
 [#123119](https://projects.blender.org/blender/blender/pulls/123119), 
 [#123221](https://projects.blender.org/blender/blender/pulls/123221), 
 [#123369](https://projects.blender.org/blender/blender/pulls/123369), 
 [#123391](https://projects.blender.org/blender/blender/pulls/123391), 
 [#123431](https://projects.blender.org/blender/blender/pulls/123431), 
 [#123515](https://projects.blender.org/blender/blender/pulls/123515), 
 [#124210](https://projects.blender.org/blender/blender/pulls/124210), 
 [#124965](https://projects.blender.org/blender/blender/pulls/124965), 
 [#125220](https://projects.blender.org/blender/blender/pulls/125220)),
 making the rounded corners *actually work*. Some of these were fixes
(snapping to pixel grid, DPI awareness, precision issues), some were design and visual look tweaks.

All of that, together with various other design and strip handle UX updates done by Richard Antalik and Pablo Vazquez, resulted
in Blender 4.2 VSE looking like this now: \
[{{<img src="/img/blog/2024/blender_vse_widgets_42.png">}}](/img/blog/2024/blender_vse_widgets_42.png)

I've also implemented visual indication of missing media (strips that point to non-existing image/movie/sound files)
[#116869](https://projects.blender.org/blender/blender/pulls/116869); it can be seen in the screenshot above too.


### Text shadows & outlines

Text strips in VSE had an option for a drop-shadow, but it was always at fixed distance and angle from the text,
making it not very useful in general case. I made the distance and angle configurable, as well as added shadow
blur option. While at it, text also got an outline option ([#121478](https://projects.blender.org/blender/blender/pulls/121478)). \
[{{<img src="/img/blog/2024/blender_vse42_text_shadow.png">}}](/img/blog/2024/blender_vse42_text_shadow.png)
[{{<img src="/img/blog/2024/blender_vse42_text_outline.png">}}](/img/blog/2024/blender_vse42_text_outline.png)

Outlines are implemented with Jump-Flooding algorithm, as wonderfully described by Ben Golus in
"[The Quest for Very Wide Outlines](https://bgolus.medium.com/the-quest-for-very-wide-outlines-ba82ed442cd9)" blog post.



### Performance

While Blender 4.1 brought many and large performance improvements to VSE, the 4.2 release is not so big. There
is some performance work however:

"**Occlusion culling**" for opaque images/movies ([#118396](https://projects.blender.org/blender/blender/pulls/118396)).
VSE already had an optimization where a strip that is known to be fully opaque and covers the whole screen, stops
processing of all strips below it (since they would not be visible anyway). Now the same optimization happens for
some cases of strips that do not cover the whole screen: when a fully opaque strip completely covers some strip that
is under it, the lower strip is not evaluated/rendered.

The typical case is letterboxed content: there's black background that covers the whole screen, but then the actual
"content" only covers a smaller area. On [Gold](https://studio.blender.org/films/gold/) previs, this saved
about 7% of total render time. Not much, but still something.

**Optimize parts of ffmpeg movie decoding**

* Cache ffmpeg `libswscale` color conversion contexts ([#118130](https://projects.blender.org/blender/blender/pulls/118130)).
  Before this change, each individual movie strip would create a "color conversion context" object, that is mostly used
  to do YUV↔RGB conversion. However, most of them end up being exactly the same, so have a pool of them and reuse
  them as needed.
* Stop doing some redundant work when starting movie strip playback ([#118503](https://projects.blender.org/blender/blender/pulls/118503)).
  Not only this made things faster, but also removed about 200 lines of code. Win-win!
* *(not performance per se, but eh)* Remove non-ffmpeg AVI support ([#118409](https://projects.blender.org/blender/blender/pulls/118409)).
  Blender had *very limited* `.avi` video support that does not go through [ffmpeg](https://www.ffmpeg.org/). Usefulness of that
  was highly questionable, and mostly a historical artifact. Poof, now it is gone, along with 3600 lines of code. 🎉


### What's Next

I investigated support for 10/12 bpp and HDR videos, but did not get anything finished in time for Blender 4.2. It does not help
that I know nothing about video codecs or color science lol :) But maybe I should continue on that.

The VSE timeline drawing has obvious "would be nice to finish" parts, some of which would address performance issues too. Right now
*most* of strip control is drawn inside a dedicated GPU shader, but there are bits that are still drawn separately (thumbnails, audio
waveforms, meta strip contents, animation curve overlays). Getting them to be drawn inside the same shader would (or could?)
make CPU side work much simpler.

VSE feature within Blender overall could benefit from a thousand small improvements, but also perhaps the relevant people
should discuss what is the bigger picture and actual plans for it. It is good to continue doing random incremental improvements,
but once in a while discussing and deciding "where exactly we want to end up at?" is also useful. Maybe we should do that soon.

That's it!
