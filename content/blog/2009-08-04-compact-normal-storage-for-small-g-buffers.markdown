---
categories:
- d3d
- gpu
- rendering
- work
comments: true
date: 2009-08-04T11:39:51Z
slug: compact-normal-storage-for-small-g-buffers
status: publish
title: Compact Normal Storage for small g-buffers
url: /blog/2009/08/04/compact-normal-storage-for-small-g-buffers/
wordpress_id: "377"
---

I've been experimenting with compact storage of view space normals for small g-buffers. Think about storing depth and normal in a single 8 bit/channel RGBA texture.

[**Here are my findings**](http://aras-p.info/texts/CompactNormalStorage.html) - with error visualization and shader performance numbers for some GPUs.

If you know any other method to encode/store normals in a compact way, please let me know!
