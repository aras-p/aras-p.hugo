---
categories:
- code
comments: true
date: 2005-10-26T08:30:00Z
slug: debugging-plus
status: publish
title: Debugging plus
url: /blog/2005/10/26/debugging-plus/
wordpress_id: "75"
---

Yesterday I had a cool debugging session while working on my [HDR demo](/blog/2005/10/23/jumped-onto-hdr-bandwagon). One of postprocessing filters produced weird results and I went off to investigate that. The usual tricks: debugging in Visual Studio to make sure right sample offsets are generated; D3D debug runtime, D3DX debug, reference rasterizer, firing up NVPerfHud and doing frame analysis, doing full capture with PIX and inspecting device state, etc.

Nothing helped.

Then I noticed that in the pixel shader, I wrote  

    sample = tex2D( s0, uv + vSmpOffsets[i] )

instead of  

    sample += tex2D( s0, uv + vSmpOffsets[i] )

Aaargh. So much for a plus sign.

How to deal with such bugs? Why some bugs are trivial to find, and some are hard? Why sometimes (often?) the time required to find the bug does not correlate with bug's "trickiness"? Why sometimes I can find a tricky bug in big unknown codebase in a couple of minutes; yet spend two hours on the plus sign in my own small code?

I've got no answers to the above.

By the way: PIX is a great tool, but D3D guys should really polish the UI :)

