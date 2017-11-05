---
title: "Six Months in a Service Team"
date: 2017-09-06T20:56:56+03:00
tags: ["work", "unity", "devtools"]
comments: true
---

So it's been [six months](/blog/2017/03/16/Developer-Tooling-a-week-in/) since I switched
to doing build engineering / developer tooling. Here are some random things I learned.


### It's a service team

We are working on a live codebase, with *I don't even know how many* (~500?) developers on it, and it has to
keep on building and not crumble down with additional code / people / stuff. This means the team I'm on
is a "service / live ops" team to a large extent, does a bunch of support and short term problem solving,
in addition to steering the whole codebase build towards a hopefully better future.

That is not a surprise; I fully expected that.

What was a surprise: **I thought it would be much worse to be on a "service" team**.

A build system is one of these areas where "if it works well, no one notices it", and yes, in many
cases when people talk about it they talk about *problems* they are having. And so before
starting all this I had mentally prepared myself to jump into a super thankless job, where everyone
else is complaining about state of things and shouting at you for all the issues there, etc.

So far it's less thankless, and way less people shouting at me than I expected!

It is true that when people hop onto our Slack channel, they are coming with some problem they are having.
They might provide excellent information and articulate what is happening in good detail, or they
might be very vague and confused; they might be just stating the problem or expressing
frustration, disappointment, anger or whatever else. However, all that is basically complaining
about computers, or software, or codebase; I don't remember a single time where anyone did
a "[stupid build people](https://www.youtube.com/watch?v=12cFyTOypVc)" type of complaints.
For some reason I expected some amount of issues to reach an ad-hominem level... maybe I've read
too many bad stories on the internet, and none of them turned out to be applicable here. *Great!*

That said, an occasional *"yo! just wanted to say that you are all doing a good job, keep it up!"*
is appreciated, when working in a service-like team. Go and tell that right now to people making
sure your wireless works, your servers are building stuff, your office fruits are delivered on time,
your patches are predictably shipped to the cloud, ...


### When someone has a problem, they want it solved

When someone has a problem and could not solve it for themselves in X amount of time, they hopefully turn for help.
Be it coworkers, Slack, email, twitter or whatever. The composition of my team right now is that people working on
"build system" are fairly remote, so the way people ask us is mostly Slack messages.

Here's the main thing: for their problem, what they are mostly interested in is "make it disappear". **The most important
thing is to unblock them**, so that they can continue doing whatever they were trying to do.

First figure out what is going on; do the minimal thing to make the problem disappear for them
*("graft this changeset", "change file X line Y to Z", "pass this argument on command line",
"delete this folder from build artifacts and continue build", "click that setting in your IDE", ...)* - whatever quick
fix/hack it takes. Add a TODO for yourself to fix it properly, and actually follow through on that.

If it's an issue that happened more than once or twice, add to a list of "known issues" (e.g. via pinned messages
in Slack channel), with good, searchable wording and clear instructions how to workaround/solve it.
Keep the known issues list more or less up to date!

What people are ***not interested in***, when they come to your support channel:

1) A lecture on how Well Actually It's Not An Issue You See, You Are Doing Things Wrong. Sure they might
be doing something wrong; they don't need a patronizing lecture though. Short, to the point information
on what should be done instead. Also take a note - maybe they do have a valid point? Maybe the commands / options /
file locations / whatever is indeed confusing and error prone? Maybe it could or should be improved, either in code
changes or at least in better error messages / documentation / training?

2) Whose fault the issue is (though more on that separately below).

3) Why the issue happens, and an explanation of some innards of build code. *Some* people might be interested in details on why the issue happens; but first *solve* the issue for them, and provide
details as an optional bonus information, if needed.

4) A long rant about how your team is understaffed and can't deal with that many issues. *Every* team would
want to do more things than they have resources for; and yes maybe your team is in a more dire situation --
that's not what the inquiry is about however. Solve their problem first. If you feel like it, *maybe* mention
that "yeah we'll solve this eventually, but so far didn't have time yet". But better talk with someone
who can fix the staffing situation (management, etc.).


### Own *your* f***ups

There's one exception I have for the "no one is interested in whose fault the issue is" guideline -- **if it's your fault,
then saying "Sorry about this! My fault, fixing ASAP"** is acceptable :)

Over half a year of working on build stuff, I think I have made a dozen or so embarrassing things that caused people
frustration & wasted their time. It's not ideal, in retrospect for at least half of them *"wot were you thinking aras"* is a good
summary. I did try my best to provide workarounds, apologize and fix as soon as possible. And then hope that I did more
than a dozen things that improved the build experience.

And again, if it's a problem caused by anyone else, there's no need to point fingers. You can still say
"sorry! we'll be fixing ASAP" of course.


### Build performance / stability is an uphill battle

Codebases tend to grow over time -- more stuff being added, more developers doing it; and usually "old things" can not be
retired and removed as fast. This means that even to keep "build times" constant over time, someone has to actively
spend time chipping away on various inefficiencies.

Over last month I've spent some time optimizing full/clean builds (one [example](/blog/2017/08/08/Unreasonable-Effectiveness-of-Profilers/)),
and for some build targets it's effectively "twice as fast" now. However, those slow parts *did not exist*
half a year ago! So over that time period, the full build time has not improved... it's "just" *no longer twice as bad*.

Similar with build stability - various bugs in the build process & dependency graph are not some old problems; and instead
they are caused by new systems landing, new ways that code is split up, new platforms, and so on. Just maintaining same level
of build sanity is *actual work* that needs to be done, and it's never finished. On the plus side... hey, job security! :)


### Job satisfaction?

[{{<imgright src="/img/blog/2017-09/typical-work.png" width="250">}}](/img/blog/2017-09/typical-work.png)

Kinda related to "this was supposed to be a thankless job" (turns out, not so much), I've been thinking on
what about it can be considered "job satisfaction". For me, it's perhaps the "**I feel like I'm being useful**"
part. That's a very broad statement, however it also means that I can pick many areas to work in,
compared to something like "the only way to make me happy is if I get to write GCN assembly all day long" :)

The work I'm doing is not some sort of cutting edge research or "interesting" problems. There's no best paper awards for finding
out how to split up .vcxproj and .csproj files for most optimal Visual Studio intellisense experience, for example.
But at this point in my life, I'm perfectly fine with that!


### Do I still get to delete code?

[{{<imgright src="/img/blog/2017-09/commit-stats.jpg" width="250">}}](/img/blog/2017-09/commit-stats.jpg)

Happy to report -- yes! I won't reach my extrapolated goal of [removing 200kloc](https://twitter.com/aras_p/status/807203481719361548)
this year though. So far this year: 82kloc added & 143kloc removed (so made the codebase
61kloc smaller). However first two months were still doing graphics stuff; since I started doing build engineering the
stats are 50kloc added, 87kloc removed (net effect 37kloc smaller). Not bad!


*Well, that's it. I'll get back to working on some C# project generation stuffs*.

