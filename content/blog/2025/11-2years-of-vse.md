---
title: "Two years of Blender VSE"
date: 2025-11-23T13:35:10+03:00
tags: ['blender']
---

> So, [Blender 5.0](https://www.blender.org/download/releases/5-0/) has shipped while
I was away at the excellent [Graphics Programming Conference](https://graphicsprogrammingconference.com/),
but while all that was happening, I realized it has been two years since I mostly
work on the Blender Video Sequence Editor (VSE). Perhaps not surprisingly,
a year ago it was [one year of that](/blog/2024/11/21/A-year-in-Blender-VSE-land/) :)

Just like [two years ago when I started](/blog/2024/02/06/I-accidentally-Blender-VSE/),
I am still mostly flailing my arms around, without realizing what I'm actually doing.

### The good

It *feels* like recently VSE did get quite many improvements across workflow,
user experience and performance. The first one I contributed anything to was Blender 4.1,
and look what has happened since then (pasting screenshots of the release overview
pages):

4.1 ([full notes](https://developer.blender.org/docs/release_notes/4.1/sequencer/)): <br/>
[{{<img src="/img/blog/2025/vse2year_41a.png" width="320px">}}](/img/blog/2025/vse2year_41a.png)
[{{<img src="/img/blog/2025/vse2year_41b.png" width="320px">}}](/img/blog/2025/vse2year_41b.png)

4.2 ([full notes](https://developer.blender.org/docs/release_notes/4.2/sequencer/)): <br/>
[{{<img src="/img/blog/2025/vse2year_42a.png" width="365px">}}](/img/blog/2025/vse2year_42a.png)
[{{<img src="/img/blog/2025/vse2year_42b.png" width="275px">}}](/img/blog/2025/vse2year_42b.png)

4.3 ([full notes](https://developer.blender.org/docs/release_notes/4.3/sequencer/)): <br/>
[{{<img src="/img/blog/2025/vse2year_43a.png" width="255px">}}](/img/blog/2025/vse2year_43a.png)
[{{<img src="/img/blog/2025/vse2year_43b.png" width="385px">}}](/img/blog/2025/vse2year_43b.png)

4.4 ([full notes](https://developer.blender.org/docs/release_notes/4.4/sequencer/)):<br/>
[{{<img src="/img/blog/2025/vse2year_44a.png" width="295px">}}](/img/blog/2025/vse2year_44a.png)
[{{<img src="/img/blog/2025/vse2year_44b.png" width="345px">}}](/img/blog/2025/vse2year_44b.png)

4.5 ([full notes](https://developer.blender.org/docs/release_notes/4.5/sequencer/)): <br/>
[{{<img src="/img/blog/2025/vse2year_45.png" width="360px">}}](/img/blog/2025/vse2year_45.png)

5.0 ([full notes](https://developer.blender.org/docs/release_notes/5.0/sequencer/)): <br/>
[{{<img src="/img/blog/2025/vse2year_50a.png" width="270px">}}](/img/blog/2025/vse2year_50a.png)
[{{<img src="/img/blog/2025/vse2year_50b.png" width="370px">}}](/img/blog/2025/vse2year_50b.png)
[{{<img src="/img/blog/2025/vse2year_50c.png" width="280px">}}](/img/blog/2025/vse2year_50c.png)
[{{<img src="/img/blog/2025/vse2year_50d.png" width="360px">}}](/img/blog/2025/vse2year_50d.png)

In addition to user-facing features or optimizations, there also has been quite a lot
of code cleanups; too many to list individually but for a taste you could look at "winter of quality"
task list of last year ([#130975](https://projects.blender.org/blender/blender/issues/130975))
or WIP list of upcoming "winter of quality"
([#149160](https://projects.blender.org/blender/blender/issues/149160)).

All of this was done by 3-4 people, all of them working on VSE part time. That's not too
bad!

For upcoming year, we want to tackle three large items: 1) more compositor node-based things
(modifiers, effects, transitions) including more performance to them, 2) hardware acceleration
for video decoding/encoding, 3) workflows like media bins, media preview, three point editing.
That and more "wishlist" type of items is detailed in
[this devtalk thread](https://devtalk.blender.org/t/video-sequence-editor-vse-2026-roadmap/43206).

If you have tried Blender video editor a long time ago, and were not impressed, I suggest you try
it again! *You might still not be impressed, but then you would have learned to not trust
anything I say :P*

### The bad

It can't all be good; some terrible things have also happened in Blender VSE land too.
For one, I have became the "module owner" (i.e. "a lead") of the VSE related work. *Uh-oh!*

### The wishlist

From the current
"[things we'd want to work on](https://devtalk.blender.org/t/video-sequence-editor-vse-2026-roadmap/43206)",
an obvious lacking part is everything related to audio -- VSE has *some* audio functionality,
but nowhere near enough for a proper video editing toolbox. Currently out of "just, like, three"
part-time people working on VSE, no one is doing audio besides maintenance.

More community contributions in that area would be good. If you want to contribute, check out 
[new developer documentation](https://developer.blender.org/docs/handbook/new_developers/)
and `#module-sequencer` on the
[developer chat](https://developer.blender.org/docs/handbook/communication/chat/).
