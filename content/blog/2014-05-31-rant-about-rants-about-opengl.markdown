---
categories:
- opengl
- rant
comments: true
date: 2014-05-31T00:00:00Z
title: Rant about rants about OpenGL
url: /blog/2014/05/31/rant-about-rants-about-opengl/
---

Oh boy, people do talk about state of OpenGL lately! Some exhibits: Joshua Barczak's ["OpenGL is Broken"](http://www.joshbarczak.com/blog/?p=154), Timothy Lottes' [reply on that](http://timothylottes.blogspot.com/2014/05/re-joshua-barczaks-opengl-is-broken.html), Michael Marks' [reply to Timothy's reply](https://medium.com/@michael_marks/opengl-for-real-world-games-7d0f4d35891c). Or an earlier Rich Geldreich's ["Things that drive me nuts about OpenGL"](http://richg42.blogspot.com/2014/05/things-that-drive-me-nuts-about-opengl.html) and again [Timothy's reply](http://timothylottes.blogspot.com/2014/05/re-things-that-drive-me-nuts-about.html).

*Edit: [Joshua's followup](http://www.joshbarczak.com/blog/?p=196)*

In all this talk, one side (the one that says GL is broken) frequently bring up Mantle or Direct3D 12. The other side (the one that says GL is just fine and is indeed better) frequently bring up AZDO (["Almost Zero Driver Overhead"](http://www.slideshare.net/CassEveritt/approaching-zero-driver-overhead)) approaches. There are long twitter and reddit and hackernews threads on all this.

It might seem weird -- why OpenGL would get such bashing all of a sudden? But this is much *better* state than some 7 years ago... Back then almost no one cared about OpenGL at all! If people complain, that *at least* means they do care!

But you know what, both of these sides are right.


**OpenGL has issues**

Trying to flat-out deny that would be ignorant. Too many ways to do things; too much legacy; integer names instead of pointer handles; bind-to-edit; poor multi-threading; lack of offline shader compilation; *the list goes on* -- all these are real actual issues. And yes, most or all of these are being worked on, or indeed are fixed if you can use latest GL versions.

> These technical issues are important, I think. And no, saying
> "state changes are expensive? don't do them" -- which much
> of AZDO advocacy ends up being -- is not a real answer IMHO. Yes, moving to "pull data from GPU"
> model is perhaps the future and is great. Does not mean you can completely ignore CPU side things
> and pretend inefficiencies do not exist there. CPUs are still great at doing some things!


However, the biggest issue in my view is not a technical one, but a political one. On Windows, out of the box, you do not get an OpenGL driver (but you do get a D3D one for most GPUs). And no, *actual people* out there do not update their drivers. Ever.

> I know that you do. And your technically savvy gamer friends do. But for each one of you, there are 10 people
> who don't. We have [hardware stats](http://stats.unity3d.com/) from hundreds of millions of machines;
> the most popular driver versions on Windows are the ones that ship with the OS.

On Mac, OpenGL is "somewhat behind" (GL 4.1 right now). But in comparison to Windows, it's a much better *practical usage*, since GL drivers do come with the OS. And OS updates are free, and *somehow* Mac users do update their OSes at much faster rate than Windows users do.

I've no idea about user behaviour Linux. I know there are binary drivers for NV/AMD (tracking latest GL), and open source for Intel (behind latest GL), and nouveau and gallium etc. But no idea about whether Linux people update drivers, or whether they come with OS etc. So no informed opinion on this particular part from me.

So, on Windows we have the problem that GL drivers aren't shipped with OS, and that people generally don't update the OS. ***How to solve that?*** Perhaps all of us should try to persuade Microsoft to change this, and ship GL drivers with OS & windows updates. Maybe it's not as crazy as it sounds these days *(hey, no one believed MS would ever support WebGL... but they do! kind of)*.

On Mac we don't have that particular problem, but we do have a problem that GL implementation is lagging behind the latest version. ***How to solve that?*** Perhaps try to persuade Apple to not lag behind. And/or make such kickass games that the advantage of latest GL tech would be too obvious to ignore (this one is a bit of a chicken-and-egg problem, sure).


**OpenGL also has potential**

Modern OpenGL does indeed have some crazy-awesome features. [Check out AZDO](http://www.slideshare.net/CassEveritt/approaching-zero-driver-overhead) again - that's a whole new level of thinking there. The combination of persistent mapping, fine-grained fences, bindless resources and multi-draw-indirect does enable building *substantially different* rendering pipelines.

The extension mechanism is an excellent vehicle for bleeding-edge capability delivery. Bindless and sparse textures, flexible indirect draw, persistent buffer mapping, stencil export and so on -- all these things appeared as extensions in OpenGL, long before Direct3D picked them up.

OpenGL is an API that spans the most platforms. On some of them, it is the only API. This is a valuable thing.


**This is where I forgot what I wanted to say**

I'm not sure where I'm going with this, really. Maybe *"make love, instead of fighting over whether OpenGL is good or bad"*. It will all be alright in the end. Or something like that.

If you *really* want OpenGL fixed, perhaps joining Khronos is a good idea. That particular piece is a topic for another rant I guess... We (Unity) are on Khronos but there's too much bureaucracy for my taste so I just can't be bothered. Thankfully [Christophe](http://www.g-truc.net/) often carries the flag.

I'm actually quite happy that Mantle and upcoming DX12 has caused quite a stir of discussions *(to be fair PS3's libGCM was probably the first "modern to the metal" API, but everyone who knows anyting about it can't say it)*. Once things shake out, we'll be left with a better world of graphics APIs. Maybe that will be the world with more than two non-console APIs, who knows. In any case, competition is good!

