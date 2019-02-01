---
title: "Best unknown MSVC flag: d2cgsummary"
date: 2017-10-23T08:10:57+03:00
tags: ["code", "compilers", "devtools"]
comments: true
---

I've been looking at C++ compilation times lately (e.g.
[here](/blog/2017/08/08/Unreasonable-Effectiveness-of-Profilers/) or
[there](/blog/2017/10/09/Forced-Inlining-Might-Be-Slow/)), and through correspondence
with Microsoft C++ compiler folks learned about a fairly awesome, but undocumented, `cl.exe` flag.

It's `/d2cgsummary`.


### How to investigate slow compile times?

Given that compile/build times are a massive problem in large C++ codebases, it's amazing how little information
we can get about _why_ they are slow. For example when using Visual Studio C++ compiler, about the only
options seem to be:

**1) Use [`/Bt` flag](https://blogs.msdn.microsoft.com/vcblog/2010/04/01/vc-tip-get-detailed-build-throughput-diagnostics-using-msbuild-compiler-and-linker/)**,
to get time spent in frontend (`c1xx.dll`) vs backend (`c2.dll`) compiler parts. This is good to narrow down
whether slow compiles are due to too many things being parsed (e.g. a problem of including too many header files), which
is typically a frontend problem, or is the problem some sort of language constructs that cause the backend (typically
the optimizer part of the compiler) take "a lot of time".

The "common knowledge" is that long C++ compile times are caused by too many header files being included, and in general
by C/C++ `#include` model. That is often the case, but sometimes it's the _backend_ part of the compiler that takes "forever"
to compile something (e.g. the `__forceinline` [compile time issue](/blog/2017/10/09/Forced-Inlining-Might-Be-Slow/)
I found in my previous post). The backend part typically only takes "long" when compile optimizations are on.

**2) Take a profiler capture of `cl.exe`**, using any of existing native code profiling tools (e.g.
[xperf](/blog/2015/01/09/curious-case-of-slow-texture-importing/)), load symbols, and try to guess what is causing the 
long compile time based on profiling results.

**3) Just do guesses** and kinda randomly change code structure and see whether it helped.

Well, turns out there's another option, the `/d2cgsummary` flag.


### MSVC /d2cgsummary flag

I can't find _anything_ about this flag on the internet, so I'd assume it's something "for internal use only",
and may or might not exist or be changed in any MSVC compiler version. It does seem to exist & work in VS2015 Update 3
and in VS2017. Passing it to `cl.exe` will give an output of "Anomalistic Compile Times" and some sort of "Caching Stats", like this:

```txt
Code Generation Summary
        Total Function Count: 742
        Elapsed Time: 13.756 sec
        Total Compilation Time: 13.751 sec
        Efficiency: 100.0%
        Average time per function: 0.019 sec
        Anomalistic Compile Times: 1
                ?EvaluateRootMotion@animation@mecanim@@YAXAEBUClipMuscleConstant@12@AEBUClipMuscleInput@12@PEBM2AEAUMotionOutput@12@_N@Z: 11.667 sec, 0 instrs
        Serialized Initializer Count: 4
        Serialized Initializer Time: 0.006 sec
RdrReadProc Caching Stats
        Functions Cached: 61
        Retrieved Count: 20162
        Abandoned Retrieval Count: 0
        Abandoned Caching Count: 14
        Wasted Caching Attempts: 0
        Functions Retrieved at Least Once: 61
        Functions Cached and Never Retrieved: 0
        Most Hits:
                ?f@?$load@$00@?$SWIZ@$0DCB@@v4f@meta@math@@SA?AT__m128@@T6@@Z: 2775
                ?f@?$SWIZ@$0DCB@@v4f@meta@math@@SA?AT__m128@@T5@@Z: 2774
                ?f@?$load@$00@?$SWIZ@$0EDCB@@v4f@meta@math@@SA?AT__m128@@T6@@Z: 1924
                ?f@?$SWIZ@$0EDCB@@v4f@meta@math@@SA?AT__m128@@T5@@Z: 1920
                ?pack@?$v@Uv4f@meta@math@@U?$sp@Uv4f@meta@math@@$0DCB@$02@23@$02@meta@math@@SA?AU123@AEBT__m128@@@Z: 1296
                ??B_float4@math@@QEBA?AT__m128@@XZ: 1165
```

