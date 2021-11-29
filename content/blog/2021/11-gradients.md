---
title: "Gradients in linear space aren't better"
date: 2021-11-29T14:10:10+03:00
tags: ['rendering']
comments: true
---

People smarter than me have already said it
(Bart Wronski [on twitter](https://twitter.com/BartWronsk/status/1453379831341715457)), but
here's my take in a blog post form too. _(blog posts? is this 2005, grandpa?!)_

When you want "a gradient", interpolating colors directly in sRGB space does have a lot of situations where
"it looks wrong". However, **interpolating them in "linear sRGB" is not necessarily better**!

### Background

In late 2020 Björn Ottosson designed ["Oklab" color space](https://bottosson.github.io/posts/oklab/) for gradients
and other perceptual image operations. I read about it, mentally filed under a "interesting, I should play around with it
later" section, and kinda forgot about it.

Come October 2021, and Photoshop version 2022 was announced, including an
"[Improved Gradient tool](https://helpx.adobe.com/photoshop/using/whats-new/2022.html#other-enhancements)".
One of the new modes, called "Perceptual", is actually using Oklab math underneath.

Looks like CSS ("Color 4") will be getting Oklab color space [soon](https://github.com/w3c/csswg-drafts/issues/6642).

I was like, *hmm*, maybe I should look at this again.


### sRGB vs Linear

Now - color spaces, encoding, display and transformations are a *huge* subject. Most people who are not into
all that jazz, have a very casual understanding of it. Including myself. My understanding is two points:

- Majority of images are in sRGB color space, and stored using sRGB encoding. Storage is primarily for
  precision / compression purposes -- it's "quite enough" to have 8 bits/channel for regular colors, and precision
  across the visible colors is okay-ish.
- *Lighting* math should be done with "linear" color values, since we're basically counting photons, and they add up
  linearly.

Around year 2010 or so, there was a big push in real-time rendering industry to move all lighting calculations into
a "proper" linear space. This kind-of coincided with overall push to "physically based rendering", which tried to
undo various hacks done in many decades prior, and to have a "more correct" approach to rendering. All good.

However, I think that, in many bystander minds, has led to a "*sRGB bad, Linear good*" mental picture.

Which is the correct model when you're thinking about calculating illumination or other areas where physical
quantities of countable things are added up. "I want to go from color A to color B in a way that looks
aesthetically pleasing" is not one of them though!


### Gradients in Unity

While playing around with Oklab, I found things about gradients in Unity that I had no idea about!

Turns out, today in Unity you can have gradients either in sRGB or in Linear space, and this is independent
on the "color space" project setting. The math being them is "just a lerp" in both cases of course, but it's
up to the system that uses the gradients to decide how they are interpreted.

Long story short, the [particle systems](https://docs.unity3d.com/Manual/PartSysUsage.html) (a.k.a. "shuriken")
assume gradient colors are specified in sRGB, and blended as sRGB; whereas the
[visual effect graph](https://docs.unity3d.com/Packages/com.unity.visualeffectgraph@latest) specifies colors
as linear values, and blends them as such.

As I'll show below, neither choice is strictly "better" than the other one!


### Random examples of sRGB, Linear and Oklab gradients

All the images below have four rows of colors:

1. Blend in sRGB, as used by a particle system in Unity.
1. Blend in Oklab, used on the same particle system.
1. Blend in Linear, as used by a visual effect graph in Unity.
1. Blend in Oklab, used on the same visual effect graph.

Each color row is made up by a lot of opaque quads (i.e. separate particles), that's why they are not
all neatly regular:
[{{<img src="/img/blog/2021/gradients-wire.png">}}](/img/blog/2021/gradients-wire.png)

[{{<img src="/img/blog/2021/gradients-black-white.png">}}](/img/blog/2021/gradients-black-white.png)
Black-to-white is “too bright” in Linear.

[{{<img src="/img/blog/2021/gradients-blue-white.png">}}](/img/blog/2021/gradients-blue-white.png)
Blue-to-white adds a magenta-ish tint in the middle, and also “too bright” in Linear.

[{{<img src="/img/blog/2021/gradients-red-green.png">}}](/img/blog/2021/gradients-red-green.png)
Red-to-green is “too dark & muddy” in sRGB. Looks much better in Linear, but if you compare it with
Oklab, you can see that in Linear, it _feels_ like the "red" part is much smaller than the "green" part.

[{{<img src="/img/blog/2021/gradients-blue-yellow.png">}}](/img/blog/2021/gradients-blue-yellow.png)
Blue-to-yellow is too dark in sRGB, too bright in Linear, and in both cases adds a magenta-ish tint.
The blue part feels too narrow in Linear too.

[{{<img src="/img/blog/2021/gradients-rainbow.png">}}](/img/blog/2021/gradients-rainbow.png)
Rainbow gradient using standard “VIBGYOR” color values is missing the cyan section in sRGB.

[{{<img src="/img/blog/2021/gradients-ramp.png">}}](/img/blog/2021/gradients-ramp.png)
Black-red-yellow-blue-white adds magenta tint around blue in Linear, and the black part goes too bright too soon.

[{{<img src="/img/blog/2021/gradients-muddy.png">}}](/img/blog/2021/gradients-muddy.png)
Random set of “muddy” colors - in Linear, yellow section is too wide & bright, and brown section is too narrow.

[{{<img src="/img/blog/2021/gradients-rbg.png">}}](/img/blog/2021/gradients-rbg.png)
Red-blue-green goes through too dark magenta/cyan in sRGB, and too bright magenta/cyan in Linear.


### Further reading

I don't *actually* know anything about color science. If the examples above piqued your interest,
reading material from people in the know might be useful. For example:

* "[A perceptual color space for image processing](https://bottosson.github.io/posts/oklab/)" by Björn Ottosson
  (2020 Dec) is the original Oklab blog post.
* "[An interactive review of Oklab](https://raphlinus.github.io/color/2021/01/18/oklab-critique.html)" by Raph Levien
  (2021 Jan) has _a lot_ of details, and an interactive gradient tool with lots of color spaces.
* "[Notes on OKLab](https://github.com/svgeesus/svgeesus.github.io/blob/master/Color/OKLab-notes.md)"
  by Chris Lilley (2021 Feb) has even more color sciencey details, and also a related
  ["Better than Lab? Gamut reduction CIE Lab & OKLab" video](https://www.youtube.com/watch?v=dOsp6u4bIwI)
  (2021 Sep).
  
*That's it!*
