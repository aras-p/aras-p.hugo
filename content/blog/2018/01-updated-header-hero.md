---
title: "Header Hero Improvements"
date: 2018-01-17T13:44:20+02:00
tags: ["code", "devtools"]
comments: true
---

There's a neat little tool for optimizing C++ codebase header `#include` dependencies:
**[Header Hero](http://bitsquid.blogspot.lt/2011/10/caring-by-sharing-header-hero.html)**
*(thanks [Niklas](https://twitter.com/niklasfrykholm) for making it!)*.

It can give an estimate of "how many lines of code" end up being parsed by the
compiler, when all the header files have been included. I suggest you read the
[original post](http://bitsquid.blogspot.lt/2011/10/caring-by-sharing-header-hero.html) about it.
A more recent post from Niklas at how they are approaching
the [header file problem now](http://ourmachinery.com/post/physical-design/) is very interesting
too; though I'm not convinced it scales beyond "a handful of people" team sizes.

Anyway. I just made some [small improvements](https://bitbucket.org/aras_p/header_hero/commits/all)
to Header Hero while using it on our codebase:


#### Precompiled Headers

[{{<img src="/img/blog/2018/header_hero1.png" width="400">}}](/img/blog/2018/header_hero1.png)

I added a new field at the bottom of the main UI where a
"[precompiled header](https://en.wikipedia.org/wiki/Precompiled_header)" file can be indicated.
Everything included into that header file will be **not** counted into "lines of code parsed"
lists that the UI shows in the report.

What goes into the precompiled header itself is shown at the bottom of the report window:

[{{<img src="/img/blog/2018/header_hero3.png" width="150">}}](/img/blog/2018/header_hero3.png)


#### Small UI tweaks

Added quick links to "list of largest files" and "list of most included files" (hubs) at top of build
report. Initially I did not even know the UI *had* the "hubs" list, since it was so far away on the scrollbar :)

[{{<img src="/img/blog/2018/header_hero2.png" width="150">}}](/img/blog/2018/header_hero2.png)

Switched the "Includes" tab to have file lists with columns (listing include count / line count), and added
a "go to previous file" button for navigation.

[{{<img src="/img/blog/2018/header_hero4.png" width="650">}}](/img/blog/2018/header_hero4.png)


That's it! Get them **[on bitbucket here](https://bitbucket.org/aras_p/header_hero)**.

