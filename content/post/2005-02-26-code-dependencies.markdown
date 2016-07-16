---
categories:
- code
- rant
comments: true
date: 2005-02-26T16:17:00Z
slug: code-dependencies
status: publish
title: Code dependencies
url: /blog/2005/02/26/code-dependencies/
wordpress_id: "16"
---

They **suck**! Well, this probably isn't fresh news to anyone, but right now I'm working with code where basically everything includes everything else. The code is template-heavy so that makes things even worse. Try changing that file without 20 minute rebuild later - good luck!

Yeah, I know, the code is a product of 2 years of wandering in the darkness :) - prototyping, adding something and removing something, and when you constantly have to actually produce "working demos" in short timeframes, it ain't easy to actually sit down and refactor whole thing. Oh well, the reality...

Still, code with lots of dependencies does suck of course.

On the other hand, I'm pretty happy with the module I'm writing. All the outside just needs one header file that's right now **76 lines** long and that includes _only_ very basic "resource id" header file. The insides of my module also need some of the outside functionality; I've written some "outside interface" for that - a header file with something like 50 lines and also absolutely no includes.

That's what I consider to be a low-dependency interface. That's good. That doesn't suck :)

_[EDIT]_ The code I was referring to isn't so bad (e.g. it really doesn't "includes everything else", but "includes quite large amount of stuff"). I'm just somewhat frustrated at the moment, and anything that's not very ideal in the codebase irritates me.

