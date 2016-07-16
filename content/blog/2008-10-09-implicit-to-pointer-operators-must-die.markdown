---
categories:
- code
- rant
- work
comments: true
date: 2008-10-09T15:15:26Z
slug: implicit-to-pointer-operators-must-die
status: publish
title: Implicit to-pointer operators must die!
url: /blog/2008/10/09/implicit-to-pointer-operators-must-die/
wordpress_id: "223"
---

> For the sake of the nation,  
> this operator must die!



Seriously. Suppose there is some class, let's say `ColorRGBAf`. That has four floats inside. Now, someone at some point decided to add this operator to it:



    operator float* () { /* */ }
    operator const float* () const { /* */ }



Probably because it's easier to pass color to OpenGL this way, or something like that.

This is evil. Like, really **evil**. Especially if that class did not have comparison operators defined, and some totally unrelated code four years later does:


    if (color != oldColor) { /* ... */ }



Ouch! Sounds like someone will spend four hours debugging something that looks like an event routing issue that _only_ happens on Windows and _only_ with optimizations on _(yes, I just did that...)_.

What happens here? The compiler takes pointers to two colors and compares _the pointers_. If for some reason both colors are temporary objects, then it can even happen that _both_ get folded into the same variable/register/whatnot. The pointers are the same. Ouch!

Implicit "nice" operators are just disguised evil. Remove that operator, add something like `GetPointer()` to class if someone really wants to use that, and better even make the comparison operators private and without implementations. Yes. Much better.
