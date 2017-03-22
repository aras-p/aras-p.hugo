+++
tags = ['work', 'unity', 'devtools']
comments = true
date = "2017-03-16T20:42:22+02:00"
title = "Developer Tooling, a week in"
+++

So I switched job role from graphics to
[developer tooling / build engineering](/blog/2017/02/26/Stopping-graphics-going-to-build-engineering/) about 10
days ago. *You won't believe what happened next! Click to find out!*


#### Quitting Graphics

I wrote about the change right before GDC on purpose - wanted to see
reactions from people I know. Most of them were along what I expected,
going around "build systems? why?!" theme (my answer: between "why not" and
¯\\\_\(ツ\)\_/¯). I went to the gathering of rendering people one evening,
and the "what are you doing here, you're no longer graphics" joke that _everyone_
was doing was funny at first, but I gotta say it to you guys: hearing it
40 times over is not that exciting.

At work, I left all the graphics related Slack channels (a *lot* of them), and
wow the sense of freedom feels good. I think the number of Slack messages
I do per week should go down from a thousand to a hundred or so; big
improvement (for me, anyway).

A pleasant surprise: me doing that and stopping to answer the questions, doing
graphics related code reviews and writing graphics code *did not* set the world
on fire! Which means that ***my "importance" in that area was totally imaginary***,
both in my & in some  other people's heads. *Awesome!* Maybe some years ago
that would have bothered me, but I *think* I'm past the need of wanting to
"feel important".

Not being important is liberating. Highly recommended!

Though I have stopped doing graphics related work at work, I am still kinda following
various graphics research & advances happening in the world overall.


#### Developer Tooling

The "developer tools" team that I joined is six people today, and the "mission" is
various internal tools that the rest of R&D uses. Mostly the code build system,
but also parts of version control, systems for handing
3rd party software packages, various helper tools
(e.g. Slack bots), and random other things (e.g. "upgrade from VS2010 to VS2015/2017"
that is happening as we speak).

So far I've been in the build system land. Some of the things I noticed:

* Wow it's super easy to save hundreds of milliseconds from build time. This is not a big
  deal for a clean build (if it takes 20 minutes, for example), but for an incremental build
  add 100s of milliseconds enough times and we're talking some real "developer
  flow" improvements. Nice!
* Turns out, a lot of things are outdated or obsolete in the build scripts or the dependency
  graph. Here, we are generating some config headers for the web player deployment
  (but we have dropped web player a long time ago). There, we are always building this small
  little tool, that turns out is not used by anything whatsoever. Over here,
  tons of build graph setup done for platforms we no longer support. Or this
  often changing auto-generated header file, is included into way too many source files.
  And so on and so forth.
* There's plenty of little annoyances that everyone has about the build process
  or IDE integrations. None of them are blocking anyone, and very often do not get fixed.
  However I think they add up, and that leads to developers being much less happy
  than they could be.
* Having an *actual*, statically typed, language for the build scripts is really nice.
  Which brings me to the next point...


#### C\#

Our build scripts today are written in C#. At this very moment, it's this strange beast
we call "JamSharp" (primarily work of [@lucasmeijer](https://twitter.com/lucasmeijer)).
It is [JamPlus](http://jamplus.org/), but with an embedded .NET runtime (Mono), and so
the build scripts and rules are written in C# instead of the not-very-pleasant Jam
language.

Once the dependency graph is constructed, today it is still executed by Jam itself, but we
are in the process of replacing it with our own, C# based build graph execution engine.

Anyway. C# is really nice!

I was supposed to kinda know this already, but I only occasionally dabbled in C# before,
with most of my work being in C++.

In a week I've learned these things:

* JetBrains [Rider](https://www.jetbrains.com/rider/) is a really nice IDE, especially on
  a Mac where the likes of VisualStudio + Resharper do not exist.
* Most of [C# 6 additions](https://github.com/dotnet/roslyn/wiki/New-Language-Features-in-C%23-6)
  are not rocket surgery, but make things so much nicer. Auto-properties, expression
  bodies on properties, "using static", string interpolation are all under "syntax sugar"
  category, but each of them makes things just a little bit nicer. Small "quality of life"
  improvements is what I like a lot.
* Perhaps this is a sign of me getting old, but e.g. if I look at new features added to
  C++ versions, my reaction to most of them is "okay this probably makes sense, but also
  makes my head spin. such. complexity.". Whereas with C# 6 (and
  [7 too](https://docs.microsoft.com/en-us/dotnet/articles/csharp/csharp-7)), almost
  all of them are "oh, sweet!".


#### So how are things?

[{{<imgright width="200" src="/img/blog/2017-03/week-improvements2.png">}}](/img/blog/2017-03/week-improvements2.png)

One week in, pretty good! Got a very vague grasp of the area & the problem. Learned a few
neat things about C#. Did already land two pull requests to mainline
([small improvements](/img/blog/2017-03/week-improvements1.png) and
[warning fixes](/img/blog/2017-03/week-warnings.png)), with another
[improvements batch](/img/blog/2017-03/week-improvements2.png) waiting for code reviews.
Spent two days in Copenhagen discussing/planning next few months of work and talking to
people.

Is very nice!
