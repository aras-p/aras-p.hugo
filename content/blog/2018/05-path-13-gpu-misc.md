---
title: "Pathtracer 13: GPU threadgroup memory is useful!"
date: 2018-05-28T19:53:20+03:00
tags: ['rendering', 'code', 'gpu']
comments: true
---

*Introduction and index of this series [is here](/blog/2018/03/28/Daily-Pathtracer-Part-0-Intro/)*.

> Oh, last post was exactly a month ago... I guess I'll remove "daily" from the titles then :)
>
> So the [previous approach](/blog/2018/04/25/Daily-Pathtracer-12-GPU-Buffer-Oriented-D3D11/)
> "let's do one bounce iteration per pass" (a.k.a. "buffer oriented") turned out to add a whole lot
> of complexity, and was not really faster. So you know what, let's park that one for now; maybe we'll
> return to something like that once (if ever) we either actually need it, or perhaps when we'll work
> on smaller ray packets that don't need hundreds-of-megabytes of ray buffers.

Scott Bean ([@gfxbean](https://twitter.com/gfxbean)) sent a little hint that in my "regular, super simple"
[GPU implementation](/blog/2018/04/16/Daily-Pathtracer-10-Update-CsharpGPU/) I might get much better
performance by moving scene/material data into `groupshared` memory. As we've seen in the
[previous post](/blog/2018/04/25/Daily-Pathtracer-12-GPU-Buffer-Oriented-D3D11/), using group shared
memory can speed things up quite a lot, and in this case *all threads* will be going through exactly
the same spheres to check rays against.

All that work is completely isolated inside the compute shader *(nice!)*, and conceptually goes like this:
```c++
groupshared Foo s_GroupFoo[kMaxFoos];

// at start of shader:
CopyFoosFromStructuredBuffersInto(s_GroupFoo);

ThreadGroupMemoryBarrier(); // sync threads in the group

// proceed as usual, just use s_GroupFoo instead
// of StructuredBuffer<Foo> variable
```

#### D3D11

The actual commit for D3D11 [is here](https://github.com/aras-p/ToyPathTracer/commit/82914e5c23e1cc4034c2ab82d1671b7d7bb4b443),
and is pretty self-explanatory. At start of shader I make each thread do a little bit of "copy" work like this:
```c++
void main(uint3 tid : SV_GroupThreadID)
{
    uint threadID = tid.y * kCSGroupSizeX + tid.x;
    uint groupSize = kCSGroupSizeX * kCSGroupSizeY;
    uint objCount = g_Params[0].sphereCount;
    uint myObjCount = (objCount + groupSize - 1) / groupSize;
    uint myObjStart = threadID * myObjCount;
    for (uint io = myObjStart; io < myObjStart + myObjCount; ++io)
    {
        if (io < objCount)
        {
            s_GroupSpheres[io] = g_Spheres[io];
            s_GroupMaterials[io] = g_Materials[io];
        }
        if (io < g_Params[0].emissiveCount)
        {
            s_GroupEmissives[io] = g_Emissives[io];
        }
    }
    GroupMemoryBarrierWithGroupSync();
```

I also reduced thread group size from 16x16 to 8x8 since that was a bit faster on my GPU *(may or might not be faster
on any other GPU...)*. What's the result? NVIDIA GeForce 1080 Ti: **778 -> 1854 Mray/s**.

So that's 2.4x faster for a fairly simple (and admittedly not trivially scalable to large scenes) change! However...
a quick test on Radeon Pro WX 9100: says: 1200 -> 1100 Mray/s, so a bit slower. I haven't investigated why, but I guess the takeaways
are:

1. Pre-caching compute shader data into thread group shared memory can make it a lot faster!
1. Or it might make it slower on a different GPU.
1. _Good luck!_


#### Metal

I did the same change in the Metal implementation; [here's the commit](https://github.com/aras-p/ToyPathTracer/commit/e16b30a6cf729b876322bef26c8b6e4658aadbb2) -
pretty much the same as what is there on D3D11.
The result? MacBook Pro (2013) with Intel Iris Pro 60.8 -> 42.9 Mray/s. (oꆤ︵ꆤo)

Why? No idea; Mac has no tooling to answer this question, as far as I can tell.

And then I did a change that I thought of *totally at random*, just because I modified these lines of code and started to think
*"I wonder what would happen if I..."*. In the shader, several places had code like `const Sphere& s = spheres[index]` -- initially
came from the code being a direct copy from C++. I changed these places to copy into local variables by value, instead
of having a const reference, i.e. `Sphere s = spheres[index]`.

Here's [the commit](https://github.com/aras-p/ToyPathTracer/commit/ec4eac597bef44120cfb0408ee48bd869f6dbd86), and that tiny
change got the performance up to **98.7 Mray/s** on Intel Iris Pro.

Why? Who knows! I would have expected any "[sufficiently smart compiler](http://wiki.c2.com/?SufficientlySmartCompiler)"
to have compiled both versions of code into exact same result. Turns out, nope, one of them is 2x faster, _good luck_!

Metal shaders are a bit of a black box, with not even intermediate representation being publicly documented. Good thing is...
turns out the IR is just LLVM bitcode ([via @icculus](https://twitter.com/icculus/status/721893213452312576)).
So I grabbed a random `llvm-dis` I had on my machine (from Emscripten SDK, of all places), checked which output file Xcode
produces for the `*.metal` inputs, and ran it on both versions.

[{{<img src="/img/blog/2018/rt-metal-xcode-output.png">}}](/img/blog/2018/rt-metal-xcode-output.png)

The resulting LLVM IR disassembly is not very easy on the eyes, looking generally like this:
```txt
; <label>:13:                                     ; preds = %54, %10
  %14 = phi float [ %5, %10 ], [ %56, %54 ]
  %15 = phi i32 [ -1, %10 ], [ %55, %54 ]
  %16 = phi i32 [ 0, %10 ], [ %57, %54 ]
  %17 = sext i32 %16 to i64
  %18 = getelementptr inbounds %struct.Sphere, %struct.Sphere addrspace(3)* %2, i64 %17
  %19 = bitcast %struct.Sphere addrspace(3)* %18 to i8 addrspace(3)*
  call void @llvm.memcpy.p0i8.p3i8.i64(i8* %11, i8 addrspace(3)* %19, i64 20, i32 4, i1 false), !tbaa.struct !47
  br label %20
; <label>:20:                                     ; preds = %20, %13
  %21 = phi i32 [ 0, %13 ], [ %30, %20 ]
  %22 = phi <4 x float> [ undef, %13 ], [ %29, %20 ]
  %23 = sext i32 %21 to i64
  %24 = getelementptr inbounds %struct.Sphere, %struct.Sphere* %8, i64 0, i32 0, i32 0, i64 %23
  %25 = load float, float* %24, align 4, !tbaa !46
```

I'm not fluent in reading it, but by diffing the two versions, it's not immediately obvious why one would be slower
than the other. The slow one has some more `load` instructions with `addrspace(3)` on them, whereas the fast one
has more calls into `alloca` (?) and `llvm.memcpy.p0i8.p3i8.i64`. Ok I guess? The alloca calls are probably not "real"
calls; they just end up marking up how much of thread local space will get needed after all inlining. Memcpy probably
ends up being a bunch of moves in exactly once place, so if GPU has any sort of load coalescing, then that gets used
there. Or that's my theory for "why faster".

So Metal takeaways might be:

1. By-value instead of by-const-reference things might be much more efficient.
1. Metal bytecode is "just" LLVM IR, so peeking into that with `llvm-dis` can be useful. Note that this is still
   a machine-independent, very high level IR; you have no visibility into what the GPU driver will make of it
   in the end.


### Current status and what's next

So this simple change to pre-cache sphere/material/emissive data into thread group shared memory got GPU performance
up to:

* PC (GeForce 1080 Ti): 778 -> 1854 Mray/s,
* Mac (Intel Iris Pro): 61 -> 99 Mray/s.

Which is not bad for such a simple change. Current code is over at [`13-gpu-threadgroup-opt` tag on github](https://github.com/aras-p/ToyPathTracer/tree/13-gpu-threadgroup-opt/Cpp).

What's next? I'm not sure. Maybe I should look at moving this out of "toy" stage and add bounding volume hierarchy & triangle
meshes support?
