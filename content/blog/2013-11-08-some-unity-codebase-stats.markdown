---
tags:
- code
- unity
comments: true
date: 2013-11-08T00:00:00Z
title: Some Unity codebase stats
url: /blog/2013/11/08/some-unity-codebase-stats/
---

I was doing fresh codebase checkout & building on a new machine, so got some stats along the way. No big insights, move on!

### Codebase size

We use [Mercurial](http://mercurial.selenic.com) for source control right now. With "[largefiles](http://mercurial.selenic.com/wiki/LargefilesExtension)" extension for some big binary files (precompiled 3rd party libraries mostly).

Getting only the "trunk" branch *(without any other branches that aren't in trunk yet)*, which is 97529 commits:

* Size of whole Mercurial history (.hg folder): 2.5GB, 123k files.
* Size of large binary files: 2.3GB (almost 200 files).
* Regular files checked out: 811MB, 36k files.

Now, the build process has a "prepare" step where said large files are extracted for use (they are mostly zip or 7z archives). After extraction, everything you have cloned, updated and prepared so far takes **11.7GB** of disk space.

### Languages and line counts

Runtime ("the engine") and platform specific bits , about 5000 files:

* C++: 360 kLOC code, 29 kLOC comments, 1297 files.
* C/C++ header: 146 kLOC code, 18 kLOC comments, 1480 files.
* C#: 20 kLOC code, 6 kLOC comments, 154 files.
* Others are peanuts: some assembly, Java, Objective C etc.
* Total about **half a million lines of code**.

Editor ("the tools"), about 6000 files:

* C++: 257 kLOC code, 23 kLOC comments, 588 files.
* C#: 210 kLOC code, 16 kLOC comments, 1168 files.
* C/C++ Header: 51 kLOC code, 6k comments, 497 files.
* Others are peanuts: Perl, JavaScript etc.
* Total, also about **half a million** lines of code!

Tests, about 7000 files. This is excluding C++ unit tests which are directly in the code. Includes our own internal test frameworks as well as tests themselves.

* C#: 170 kLOC code, 11 kLOC comments, 2248 files.
* A whole bunch of other stuff: C++, XML, JavaScript, Perl, Python, Java, shell scripts.
* Everything sums up to about **quarter million** lines of code.


Now, all the above does not include 3rd party libraries we use (Mono, PhysX, FMOD, Substance etc.). Also does not include some of our own code that is more or less "external" (see [github](https://github.com/Unity-Technologies)).


### Build times

Building Windows Editor: 2700 files to compile; **4 minutes** for Debug build, **5:13** for Release build. This effectively builds "the engine" and "the tools" (main editor and auxilary tools used by it).

Build Windows Standalone Player: 1400 files to compile; **1:19** for Debug build, **1:48** for Release build. This effectively builds only "the engine" part.

All this doing a complete build. As timed on MacBookPro *(2013, 15" 2.3GHz Haswell, 16GB RAM, 512GB SSD model)* with Visual Studio 2010, Windows 8.1, on battery, and watching [Jon Blow's talk](http://www.youtube.com/watch?v=AxFzf6yIfcc) on youtube. We use
[JamPlus](http://jamplus.org/) build system ("everything about it sucks, but it gets the job done") with precompiled headers.

> Sidenote on developer hardware: this top-spec-2013 MacBookPro is about **3x faster** at building code as my
> previous top-spec-2010 MacBookPro (it had really heavy use and SSD isn't as fast as it used to be).
> And yes, I also have a development desktop PC; most if not all developers at Unity get a desktop & laptop.
>
> However difference between a 3 minute build and 10 minute build is **huge**, and costs a lot more than
> these extra 7 minutes. Longer iterations means more distractions, less will to do big changes
> ("oh no will have to compile again"), less will to code in general etc.
>
> **Do** get the best machines for your developers!


*Well this is all!*

