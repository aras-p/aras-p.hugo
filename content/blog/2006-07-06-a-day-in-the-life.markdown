---
categories:
- work
comments: true
date: 2006-07-06T01:38:00Z
slug: a-day-in-the-life
status: publish
title: A day in the life...
url: /blog/2006/07/06/a-day-in-the-life/
wordpress_id: "95"
---

Ok, as [mcpunky](http://sorcy7.livejournal.com/) asked to say something in more detail, I have no choice but to log a day of my [work](http://unity3d.com/company/people.html). Bear with me.

_10:30_ - I come to work. Peter is already here and wants to show me the shader-related project he's working on. It is becoming really cool by now, we discuss the projects, shaders, lighting, BRDFs and all the other stuff that graphics programmers always talk about.

{% img right http://aras-p.info/img/blog/060704a.png %}
_10:55_ - I turn on my work machines, read mail, check our forums, bugtracker and sales statistics :) Update code/website from Subversion. Review website changes of yesterday.

_11:35_ - Checkout new bugs reported to me. This day we have three of them. Some shaders don't display correctly for one user on his laptop; checking it, replied asking for log files now. Webplayer crashed on win2000. Checkout the crash dump, it's in Mono initialization; flag the bug for later review on actual win2000. One more: windows build loses the very first keystroke. Launch Visual Studio, build debug player. By 12:10 I have this bug found, fixed and checked (was an error in handling lost DirectInput devices). Commit into svn, merge into Unity 1.5.1 branch as well.

{% img right http://aras-p.info/img/blog/060704b.png %}
_12:15_ - Launch build of 1.5.1 branch on my PC, meanwhile I'll install more RAM on the windows build machine (it clearly needs it). Fifteen minutes later the release build is done and I have the RAM installed. Start debug build, do some small stuff on the laptop.

_12:40_ - Nicholas comes to discuss our strategy for handling per-vertex lighting with custom shaders of Peter's project. Fixed function T&L and custom shaders that don't care about vertices or pixels just don't merge well together. Maybe we'll have to reimplement the fixed-function vertex pipeline equivalent in vertex shader(s). Might be hard to get right, especially on some lower hardware.

_13:05_ - Lunchtime! At this time of year it's almost mandatory to eat outside. So nice. After lunch I watch a demo [deities by mfx](http://www.pouet.net/prod.php?which=24487) for some inspiration. Ah, I so want to do that.

_13:45_ - Joe comes in, shows some cool stuff one customer is working on. We discuss some glow filter issues.

_14:00_ - Philosophical talk about our unit/functional testing strategy. We do quite some functional tests now, and a tiny amount of unit tests. I'd like to try some unit tests on shader parser (which I'll have to refactor someday soon, and without unit tests I'm scared). Agreed that unittests are probably a good thing if you strike the right balance (now THAT was a surprise, huh?). Will try.

_14:30_ - Checkout a whitepaper from ATI about fixed function emulation in HLSL, playing with their shader. Instruction counts are scary! Talk with Nich about glow improvements, he has some good tips.

_15:15_ - Put a GeForce into my PC - will try to figure out why in some cases graphics looks too dark on them.

_15:25_ - Again comes Nicholas, more talk about this shader thingy, regular additive pixel passes vs. ambient passes vs. vertex lighting and how the actual end user should not care about these details. We found a nice solution it seems; this way shader authoring seems intuitive (or at least much more intuitive than the other alternatives we considered).

_15:50_ - Back to darkness-on-geforces issue. On the way I found out that motion blur filter no longer works on a PC! Reverting to older versions from svn to find out where I did introduce it. Ok, that was a stupid typo by me from several days ago, fixed.

_16:35_ - David opens a shoe box - and surprise surprise - there's a cake and cookies inside! Good stuff. Me loves anything that's sweet.

_16:45_ - The darkness-on-geforces is only the motion blur filter going wrong. That's good. Debugging with glIntercept, something's wrong with blur accumulation texture.

_17:10_ - D'oh! The motion blur had a pretty crappy implementation; was trying to render into the texture while using itself as the input. Obviously the results can be undefined, which just happened on geforces. Rewriting the filter to do it properly. Testing it, integrating into standard assets.

_17:45_ - Discuss our (i.e. company) roadmap and which parts of it can be announced publicly :) Then the talk went downhill about memory management, fragmentation, per frame allocations, porting Mono to some exotic architectures, whether porting runtime to Direct3D or  kind-of-OpenGL-but-not-quite would be easier (my take is that porting to D3D would be easier) etc. Did something else which I don't quite remember because we couldn't have talked for an hour, right?

{% img right http://aras-p.info/img/blog/060704c.png %}
_18:50_ - Making some tweaks to the glow filter to look nicer in some cases. Done except the Radeon9000 path. I guess that's enough for today, now again check our forums, email and will go home.

_19:30_ - I actually leave home, it's some 20 minutes away on a bike. The plan is to eat lots of grapes and watch a movie tonight. What turned out is that I read some papers and then did the plan.

Overtall, it has been a day with quite much talks. Not exactly the day where you feel  "hey, I've done a lot of stuff today", but not bad either. Will be ok for blog purposes I guess.
