---
title: "A year in Blender VSE land"
date: 2024-11-21T15:35:10+03:00
tags: ['blender', 'code', 'performance']
---

Turns out, now is exactly one year of me working on the video sequence editor (VSE).

Going pretty well so far! What I managed to put into Blender [4.1](/blog/2024/02/06/I-accidentally-Blender-VSE/) and
[4.2](/blog/2024/07/22/More-Blender-VSE-stuff-for-4.2/) is in the previous blog posts.
Blender [4.3](https://www.blender.org/download/releases/4-3/) has just shipped, and everything related to
Video Sequence Editor is [listed on this page](https://developer.blender.org/docs/release_notes/4.3/sequencer/).
Items related to performance or thumbnails are my doing.

Some of the work I happened to do *for* VSE over this past year ended up improving other areas of Blender. E.g.
video rendering improvements are useful for anyone who renders videos; or image scaling/filtering improvements
are beneficial in other places as well. So that's pretty cool!


### Google Summer of Code

The main user-visible workflow changes in 4.3 VSE ("connected" strips, and preview area snapping) were done
by John Kiril Swenson as part of Google Summer of Code, see
[his report blog post](https://kirilswenson.netlify.app/posts/blender-vse). I was "mentoring" the project, but
that was surprisingly easy and things went very smoothly. Not much more to say, except that the project was
successful, and the result is actually shipping now as part of Blender. Nice!


### Sequencer workshop at Blender HQ

In 2024 August some of us had a "VSE Workshop" at the Blender office in Amsterdam. Besides geeking out on
some technical details, most of discussion was about high level workflows, which is not exactly my area
(I can implement an existing design, or fix some issues, but doing actual UI or UX work I'm the least
suitable person for).

But! It was very nice to hear all the discussions, and to see people face to face, at last. Almost five years
of working from home is mostly nice, but once in a while getting out of the house is also nice.

[{{<img src="/img/blog/2024/vse_workshop.jpg">}}](/img/blog/2024/vse_workshop.jpg)

There's a [short blog post](https://code.blender.org/2024/08/vse-workshop-august-2024/) and a more
detailed [report thread](https://devtalk.blender.org/t/2024-08-21-vse-workshop/36373) about the workshop
on Blender website/forum.

Surprising no one, what became clear is that the amount of *possible work* on the video editing tools is
way more than the amount of people and the amount of time they can spend implementing them. Like, right now
there's maybe... 1.5 people actually working on it? (my math: three people, part-time).
So while Blender [4.1](https://developer.blender.org/docs/release_notes/4.1/sequencer/),
[4.2](https://developer.blender.org/docs/release_notes/4.2/sequencer/) and
[4.3](https://developer.blender.org/docs/release_notes/4.3/sequencer/) all have VSE
improvements, no "hey magically it is now
better than Resolve / Premiere / Final Cut Pro" moments anytime soon :)

A side effect of the workshop: I got to cuddle Ton's dog Bowie, and saw Sergey's frog collection, including this
most excellent güiro: \
[{{<img src="/img/blog/2024/vse_workshop_bowie.jpg" width="47%">}}](/img/blog/2024/vse_workshop_bowie.jpg)
[{{<img src="/img/blog/2024/vse_workshop_guiro.jpg" width="47%">}}](/img/blog/2024/vse_workshop_guiro.jpg)


### Blender Conference 2024

I gave a short talk at BCON'24, "How to accidentally start working on VSE". It was not so much about 
VSE per se, but more about "how to start working in a new area". Vibing off the whole conference theme
which was "building Blender".

Here's **[slides for it (pdf)](https://aras-p.info/texts/img/2024-bcon-vse.pdf)** and the recording:
{{< youtube id="WJVQLpGHB8g" >}}

The whole conference was lovely. All the talks are in [this playlist](https://www.youtube.com/playlist?list=PLa1F2ddGya_-Ymw4YlOjqrdQxiRMJql5x),
and overall feeling is well captured in the [BCON'24 recap video](https://www.youtube.com/watch?v=6iTmxeQUAgA).


### What's Next

Blender 4.4 development is happening as we speak, and VSE already got [some stuffs done for it](https://developer.blender.org/docs/release_notes/4.4/sequencer/).
For this release, so far:

* Video improvements: H.265/HEVC support, 10- and 12-bit videos. Some colorspace and general color precision shenanigans.
* Proxy improvements: proxies for EXR images work properly now, and are faster to build. There's a ton of possible 
  improvements for video proxies, but not sure how much of that I'll manage to squeeze into 4.4 release.

Generally, just like this whole past year, I'm doing things without much planning. *Stochastic development!* Yay!
