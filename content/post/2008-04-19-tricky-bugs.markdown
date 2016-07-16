---
categories:
- code
- work
comments: true
date: 2008-04-19T21:00:41Z
slug: tricky-bugs
status: publish
title: 'Tricky bugs: peculiarities of dynamic linking, and magic divisions'
url: /blog/2008/04/19/tricky-bugs/
wordpress_id: "166"
---

After wasting nearly two days on some really funky animation import crash, I checked in a code change with this log message:



> Fix FBX animation import crash once more. When exported symbols are not listed for a dylib, it seems to link _back_ to calling executable (?!), making them share function impls with the same name. And because Keyframe is actually different in editor vs ImportFBX, this is wrong. Apparently this is OS X Leopard only, or something. Argh.



The code change in question was just telling the compiler "here's the list of the functions that are exported from this dynamic library". The list was already there, just the compiler was never told about existence of it.

The bug manifested itself as a crash when importing animations. But it would not happen when importer was run from a small unit test application. There were no memory corruptions happening, it was not running out of memory, yet the code was crashing with access violation, usually because STL's vector was returning it's wrong size (but the actual data of the vector was correct; it was just returning bogus size). And it was doing that only on OS X Leopard, and not on OS X Tiger. _Huh?_

Turns out what did happen - and I'm not sure if that's a bug in OS X or a feature - is that the calling application did contain a class called Keyframe. And the shared library (where the crash was happening) also contained a class called Keyframe. But those classes were slightly different; first was 20 bytes in size, and second one was 16 bytes.

Now, _somehow_ when the shared library was calling vector<Keyframe>::size(), the _function from the calling application_ was used. I have no idea at all _how or why_ this was happening, but it sure was! I could see from tracing the assembly code, that it was doing difference of two pointers, and then doing _something that for sure was not_ division by 16.

What was the code doing? Turns out it was calculating division by 20 in a cunning way:

 
     mov  edx,esi   # edx = end()
     sub  edx,eax   # edx -= begin()
     mov  eax,edx   # eax = edx
     sar  eax,0x2   # eax >>= 2
     imul eax,eax,0xcccccccd # eax *= 0xcccccccd
     

In other words, the compiler was replacing division by constant (as used in vector's size()) by a shift and multiplication with a magic number. You can read more about the technique [here](http://blogs.msdn.com/devdev/archive/2005/12/12/502980.aspx) or [here](http://www.nynaeve.net/?p=115).

But of course the code above _only works_ if the number was actually divisible by 20; otherwise it returns _totally wrong_ result. This is perfectly fine for computing the difference in two pointers to structures of known size... Except that inside the shared library the Keyframe structures are 16 bytes, and not 20!

So yeah. Watch out for peculiarities of dynamic linking on your platform.
