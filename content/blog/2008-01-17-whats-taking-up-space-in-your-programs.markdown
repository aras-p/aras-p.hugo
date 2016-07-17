---
tags:
- code
comments: true
date: 2008-01-17T12:24:54Z
slug: whats-taking-up-space-in-your-programs
status: publish
title: What's taking up space in your programs?
url: /blog/2008/01/17/whats-taking-up-space-in-your-programs/
wordpress_id: "154"
---

Ever wondered what takes up space in the programs you write? I certainly did on a number of occasions.

For some reason though, I could not find a decent tool that would look at a Visual Studio compiled executable or a DLL, and report an overview of how large are the functions, classes, object files and whatnot. [.kkrunchy](http://farbrausch.com/~fg/kkrunchy/) executable packer does have a very nice size report, but it's not exactly suitable for large executables...

Anyway, [ryg](http://farbrausch.com/~fg/) of farbrausch fame was kind enough to donate the size reporting code, I did some modifications, and here it is: [**Sizer** - executable symbol size reporting utility](http://aras-p.info/projSizer.html).

Enjoy. Oh, and the source code looks messy mostly because ryg and I use different indentation, and I never cared to format everything with a single style. Noone cares about the source code anyway, as long as it works. I'm not claiming that _this_ code works, of course!
