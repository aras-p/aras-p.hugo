---
tags:
- gpu
comments: true
date: 2005-02-05T13:55:00Z
slug: the-video-cards-are-damn-fast
status: publish
title: The video cards are damn fast
url: /blog/2005/02/05/the-video-cards-are-damn-fast/
wordpress_id: "9"
---

I was working on our next demo the other day. Boy, the video cards are damn fast nowadays!

We have a high-poly model for the main character (~200k tris), for the demo we use low-poly (~6500 tris) and a normalmap. Now, I've put 128 lights scattered on the hemisphere above him, each using shadow buffer. I have 4 shadow buffers, render to these from four lights, then render the character, fetching shadows from four shadowmaps at once. The result is that it's almost realtime ambient occlusion for the animating character, and it runs at ~40FPS on my geforce 6800gt!

This is of course pretty useless, we don't need realtime AO in the demo. But it has been nice :)
