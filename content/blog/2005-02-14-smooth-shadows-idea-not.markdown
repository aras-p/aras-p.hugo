---
tags:
- uncategorized
comments: true
date: 2005-02-14T12:44:00Z
slug: smooth-shadows-idea-not
status: publish
title: Smooth shadows idea (not)
url: /blog/2005/02/14/smooth-shadows-idea-not/
wordpress_id: "12"
---

So, I had [this](/blog/2005/02/11/things-to-try) smooth shadows idea recently. Turns out that it's not an idea, Willem de Boer already has done a very similar thing ([Smooth Penumbra Transitions with Shadow Maps](http://www.whdeboer.com/writings.html)). Now, the only way to have my idea back is to 'optimize' (i.e. hack/cheat) this method. Specifically, if we target projected shadows (not real shadowmaps), and can do some approximations, then we can get it working faster. I've done some experiments, right now it involves rendering to shadow map once, then Gaussian blurring it (two separable passes), then Poisson-blurring it. Works pretty fast :) I need to experiment some more and see if I can get anything.

