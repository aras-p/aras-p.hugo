---
title: "Mentoring: You Won't Believe What Happened Next!"
date: 2018-12-25T21:27:10+03:00
tags: ['rants', 'work']
comments: true
draft: true
---

So! For three months by now, I'm mentoring three junior engineers at work.

*But first, let me talk about something entirely else.*

### The Seniority Trap

In games, and especially in engine/middleware technology, it's fairly common to see job requirements like "Senior engineer
needed, 10+ years of AAA/console development experience required" etc.

Sometimes that makes sense. Some other times, it's there just because of inertia or because that's what the
hiring policies tend to naturally gravitate towards, given the surrounding environment. A long sequence of individual
"yeah that makes sense" steps *can* lead to an outcome that makes you go "wait what?".

Here's a simple example: it makes sense to allocate "headcount" for departments or teams when planning company's financial
year. However, *if* teams have fixed headcount available for new hires, they *will* tend to gravitate towards
"well if we only can hire one more person, we'd better hire someone really really good". So that tends to lean into
"hire senior people only" territory. Repeat that for a number of years, and you get a situation where *default*
action for any team anywhere in the company is to hire senior people. There was no "plan" to become like that, it just
kinda evolved from a set of surrounding rules.

Senior people are convenient to hire - they already have lots of experience, you don't have to explain the basics to them,
they can bring valuable experience & knowledge from other places and products, you yourself often can learn quite a lot
from them. Super cool! As a fairly senior (in my office at least) dude myself, I say "senior people FTW!".

They are also rare, expensive, sometimes stubborn, perhaps unwilling to learn new things, and sometimes their experience
is "same old, same old". That describes me as well.

Hiring *just* senior people can lead to teams that have optimized for [local maxima](https://en.wikipedia.org/wiki/Maxima_and_minima)
-- each new hire fits well, is an incremental improvement to the team/product; what's not to like. And so the team climbs
into a local hill, completely missing a mountain peak that is beyond the valley.


#### Isn't all that just hypothetical philosophizing?

Maybe. I don't know. I do know that for many years, most of our job ads had requirements that the Unity founders,
and many of the early employees would not satisfy :)

It's hard to *prove* it without forking reality sometime in the past and picking an alternate history -- but you *could* imagine
that, for example, if Unity founders had 10+ years of game development experience, they would have picked different decisions.
Some might have been better, or way better. But some might have been way worse, since they might have built an engine, tools
and workflows that mimicked what they were familiar with!

My point is, I guess: you need both experienced people and "new" people. There can be many different reasons for getting "new"
people.


### Giving Juniors A Chance

> The following is mostly about the environment we had in Unity's Lithuania (Vilnius & Kaunas) offices.
> We have a ton of offices all over the place, and their setup, composition and structure can differ
> a lot!

About a year ago I noticed that here (Unity Lithuania offices) we have a lot of junior / entry-level people
in frontline QA team. These are the folks that read all the incoming bug reports, try to make sense of them
(reproduce the issue, find versions where it started happening, summarize in usable form for developers, and
assign further down to devs if the issue turns out to be an actual bug). Very important job! But perhaps
not the most interesting / motivating in the world; a large part of these QA people wanted to move
into "product QA" or become programmers.

To some extent, we did have this "Seniority Trap" problem from above -- most of our job requirements for engineers
had a "yadda yadda, 5+ years programming experience". And so on one hand, we have something like 50 junior QA
people here, but on the other hand, there's a sort of uncrossable chasm to get to any other positions from there.

I thought that's both sad, and a bit stupid on our part, and started to lobby along the lines of *"we should come up
with ways of having a better career path for these QA people"*. Some of more concrete steps we started doing:

* Start planting seeds of thought that there's no rule of universe that says "good people are all in San Francisco or Seattle"
  *(I checked, there is no such rule indeed)*.
* Make a training programme -- various topics about coding etc. -- that we're doing for QA students here.
  I did a bunch of lectures there (check out my [talks page](/texts/talks.html) around 2018 Fall), more people did other topics.
  This by itself does not give *immediate* benefit, but hopefully is good learning material for the attendees.
  Most of topics are not even Unity specific; just general "good to know" info, especially if one wants to
  become a developer in game-industry related company.
* Make this whole QA group be much more visible inside the company, by talking with other teams more, and better
  "advertise" it as a possible source of talent.
* All in all, it ended up with about 25 of QA students "graduating" to other teams or positions inside the company
  over this year (2018); in comparison last year the number was like "five". I consider that to be a pretty good change!

