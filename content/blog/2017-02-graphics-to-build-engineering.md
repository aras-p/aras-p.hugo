+++
tags = ['work', 'unity']
comments = true
date = "2017-02-26T09:53:21-08:00"
title = "Stopping graphics, going to build engineering"
+++

I'm doing a sideways career move. Which is: stopping whatever graphics related
programming I was doing, and start working on internal build engineering. Been somewhat removing myself
from many graphics related areas (ownership, code reviews, future tasks, decisions & discussions) for a while
now, and right now GDC provides a conceptual break between graphics and non-graphics work areas.

Also, I can go into every graphics related GDC talk, sit there at the back and shout *"booo, graphics sucks!"* or
something.

*"But why?"* - several reasons, with major one being "why not?". In random order:

* I wanted to "change something" for a while, and this does qualify as that. I was mostly doing graphics
  relate things for what, 11 years by now at the same company? That's a long time!
* I wanted to try myself in an area where I'm a complete newbie, and have to learn everything
  from scratch. In graphics, while I'm nowhere near being "leading edge" or having _actual_ knowledge, at least I have a pretty good mental picture
  of current problems, solutions, approaches and what is generally going on out there. And I know the buzzwords!
  In build systems, I'm Jon Snow. I want to find out how that is and how to deal with it.
* This one's a bit counter-intuitive... but I wanted to work in an area where there are
  three hundred customers instead of five million *(or whatever is the latest number)*. Having an extremely widely
  used product is often inspiring, but also can be tiring at times.
* Improving ease of use, robustness, reliability and performance of our own internal
  build system(s) does sound like a useful job! It's something all the developers here do
  many times per day, and there's no shortage of improvements to do.
* Graphics teams at Unity right now are in better state than ever before, with good
  structure, teamwork, plans and
  efficiency in place. So me leaving them is not a big deal at all.
* The build systems / internal developer tooling team did happen to be looking for some
  helping hands at the time. Now, they probably don't know what they signed up for by
  accepting me... but we'll see :)

I'm at GDC right now, and was looking for relevant talks about build/content/data pipelines. There
are a couple, but actually not as much as I hoped for... That's a shame! For example last year
[Rémi Quenin's](https://twitter.com/azagoth) talk on
[Far Cry 4 pipeline](http://www.gdcvault.com/play/1021976/Fast-Iteration-for-Far-Cry) was amazing.

What will my daily work be about, I still have no good idea. I suspect it will be things like:

* Working on our own build system (we were on [JamPlus](http://jamplus.org/) for a long time, and
  replacing pieces of it).
* Improving reliability of build scripts / rules.
* Optimizing build times for local developer machines, both for full builds as well as incremental builds.
* Optimizing build times for the build farm.
* Fixing annoyances in current builds (there's plenty of random ones, e.g. if you build a 32 bit version of
  something, it's not easy to build 64 bit version without wiping out some artifacts in between).
* Improving build related IDE experiences (project generation, etc.).

Anyhoo, so that's it. I expect future blog posts here might be build systems related.

*Now, build all the things! Picture unrelated.*

[{{<img src="/img/blog/2017-02/build-all-things.jpg">}}](/img/blog/2017-02/build-all-things.jpg)

