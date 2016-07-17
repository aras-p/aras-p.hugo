---
tags:
- uncategorized
comments: true
date: 2005-03-06T20:35:00Z
slug: blender-first-try-uv-mapping
status: publish
title: 'Blender: first try; UV mapping'
url: /blog/2005/03/06/blender-first-try-uv-mapping/
wordpress_id: "18"
---

{{<imgright src="http://aras-p.info/img/blog/050306.png">}}
Tried [Blender](http://www.blender3d.org/) this weekend. Well, long story short: I'm totally impressed!The long story is this: for our demo we need normal mapping, ambient occlusion and similar stuff. Now, these do need unique UV parametrization of the models. The sad facts are: 1) 3dsMax sucks at automatic UV unwrapping, 2) nVidia's Melody, which I use to compute normal maps, also didn't impress me with it's auto-UV calculation much, 3) our 1 and a half of the "artists" are really busy doing models and animations.

That leaves me, the humble programmer, at the task of calculating normal maps / ambient occlusion, with the "minor" task of getting good UV parametrization from wherever it's possible.

I knew that Blender has got LSCM unwrapping some time ago _(does any of the "real" 3D packages has got it yet? Hellooo? :))_ and decided to give it a try.

And I must say it **rocks**. 3dsMax keeps irritating me all the time (it's good that most of my tasks are/were writing exporters). Blender is small, slick and the workflow seems to be much more efficient.

Given my nearly non-existent "UV mapping skills" I've unwrapped a pretty tough character model (see images - mapping such fingers ain't easy) in something like 4 hours. I guess with a little practice, I could get that to 1-2 hours. This probably isn't impressive for a real artist, but hey, I've been doing nothing but writing keywords, identifiers and semicolons for the last 8 years :)
