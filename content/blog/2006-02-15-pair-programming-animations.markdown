---
categories:
- code
comments: true
date: 2006-02-15T11:40:00Z
slug: pair-programming-animations
status: publish
title: Pair programming / animations
url: /blog/2006/02/15/pair-programming-animations/
wordpress_id: "87"
---

Tried out [pair programming](http://en.wikipedia.org/wiki/Pair_programming) the other day. I can definitely see it working, especially on hard topics (i.e. where you spend lots of time thinking, explaining, arguing and brainstorming that just typing in code). I am still not sure whether it really suits for "ordinary" day-to-day programming though.

The topic I and [Joe](http://otee.dk/people.html) tried it on was a pretty hard one - related to the core animation system. Now, of course I don't know anything about animation systems, but my impression is that there is just no "universal" way of designing it. The ones that are floating around inside free/open engines/libraries ([cal3d](http://cal3d.sourceforge.net), [nebula2](http://nebuladevice.cubik.org/documentation/nebula2/group__Anim2.shtml)) are quite fine, but not much more impressive than my own [very](http://svn.berlios.de/viewcvs/dingus/trunk/dingus/dingus/animator) [simplistic](http://svn.berlios.de/viewcvs/dingus/trunk/dingus/dingus/gfx/skeleton) attempts at doing animations. There is nothing wrong with that of course - its simple, it gets the job done, and its okay in most of the cases when you're doing simple stuff.

But then, if you want something more advanced, you either have to go and get the [big serious ](http://radgametools.com/gramain.htm)libraries, or just... well... not do it.

So, back to pair programming - making the core animation system that would have transitions, continuous blends, animation layers, bone masks and whatnot (and the kitchen sink of course!) is just not very easy. We paired basically on writing the pseudocode of the system, or some sort of outline; changed the implementation several times along the way, and in the end we're left with a really nice and fast system and the code is actually quite simple. Much of the credit for that goes to Joe, as he found out some really cool ways to optimize the expensive things away (at that time we were not doing pair programming anymore - I went to do some research on shadows!)

But to reiterate - pairing can definitely work. I guess mostly because the other person just keeps asking "why you're doing this?" or "this is wrong" or "we're in a deep shit now" or "that's awesome" :)
