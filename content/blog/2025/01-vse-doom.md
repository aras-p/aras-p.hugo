---
title: "Doom in Blender VSE"
date: 2025-01-17T12:18:10+03:00
tags: ['blender', 'random']
comments: true
---

You know how in Blender Video Sequence Editor (VSE) you can create Color strips,
and then their color is displayed in the timeline? <br/>
[{{<img src="/img/blog/2025/doom-vse-color1.png" width="315px">}}](/img/blog/2025/doom-vse-color1.png)
[{{<img src="/img/blog/2025/doom-vse-color2.png" width="300px">}}](/img/blog/2025/doom-vse-color2.png)

You can create many of them, and when sufficiently zoomed out, the strip headings disappear since there's
not enough space for the label: <br/>
[{{<img src="/img/blog/2025/doom-vse-color3.png" width="315px">}}](/img/blog/2025/doom-vse-color3.png)

So if you created say 80 columns and 60 rows of color strips...<br/>
[{{<img src="/img/blog/2025/doom-vse-color4.png" width="450px">}}](/img/blog/2025/doom-vse-color4.png)

...and kept on changing their colors constantly... you could run
[Doom](https://en.wikipedia.org/wiki/Doom_(1993_video_game)) inside the Blender VSE timeline.

*And so that's what I did*. Idea sparked after seeing someone make
[Doom run in Houdini COPs](https://www.youtube.com/watch?v=ufDxjXLeodw).

### Result

Here's the result: <br/>
{{< youtube id="Y2iDZjteMs8" >}}

And the file/code on github:
[**github.com/aras-p/blender-vse-doom**](https://github.com/aras-p/blender-vse-doom)

It is a modal blender operator that loads doom file, creates
VSE timeline full of color strips (80 columns, 60 rows), listens to
keyboard input for player control, renders doom frame and updates the
VSE color strip colors to match the rendered result. Escape key finishes
the operator.

All the Doom-specific heavy lifting is in `render.py`, written by
Mark Dufour and is completely unrelated to Blender. It is just a tiny
pure Python Doom loader/renderer. I took it from
"[Minimal DOOM WAD renderer](https://github.com/shedskin/shedskin/blob/6c30bbe617/examples/doom/render.py)"
and made two small edits to avoid division by zero exceptions that I was getting.


## Performance

This runs pretty slow (~3fps) in current Blender (4.1 .. 4.4) 😢

I noticed that is was slow when I was "running it", but when stopped, navigating the VSE
timeline with all the strips still there was buttery smooth. And so, being an idiot that I am,
I was "rah rah, Doom rendering is done in pure Python, *of course* it is slow!"

Yes, Python is slow, and yes, the minimal Doom renderer (in exactly 666 lines of code -- nice!)
is not written in "performant Python". But turns out... performance problems are *not there*.
Another case for "never guess, always look at what is going on".

The pure-python Doom renderer part takes 7 milliseconds to render a 80x60 "frame". Could it be
faster? Probably. But... it takes **300 milliseconds** to update the colors of all the VSE strips.

> Note that in Blender 4.0 or earlier it runs even slower, because redrawing the
> VSE timeline with 4800 strips takes about 100 milliseconds; that is no longer slow
> in (1-2ms) in later versions due to [what I did a year ago](blog/2024/02/06/I-accidentally-Blender-VSE/).

Why does it take 300 milliseconds to update the strip colors? For that of course
I brought up [Superluminal](https://superluminal.eu/) and it tells me the problem is cache
invalidation: <br/>
[{{<img src="/img/blog/2025/doom-vse-profile.png">}}](/img/blog/2025/doom-vse-profile.png)

*Luckily, cache invalidation is one of the easiest things in computer science, right?* 🧌

Anyway, this looks like another case of accidental quadratic complexity: for each strip that gets
a new color set on it, there's code that 1) invalidates any cached results for that strip (ok), and
2) tries to find whether this strip belongs to any meta-strips to invalidate those
(which scans *all the strips*), and 3) tries to find which strips intersect the strip horizontal range
(i.e. are "composited above it"), and invalidate partial results of those -- this again scans
*all the strips*.

Step 2 above can be easily addressed, I think, as the codebase already maintains data structures for
finding which strips are part of which meta-strips, without resorting to "look at *everything*".

Step 3 is slightly harder in the current code. However, half a year ago during
[VSE workshop](https://code.blender.org/2024/08/vse-workshop-august-2024/) we talked about how the
whole caching system within VSE is *maybe* too complexicated for no good reason.

Now that I think about it, *I think* most or all of that extra cost could be removed, if
Someone™️ would rewrite VSE cache to be along the lines of how we discussed at the workshop.

Hmm. Maybe I have some work to do. And then the VSE timeline could be properly *doomed*.

