---
title: "Fourteen years at Unity o_O"
date: 2020-01-04T16:02:10+03:00
tags: [unity', 'work', 'personal']
comments: true
---

Looks like I've been working at [Unity](https://unity.com/) for 14 years. *What?!?!* So here's another blog post that looks
at the past without presenting any useful information, similar to the ones from [two](/blog/2008/01/15/about-two-years-ago/),
[four](/blog/2010/01/04/four-years-ago-today/), [ten](/blog/2016/01/04/10-years-at-unity/),
[eleven](/blog/2017/01/15/A-look-at-2016-and-onto-2017/) years.

A year ago I [wrote](/blog/2019/01/07/Mentoring-You-Wont-Believe-What-Happened-Next/) how I started mentoring
several juniors at work, and then how I've spent [two years](/blog/2019/03/08/Two-years-in-a-build-team/) on
the build system team.

What happened next is that I somehow managed to convince others *(or someone else has convinced me -- it's a blur)*
that I should stop being on the build system team, "steal" the juniors I was mentoring and create a whole new team.
And so one thing led to another, and I ended up leading/managing a whole new 8-person team, with most of us being
in Unity [Kaunas](https://en.wikipedia.org/wiki/Kaunas) office. Due to lack of imagination, this was simply called
a "Core Kaunas" team.

We spent most of 2019 focusing on **improving version control (mostly Perforce)** integration in Unity -- fixing bugs,
improving integration UI/UX, fixing *lots* of cases of versioned files being written to disk for no good reason
(which under Perforce causes a checkout), and so on. See release notes items starting with "Version Control" in
[2019.3 release notes](https://unity3d.com/unity/beta/2019.3.0f3) for an example. Most of that work ships in 2019.3,
some in 2020.1, some was backported all the way back to 2018.4 LTS. Most of what we did was either
reported bugs / feature requests by users, or things coming from our own internal production(s), that for the first time
ever used Perforce (on purpose! so that we could see the issues with our own eyes).

But also we managed to do some **"random other" work**, here's a summary of what we casually did on the side
in 2019 Q3 and Q4 respectively:

[{{<img src="/img/blog/2020/14y-corekaunas-q3.png" width="390px">}}](/img/blog/2020/14y-corekaunas-q3.png)
[{{<img src="/img/blog/2020/14y-corekaunas-q4.png" width="365px">}}](/img/blog/2020/14y-corekaunas-q4.png)

For a team where 5 out of 8 people have only about a year of "professional programming/QA" experience, and where
this "side work" is not even our main focus area, I think that's pretty decent! Happy there.

Starting this year, my team will be transitioning towards **"various quality-of-life improvements"** work, mostly
in the editor based on artist/production feedback. Not "large features", but various "low hanging fruit" that
is relatively easy to do, but for whatever reason no one did yet. Some of that because teams are busy doing
more important stuffs, some because work lands in-between teams with unclear ownership, and so on. "Editor Quality of Life"
in Q4 random work image above is basically what we're after. Version Control integration and improvements we'll
hand over to another team. Let's see how that goes.

On a more personal side of work, I keep on doing short summaries of every week, and then at end of year
I write up a *"wot is it that aras did"* doc. Partially because every one of my bosses is in some other office and I rarely
get to see them, and partially so that I can argue I'm worth my salary :), or whatever.

[{{<img src="/img/blog/2020/14y-2019.png">}}](/img/blog/2020/14y-2019.png)

[{{<imgright src="/img/blog/2020/14y-lines-of-code-per-year.png" width="250px">}}](/img/blog/2020/14y-lines-of-code-per-year.png)
Happy to report that I managed to **delete 747 thousand lines of code** last year! That is a bit cheating though,
since half a million of that was versioned Quicktime headers, and turns out they are *huge*. Most of other deletions
were things like "remove the old C#<->C++ bindings system", which is no longer used. Anyway, I like deleting code, and this year was *good*.

Looking forward to what "my" team will be able to pull off in 2020, and also how juniors on the team will
grow. Let's make and ship more of these improvements, optimizations and "someone should totally have done this by
now" type of things. \o/
