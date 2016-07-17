---
tags:
- code
- random
comments: true
date: 2008-07-16T22:02:27Z
slug: it-must-be-a-bug-in-oscompiler
status: publish
title: It must be a bug in OS/compiler/...
url: /blog/2008/07/16/it-must-be-a-bug-in-oscompiler/
wordpress_id: "187"
---

Ever looked at the code which is _absolutely correct_, yet runs incorrectly? Sometimes it looks like a genuine compiler bug. _"I swear, mister! The compiler corrupts my code!"_

Look again. And again. Eventually you'll find where your code is broken.

_(Of course, in some cases quite often the compiler is broken... GLSL, anyone?)_

[Pimp my code, part 15: The Greatest Bug of All](http://wilshipley.com/blog/2008/07/pimp-my-code-part-15-greatest-bug-of.html) says the above in a much nicer way:



> Maybe the problem was there was some huge bug in Apple's Mach, where if you open too many files in a short period of time, the filesystem tried to, like, cache the results, and the cache blew up, and as a result the filesystem incorrectly just would fail to open any more files, instead of flushing the cache.
>
> ...
>
> I've also been around long enough to _know_ that whenever I know the operating system must be bugged, since _my_ code is correct, I should take a damn close look at my code. The old adage (not mine) is that 99% of the time operating system bugs are actually bugs in your program, and the other 1% of the time they are still bugs in your program, so look harder, dammit.



A post well worth reading... about the process of investigating tricky bugs. And sincere as well. It's so good that I'll just quote it again:



> It's a bug we should have caught. We should have spent the time to get the images in the 10,000 item file. I messed up.
> 
> Software is written by humans. Humans get tired. Humans become discouraged. They aren't perfect beings. As developers, we want to pretend this isn't so, that our software springs from our head whole and immaculate like the goddess Athena. Customers don't want to hear us admit that we fail.
> 
> The measure of a man cannot be whether he ever makes mistakes, because he _will_ make mistakes. It's what he does in response to his mistakes. The same is true of companies.
> 
> We have to apologize, we have to fix the problem, and we have to learn from our mistakes.



So very true.
