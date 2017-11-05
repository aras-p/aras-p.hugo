---
tags:
- opengl
- rant
comments: true
date: 2007-03-22T23:51:00Z
slug: arb_vertex_buffer_object-is-stupid
status: publish
title: ARB_vertex_buffer_object is stupid
url: /blog/2007/03/22/arb_vertex_buffer_object-is-stupid/
wordpress_id: "105"
---

OpenGL vertex buffer functionality, I [mock thee](http://www.stevestreeting.com/?p=489) too! Why couldn't they make the [specification](http://oss.sgi.com/projects/ogl-sample/registry/ARB/vertex_buffer_object.txt) simple&clear, and then why can't the implementations work as expected?

It started out like this: converting some existing code that generates geometry on the fly. It used to generate that into in-memory arrays and then Just Draw Them. Probably not the most optimal solution, but that's fine. Of course we can optimize that, right?

So with all my knowledge how things used to work in D3D I start "I'll just do the same in OpenGL" adventure. Create a single big dynamic vertex buffer, a single big dynamic element buffer; update small portions of it with glBufferSubData, "discard" it (=glBufferData with null pointer) when the end is reached, rinse & repeat.

Now, let's for a moment ignore the fact that updating portions of index buffer does not actually work on Mac OS X... Everything else is fine and it actually works! Except for... it's quite a lot slower than just doing the old "render from memory" thing. Ok, must be some OS X specific thing... Nope, on a Windows box with GeForce 6800GT it is still slower.

Now, there are three things that could have gone wrong: 1) I did something stupid (quite likely), 2) VBOs for dynamically updated chunks of geometry suck (could be... they don't have a way to update just one chunk without one extra memory copy at least), 3) both me and VBOs are stupid. If I was me I'd bet on the third option.

What I don't get is: D3D has had a buffer model that is simple to understand and actually works for, like, 6 years now! Why ARB_vertex_buffer_object guys couldn't just copy that? The world would be a better place! No, instead they make a way to map only whole buffer; updating chunks is extra memory copy; there are confusing usage parameters (when should I use STREAM and when DYNAMIC?); performance costs are unclear (when is [glBufferSubData faster than glMapBuffer](http://www.stevestreeting.com/?p=491)?) etc. And in the end when an OpenGL noob like me tries to actually make them work - he can't! It's slow!

