---
categories:
- random
- rant
comments: true
date: 2011-04-01T20:55:26Z
slug: stories-of-universities
status: publish
title: Stories of Universities
url: /blog/2011/04/01/stories-of-universities/
wordpress_id: "658"
---

I was doing a talk and a Q&A session at a local university. Unaware of the consequences, one guy asked about the usefulness of the programming courses they have in real work...

Oh boy. Do you really want to go there?



> Now before I go ranting full steam, let me tell that there were really good courses and really bright teachers at my (otherwise unspectacular) university. Most of the math, physics and related fundamental sciences courses were good & taught by people who know their stuff. Even some of the computer science / programming courses were good!




With that aside, let's bet back to ranting.


**What is OOP?**

Somehow conversation drifted to the topics of code design, architecture and whatnot. I asked the audience, for example, what do they think are the benefits of object oriented programming (OOP)? The answers were the following:




  * Mumble mumble... weeelll... something something mumble. This was the majority's opinion.


  * OOP makes it very easy for a new guy to start at work, because everything nicely separated and he can just work on this one file without knowing anything else.


  * Without OOP there's no way to separate things out; everything becomes a mess.


  * OOP uses classes, and they are nicer than not using classes. Because a class lets you... uhm... well I don't know, but classes are nicer than no classes. I think it had something to do with something being in separate files. Or maybe in one file. I don't actually know...


  * _I forget if there was anything else really._



Let me tell you how easy it is for a guy to start at work. You come to new place all inspired and excited. You're being put into some unholy codebase that grew in a chaotic way over last N years and being assigned to do some random feature or fix some bugs. When you encounter anything smelly in the codebase (this happens fairly often), the answer to "WTF is this?" is most often "it came from the past, yeah, we don't like it either" or "I dunno, this guy who left last year wrote it" or "yeah, I wrote it but it was ages ago, I don't remember anything about it... wow! this is idiotic code indeed! just be careful, touching it might break everything". All this is totally independent of whether the codebase used OOP or not.

I am exaggerating of course; the codebase doesn't have to be that bad. But still; whether it's good or not, or whether it's easy for a new guy to start there is really not related to it being OOP.

Interesting!

Clearly they have no frigging clue what OOP is, besides of whatever they've been told by the teacher. And the teacher in turn knows about OOP based on what he read in one or two books. And the author of the books... well, we don't know; depends on the book I guess. But this is at least a second-order disconnect from reality, if not more!

Why is that?

I guess part of the problem is teachers having no real actual work experience except by reading books. This can work for math. For a lot of programming courses... not so much. Another part is students learning in a vacuum, trying to _kind of_ get what the lectures are about and pass the tests.

In both cases it's totally separated from doing some real actual work and trying to apply what you're trying to learn. Which leads to some funny things like...


**How are floating point numbers stored?**

I saw this about 11 years ago in one lecture of a C++ course. The teacher quickly explained how various types are stored in memory. He got over the integer types without trouble and started explaining floats.



> So there's one bit for the sign. Then come the digits before the decimal point. Since there are 10 possible choices for each digit, you need four bits of memory for each digit. Then comes one bit for the decimal point. After the decimal point, again you have four bits per digit. Done!




ORLY? This was awesome, especially trying to imagine how to store the decimal point.

[![](http://aras-p.info/blog/wp-content/uploads/2011/04/pifloat.png)](http://aras-p.info/blog/wp-content/uploads/2011/04/pifloat.png)

See that decimal digit bit, haha! _You see, it's one bit and you can't... what do you mean you don't get it? And not only that; this needs variable length and... really? You're going to a party instead?_ I wasn't very popular.

Funny or not, this is not exactly telling a correct story on how floats are stored in memory on 101% of the architectures you'd ever care about.

I could tell a ton of other examples of little disconnects with reality, which I think are caused by not ever having to put your knowledge into practice.


**Where do we go from here?**

Now of course, the university I went to is not something that would be considered "good" by world standards. I went to several lectures by [Henrik Wann Jensen](http://graphics.ucsd.edu/~henrik/) at DTU at that was like night and day! But how many of these not-too-good-only-passable universities are around the world? I'd imagine certainly more than one, and certainly less than the number of MITs, Stanfords et al combined.

As a student, I _somehow_ figured I should take a lot of things with a grain of <del>salt</del> doubt. And in a lot of cases, trying to do something for real trumps lab work / tests / exams in how much you'll be able to learn. Go make a techdemo, a small game, play around with some techniques, try to implement that clever sounding paper from siggraph and observe it burst in flames, team up with friends while doing any of the above. [Do it](http://www.youtube.com/watch?v=u6ALySsPXt0)!