**Anomalistic Compile Times** seems to output "longest to compile" functions, where I guess their times are much longer
than the average. Here, it immediately says that among all 700+ functions that were compiled, there is one that takes
almost all the time! The names are [mangled](https://en.wikipedia.org/wiki/Name_mangling),
but using either the `undname.exe` MSVC utility or an online [demangler](https://demangler.com/), it says the
slow-to-compile function in this particular file was called `mecanim::animation::EvaluateRootMotion`.

This means that if I want to speed up compilation of this file, I need to only look at that one function, and *somehow*
figure out why it's slow to compile. The "why" is still an exercise for the reader, but at least I know I don't have to investigate
700 other functions. Great!

**RdrReadProc Caching Stats** section I don't _quite_ understand, but it _seems_ to be listing functions that were used
_most often_ in the whole compilation. Again after demangling, seems that `math::meta::v4f::SWIZ<801>::load` got used
almost 3000 times, etc.


On some other files I was looking into, the `/d2cgsummary` flag did not point to one slow function, but at least pointed
me to a subsystem that might be contributing to slow compile times. For example here:

```txt
Code Generation Summary
        Total Function Count: 4260
        Elapsed Time: 20.710 sec
        Total Compilation Time: 20.685 sec
        Efficiency: 99.9%
        Average time per function: 0.005 sec
        Anomalistic Compile Times: 32
                ?VirtualRedirectTransfer@Shader@@UEAAXAEAVRemapPPtrTransfer@@@Z: 0.741 sec, 0 instrs
                ??$TransferBase@VShader@@@RemapPPtrTransfer@@QEAAXAEAVShader@@W4TransferMetaFlags@@@Z: 0.734 sec, 0 instrs
                ??$Transfer@VShader@@@RemapPPtrTransfer@@QEAAXAEAVShader@@PEBDW4TransferMetaFlags@@@Z: 0.726 sec, 0 instrs
                ??$Transfer@VRemapPPtrTransfer@@@Shader@@QEAAXAEAVRemapPPtrTransfer@@@Z: 0.711 sec, 0 instrs
                ??$Transfer@VRemapPPtrTransfer@@@?$SerializeTraits@VShader@@@@SAXAEAVShader@@AEAVRemapPPtrTransfer@@@Z: 0.708 sec, 0 instrs
                ??$Transfer@USerializedShader@ShaderLab@@@RemapPPtrTransfer@@QEAAXAEAUSerializedShader@ShaderLab@@PEBDW4TransferMetaFlags@@@Z: 0.632 sec, 0 instrs
                ??$Transfer@VRemapPPtrTransfer@@@?$SerializeTraits@USerializedShader@ShaderLab@@@@SAXAEAUSerializedShader@ShaderLab@@AEAVRemapPPtrTransfer@@@Z: 0.625 sec, 0 instrs
                ??$Transfer@VRemapPPtrTransfer@@@SerializedShader@ShaderLab@@QEAAXAEAVRemapPPtrTransfer@@@Z: 0.621 sec, 0 instrs
                <...>
RdrReadProc Caching Stats
        Functions Cached: 227
        Retrieved Count: 74070
        Abandoned Retrieval Count: 0
        Abandoned Caching Count: 0
        Wasted Caching Attempts: 0
        Functions Retrieved at Least Once: 221
        Functions Cached and Never Retrieved: 6
        Most Hits:
                ?GetNewInstanceIDforOldInstanceID@RemapPPtrTransfer@@QEAAHH@Z: 3065
                ?MightContainPPtr@?$SerializeTraits@D@@SA_NXZ: 2537
                ?Align@TransferBase@@QEAAXXZ: 1965
                <...>
```

Out of total 20 second compile time, there's a bunch of functions that take almost 1 second to compile each (after demangling:
`Shader::VirtualRedirectTransfer(class RemapPPtrTransfer)`, `RemapPPtrTransfer::TransferBase<class Shader>(class Shader&, enum TransferMetaFlags)`) etc.
So hey, out of everything that Shader.cpp does, I know that in our codebase it's the "data serialization" part that is slow
to compile.


> What would be super useful to have from compiler vendors: way more diagnostics on what & why something is slow.
> Detailed reports like this flag, and/or custom ETW providers for xperf, or anything like that. These would not
> be things used by most people, but when someone **has to** use them, they would be invaluable. Saving a minute or two
> of build time for several hundred programmers is an **enormous** amount of productivity gain!


Anyway, now you know. If you use Microsoft's C++ compiler, try out `/d2cgsummary` flag to `cl.exe`. You might find something
interesting! Next time, how this flag pointed me towards a particular programming pattern that is slow to compile
on MSVC, and allowed saving 80 seconds of compile time of one file.

*Update:* An item on
[Visual Studio feedback site](https://visualstudio.uservoice.com/forums/121579-visual-studio-ide/suggestions/31999147)
to document the flag and perhaps some others that might be useful.

*Update 2:* "[Visual Studio 2017 Throughput Improvements and Advice](https://blogs.msdn.microsoft.com/vcblog/2018/01/04/visual-studio-2017-throughput-improvements-and-advice/)"
post went up on Visual C++ team blog with `/d2cgsummary` and other useful compiler throughput tips.

