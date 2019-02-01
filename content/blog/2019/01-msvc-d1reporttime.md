---
title: "Another cool MSVC flag: /d1reportTime"
date: 2019-01-21T19:01:10+03:00
tags: ['code', 'compilers', 'devtools']
comments: true
---

A while ago I found about a fairly useful Visual C++ flag that helps to investigate where the
compiler backend/optimizer spends time -- `/d2cgsummary`, see
[blog post](/blog/2017/10/23/Best-unknown-MSVC-flag-d2cgsummary/).

Turns out, there is a flag that reports where the compiler frontend spends time -- **`/d1reportTime`**.

I've been writing about lack of compiler time profiling/investigation tools, starting from that
same [old post](/blog/2017/10/23/Best-unknown-MSVC-flag-d2cgsummary/), up to the more recent ones
that focused on clang - [one](/blog/2019/01/12/Investigating-compile-times-and-Clang-ftime-report/)
and [two](/blog/2019/01/16/time-trace-timeline-flame-chart-profiler-for-Clang/).
All this recent discussion about C++ compilation times *(among other issues...)* led me
to finding this fresh project on github,
[**UECompileTimesVisualizer**](https://github.com/Phyronnaz/UECompileTimesVisualizer) by
[@phyronnaz](https://twitter.com/phyronnaz). Ignore everything
about UE4 there *(unless you work with it of course :))*, the new thing for me was that it says:

> Add the following arguments to the C/C++ Command Line option in your project settings: /Bt+ /d2cgsummary /d1reportTime

`/Bt+` and `/d2cgsummary` are known, but `/d1reportTime` I haven't heard about before. What is it?!
The *only* other place on the internet that talks about it -- and it's fairly impressive for anything to have *just two* mentions
on the internet -- is a small comment in Microsoft
[forums thread](https://developercommunity.visualstudio.com/content/problem/377398/c-compiler-extremely-slow-since-158.html) a couple months ago, and the only thing it says
is: *"Can you also share the output from /d1reportTime?"*


### So what is /d1reportTime?

Passing **`/d1reportTime`** to the MSVC compiler (`cl.exe`) will make it print:

* Which header files are included (hierarchically), with time taken for each,
* Which classes are being parsed, with time taken for each,
* Which functions are being parsed, with time taken for each.

Additionally, at end of each list, "top N" are listed too. List of classes/functions seems to contain
info about which templates (or nested classes? not sure yet) are being instantiated as well.

I have no idea which version of MSVC the flag has appeared in. VS2015 seems to not have it yet; and
VS2017 15.7 (compiler version 19.14.26428) already has it; it might have appeared in earlier VS2017 builds
but I haven't checked.

Running it on anything non-trivial produces tons of output, e.g. I randomly ran it on a (fairly big)
`Shader.cpp` file we have in our codebase, and it produces 30 thousand lines of output. Shortened *a lot*:

```txt
Shader.cpp
Include Headers:
  Count: 542
    c:\trunk\runtime\shaders\shader.h: 0.311655s
      c:\trunk\runtime\shaders\shadertags.h: 0.004842s
      c:\trunk\runtime\shaders\shaderpropertysheet.h: 0.127829s
        c:\trunk\runtime\math\color.h: 0.021522s
          c:\trunk\runtime\serialize\serializeutility.h: 0.012031s
            c:\trunk\runtime\serialize\serializationmetaflags.h: 0.005427s
            c:\trunk\runtime\utilities\enumtraits.h: 0.004738s
          c:\trunk\runtime\testing\testingforwarddecls.h: 0.000331s
        c:\trunk\runtime\math\vector2.h: 0.003681s
        <... a lot more skipped ...>

  Top 24 (top-level only):
    c:\trunk\runtime\graphics\scriptablerenderloop\scriptablerendercontext.h: 0.619189s
    c:\trunk\runtime\graphics\scriptablerenderloop\scriptablebatchrenderer.h: 0.407027s
    c:\trunk\runtime\shaders\shader.h: 0.311655s
    c:\trunk\runtime\shaders\shadernameregistry.h: 0.263254s
    <... a lot more skipped ...>

  Total: 2.125453s
Class Definitions:
  Count: 10381
    ShaderTagID: 0.001146s
    EnumTraits::internal::IsReflectableEnum<`template-type-parameter-1'>: 0.000057s
    EnumTraits::internal::DefaultTraitsHelper<`template-type-parameter-1',1>: 0.000085s
    EnumTraits::internal::DefaultTraitsHelper<`template-type-parameter-1',0>: 0.000064s
    <... a lot more skipped ...>

  Top 100 (top-level only):
    std::_Vector_const_iterator<class std::_Vector_val<struct std::_Simple_types<struct ShaderLab::SerializedProperty> > >: 0.031536s
    PersistentManager: 0.029135s
    ModuleMetadata: 0.029010s
    ShaderLab::SerializedSubProgram: 0.020047s
    GlobalCallbacks: 0.019955s
    <... a lot more skipped ...>

  Total: 1.412664s
Function Definitions:
  Count: 20544
    ShaderTagID::ShaderTagID: 0.000368s
    ShaderTagID::ShaderTagID: 0.000024s
    operator ==: 0.000089s
    operator !=: 0.000017s
    operator <: 0.000021s
    <... a lot more skipped ...>

  Top 100 (top-level only):
    std::operator ==: 0.031783s
    ShaderLab::SerializedTagMap::Transfer: 0.014827s
    math::matrixToQuat: 0.014715s
    math::mul: 0.012467s
    Texture::TextureIDMapInsert: 0.012006s
    <... a lot more skipped ...>

  Total: 2.658951s
```

At 30k lines it is not the most intuitive for manual use, but a lot of interesting information *is* there.
Particularly the "Top N" lists sound like good things to start with, if looking at the output manually.

Even for something simple like (see in [Compiler Explorer](https://godbolt.org/z/NVcxdT)):
```c++
#include <vector>
int main()
{
    std::vector<int> v;
    v.push_back(13);
    return (int)v.size();
}
```

The `/d1reportTime` produces almost 2000 lines of output. Again, shortened here:

```txt
example.cpp
Include Headers:
    Count: 74
        c:\msvc\v19_16\include\vector: 0.215198s
            c:\msvc\v19_16\include\xmemory: 0.186372s
                c:\msvc\v19_16\include\xmemory0: 0.184278s
                    c:\msvc\v19_16\include\cstdint: 0.015689s
                        c:\msvc\v19_16\include\yvals.h: 0.015103s
                            c:\msvc\v19_16\include\yvals_core.h: 0.012148s
                                c:\msvc\v19_16\include\xkeycheck.h: 0.000246s
                                c:\msvc\v19_16\include\crtdefs.h: 0.011183s
                                <... skipped remaining 66 headers ...>

    Top 1 (top-level only):
        c:\msvc\v19_16\include\vector: 0.215198s
    Total: 0.215198s
Class Definitions:
    Count: 829
        _PMD: 0.000260s
        _TypeDescriptor: 0.000088s
        _s__CatchableType: 0.000113s
        _s__CatchableTypeArray: 0.000025s
        _s__ThrowInfo: 0.000044s
        _s__RTTIBaseClassDescriptor: 0.000056s
        _s__RTTIBaseClassArray: 0.000027s
        _s__RTTIClassHierarchyDescriptor: 0.000026s
        _s__RTTICompleteObjectLocator2: 0.000048s
        __s_GUID: 0.000049s
        <add_rvalue_reference><`template-type-parameter-1'>: 0.000012s
        <remove_reference><`template-type-parameter-1'>: 0.000006s
        <remove_reference><class $RBAAB &>: 0.000005s
        <... skipped remaining 816 classes ...>

    Top 100 (top-level only):
        std::basic_string<char,struct std::char_traits<char>,class std::allocator<char> >: 0.006266s
        std::vector<int,class std::allocator<int> >: 0.003368s
        std::pair<`template-type-parameter-1',`template-type-parameter-2'>: 0.002695s
        std::basic_string<`template-type-parameter-1',`template-type-parameter-2',`template-type-parameter-3'>: 0.001880s
        std::vector<`template-type-parameter-1',`template-type-parameter-2'>: 0.001117s
        <... skipped remaining 95 top classes ...>

    Total: 0.059558s
Function Definitions:
    Count: 773
        operator new: 0.000162s
        operator delete: 0.000031s
        operator new[]: 0.000025s
        operator delete[]: 0.000009s
        abs: 0.000048s
        abs: 0.000021s
        div: 0.000059s
        div: 0.000043s
        fpclassify: 0.000055s
        <... skipped remaining 764 functions ...>

    Top 100 (top-level only):
        std::logic_error::logic_error: 0.006603s
        main: 0.003693s
        std::_Uninitialized_move: 0.003665s
        std::vector<int,class std::allocator<int> >::push_back: 0.001054s
        std::numeric_limits<bool>::round_error: 0.000730s
        std::vector<int,class std::allocator<int> >::_Emplace_reallocate: 0.000612s
        _scwprintf_p: 0.000414s
        <... skipped remaining 93 top functions ...>
    Total: 0.056901s
```

Having reports like that is also useful to realize just *how much work* we're asking the compiler to do.
The C++ snippet above is "as simple as it gets" when using "standard" C++ features. Just make a vector
of integers, and add a number to it. The compiler ends up including 74 headers, parsing over 800 classes and
almost 800 functions. Just to make a simple "push an integer into a vector" task.

> Yes, I know about precompiled headers. And I know about upcoming C++ modules.
> You can save on sending that email :)


Anyway, having that tooling is really good! Thanks Microsoft Visual C++ team!
Also, [u/gratilup](https://www.reddit.com/user/gratilup) mentions in
[this reddit thread](https://www.reddit.com/r/cpp/comments/agv34v/timetrace_timeline_flame_chart_profiler_for_clang/eegqevb/?st=jr6n23xf&sh=99d858e5)
that MSVC team is working on ETW/WPA based tooling to visualize compilation times. Great!

