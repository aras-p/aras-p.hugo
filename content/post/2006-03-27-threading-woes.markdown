---
categories:
- rant
comments: true
date: 2006-03-27T11:20:00Z
slug: threading-woes
status: publish
title: Threading woes
url: /blog/2006/03/27/threading-woes/
wordpress_id: "89"
---

Getting multithreaded code right is just so damn hard. Reasoning about it's behavior or correctness is even harder! We'll be doomed until we get something of much higher level than threads and locks.

Why I'm writing this? Because my head hurts from trying to make the renderer run in one thread, and the containing application in the other. With added spice that most (win32) GUI stuff must happen in one thread, OpenGL contexts can only be active in a single thread, simple functions must be split into complex chains of inter-threading calls, etc. etc. The result is a mess that nobody can understand.

I hope I'm not missing something obvious (well, aside from the suggestion "just don't use threads").