Some of these "graduations" was result of me talking with our "Sustained Engineering" (SE) team. SE owns
[Long-Term-Support](https://blogs.unity3d.com/2018/04/09/new-plans-for-unity-releases-introducing-the-tech-and-long-term-support-lts-streams/)
and [Patch Releases](https://unity3d.com/unity/qa/patch-releases), which is mostly "fixing stuff and prevent
future breakages", and not much of fancy feature development. I started to talk with them along
the lines of *"hey, so hypothetically, if we'd find some junior developers that I would mentor..."*. Much to my
surprise, they were *"sounds great! can we start tomorrow?"*, and I was *gulp now I have to actually do this*.

And that's how I started doing a selection process sometime this summer; we picked three folks to start with, they started working
as programmers and I started mentoring them three months ago. How did that go?


### Mentoring: Actually Better Than Expected

When I realized this whole endeavor is in a *shit I have to actually do it* stage, I started to mildly panic!
It's been a few years when I was "responsible" for others at work (in terms of being a tech lead or whatever),
and I've never done mentoring/onboarding/teaching someone at work who had ***no*** previous professional programming
experience. And now there are going to be not one, but three people all at once!

*Geez Aras why you couldn't just continue coding in your little corner, things would have been so much simpler*.

So far it seems that most of my fears have been unfounded; three months in all things are going well!
I'm starting to think of increasing the number of people from 3 to a couple more.

Actual things I end up doing in this whole "mentoring" adventure:

* Being available for any & all questions related to workflows, codebase, advice, etc.
* Reviewing all of their code,
* Picking some tasks (so far mostly bug fixes or backports of existing fixes),
* Weekly 1:1 chats, and sometimes a talk with the whole team. We sometimes skip this if there isn't much to talk about,
* Impromptu talks about some concept (related to workflow, code, tips, tooling etc.) that we think is needed or useful,
* Some pair programming sessions. *Note to self: do more of this*.

All in all, I'm spending maybe 10-20% of my time on this. I expected it to be way more, but turns out the current
folks generally need a lot less hand-holding than I imagined it to be.

My observations about junior programmers are all very obvious *"well duh"*, but it's easy to forget about these things when not
dealing with them day to day. When you have years of experience, you tend to build up a lot of indirect knowledge and
approaches at solving problems. Things like:

* A **good set of hunches** at why something would behave the way it does. Is this because there's an integer overflow?
  Does this fail because the thing it's trying to do is not even supported on the platform it's running on? and so on.
* A good set of **"but what about case..." questions to ask** whenever you're about to start something. If I do this here,
  what else can possibly be affected? What are the edge cases of functions I'm calling, or data I'm receiving?
  How will this behave on a different hardware? In a multi-threaded environment? In an environment where memory is scarce?
  etc.
* Ability to efficiently **debug**. Coming up with good hypotheses at why something happens, quickly checking them, deciding
  what to check next if the hypothesis was correct (or what to do if it wasn't!). Basically
  [divide & conquer approach](/blog/2015/01/06/divide-and-conquer-debugging/) for debugging. This is an extremely useful & important
  skill, which many junior folks tend to not have at all, or have very little of it. Local schools don't teach
  debugging at all :(
* Using **source control** effectively. Starting from basics like "don't put completely unrelated things into one commit", to
  "writing good commit messages", to investigating why something happened in the past (log / annotate / blame), and
  into various situations like merges, conflicts, rebases, ways of undoing mistakes etc.
* Ensuring that code not only works today, but **will keep on working** tomorrow. Testing, asserts, compile time checks,
  runtime checks, logging - basically all of that is "new territory", and again the schools don't teach *any* of that.
  The reason for having tests or asserts often only comes with experience, and oftentimes by experiencing bugs getting
  introduced, functionality broken or other disasters. So yeah I get it that whenever I say "trust me on this, you will need it",
  it's hard to *really* internalize it :)
* Likewise, ability to **explain your code**. This could be design/feature docs, code comments that explain "why", commit
  messages etc. Like, *"ok sure if you divide X by two and multiply by two again here it fixes the issue, but WHY does it
  fix the issue?"*. In a codebase that serves tens of thousands of projects, and any code you write will live on
  for 5-10 years, it's not good enough to go *"eh I don't know, but it fixes it, so I'll just commit and move on"*.
* A good set of heuristics and rules of thumb for **managing time**. How long should you be "stuck" on a problem before reaching
  out for someone else's opinion? What can you do for that half an hour that a new fresh build is going to take? How to
  efficiently context-switch when you have to. How to interleave high-focus-needed tasks with low-effort ones. etc.

The list above is not *complaints* though; it's just a subset of what we "seniors"
have acquired over the years that allows us to be productive, and what many juniors (no matter how smart/motivated/... they are)
often don't have yet. Mostly because some of these are only *really* acquired through experience, or they might be very
different for each person.

I'm pretty sure the folks I'm working with right now will get it with time, and I hope I can be of help to get there.
So far they are doing somewhat better than I expected, so  **d(･∀･○)**

Looking forward to see what happens in a year or so!

