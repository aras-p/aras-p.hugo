---
title: "Slow to Compile Table Initializers"
date: 2017-10-24T20:23:39+03:00
tags: ["code", "devtools"]
comments: true
---
Continuing on finding low hanging fruit in our codebase (Unity game engine, a lot of C++ code) build
times with Visual Studio, here's what I found with help of `/d2cgsummary` (see
[previous blog post](/blog/2017/10/23/Best-unknown-MSVC-flag-d2cgsummary/)).

Noticed one file that was taking 92 seconds to compile on my machine, in Release config.
A quick look at it did not reveal any particularly crazy code structure;
was fairly simple & "obvious" code. However, `/d2cgsummary` said:
```txt
Anomalistic Compile Times: 44
  ?IsFloatFormat@@YA_NW4GraphicsFormat@@@Z: 5.493 sec
  ?IsHalfFormat@@YA_NW4GraphicsFormat@@@Z: 5.483 sec
  ?GetGraphicsFormatString@@YA?AV?$basic_string@DV?$StringStorageDefault@D@core@@@core@@W4GraphicsFormat@@@Z: 4.680 sec
  ?ComputeMipmapSize@@YA_KHHW4GraphicsFormat@@@Z: 4.137 sec
  ?ComputeTextureSizeForTypicalGPU@@YA_KHHHW4GraphicsFormat@@HH_N@Z: 4.087 sec
  ?GetComponentCount@@YAIW4GraphicsFormat@@@Z: 2.722 sec
  ?IsUNormFormat@@YA_NW4GraphicsFormat@@@Z: 2.719 sec
  ?Is16BitPackedFormat@@YA_NW4GraphicsFormat@@@Z: 2.700 sec
  ?IsAlphaOnlyFormat@@YA_NW4GraphicsFormat@@@Z: 2.699 sec
  <...>
```

A bunch of functions taking 2-5 seconds to compile each; and each one of those looking *very* simple:
```
bool IsFloatFormat(GraphicsFormat format)
{
    return ((GetDesc(format).flags & kFormatPropertyIEEE754Bit) != 0) && (GetDesc(format).blockSize / GetComponentCount(format) == 4);
}
```

**Why on earth a function like that would take 5 seconds to compile?!**

Then I noticed that all of them call `GetDesc(format)`, which looks like this:

```
const FormatDesc& GetDesc(GraphicsFormat format)
{
    static const FormatDesc table[] = //kFormatCount
    {
        // bSize,bX,bY,bZ, swizzleR,  swizzleG,        swizzleB,        swizzleA,        fallbackFormat,    alphaFormat,       textureFormat,    rtFormat,        comps, name,   flags
        {0, 0, 0, 0, kFormatSwizzle0, kFormatSwizzle0, kFormatSwizzle0, kFormatSwizzle1, kFormatNone,       kFormatNone,       kTexFormatNone,   kRTFormatCount,  0, 0,  "None", 0},                                                                            // None,
        {1, 1, 1, 1, kFormatSwizzleR, kFormatSwizzle0, kFormatSwizzle0, kFormatSwizzle1, kFormatRGBA8_SRGB, kFormatRGBA8_SRGB, kTexFormatNone,   kRTFormatCount,  1, 0,  "",     kFormatPropertyNormBit | kFormatPropertySRGBBit | kFormatPropertyUnsignedBit}, // R8_SRGB
        {2, 1, 1, 1, kFormatSwizzleR, kFormatSwizzleG, kFormatSwizzle0, kFormatSwizzle1, kFormatRGBA8_SRGB, kFormatRGBA8_SRGB, kTexFormatNone,   kRTFormatCount,  2, 0,  "",     kFormatPropertyNormBit | kFormatPropertySRGBBit | kFormatPropertyUnsignedBit}, // RG8_SRGB
        {3, 1, 1, 1, kFormatSwizzleR, kFormatSwizzleG, kFormatSwizzleB, kFormatSwizzle1, kFormatRGBA8_SRGB, kFormatRGBA8_SRGB, kTexFormatRGB24,  kRTFormatCount,  3, 0,  "",     kFormatPropertyNormBit | kFormatPropertySRGBBit | kFormatPropertyUnsignedBit}, // RGB8_SRGB
        {4, 1, 1, 1, kFormatSwizzleR, kFormatSwizzleG, kFormatSwizzleB, kFormatSwizzleA, kFormatNone,       kFormatRGBA8_SRGB, kTexFormatRGBA32, kRTFormatARGB32, 3, 1,  "",     kFormatPropertyNormBit | kFormatPropertySRGBBit | kFormatPropertyUnsignedBit}, // RGBA8_SRGB
        // <...a lot of other entries for all formats we have...>
    };
    CompileTimeAssertArraySize(table, kGraphicsFormatCount);
    return table[format];
}
```

Just a function that returns "description" struct for a "graphics format" with various info we might be interested in,
using a pattern similar to [Translations in C++ using tables with zero-based enums](https://www.g-truc.net/post-0704.html).

The table is *huge* though. Could it be what's causing compile times to be super slow?

Let's try moving the table to be a static global variable, outside of the function:

```
static const FormatDesc table[] = //kFormatCount
{
    // <...the table...>
};
CompileTimeAssertArraySize(table, kGraphicsFormatCount);
const FormatDesc& GetDesc(GraphicsFormat format)
{
    return table[format];
}
```

Boom. Compile time of that file went down to 10.3 seconds, or whole **80 seconds faster**.

*:what:*


### That's crazy. Why does this happen?

This “initialize table inside a function” pattern differs from “just a global variable” in a few aspects:

* Compiler must emit code to do initialization when the function is executed for the first time.
  For non-trivial data, the optimizer might not “see” that it’s all just bytes in the table.
  And so it will actually emit equivalent of `if (!initYet) { InitTable(); initYet=true; }` into the function.
* Since C++11, the compiler is required to make that thread-safe too! So it does equivalent of mutex lock or
  some atomic checks for the initialization.

Whereas for a table initialized as a global variable, none of that needs to happen; if it’s not “just bytes”
then compiler still generates the initializer code, but it’s called exactly once before “actual program” starts,
and does not add any branches to `GetDesc` function.

Ok, so one takeaway is: for "static constant data" tables like that, it might be better to **declare
them as global variables, instead of static local variables** inside a function.

*But wait, there's more!* I tried to make a "simple" repro case for Microsoft C++ folks to show the unusually
long compile times, and I could not. *Something else* was also going on.


### What is "Simple Data"?

The same `/d2cgsummary` output also had this:

```
RdrReadProc Caching Stats
  Most Hits:
    ??U@YA?AW4FormatPropertyFlags@@W40@0@Z: 18328
```

Which *probably* says that `enum FormatPropertyFlags __cdecl operator|(enum FormatPropertyFlags,enum FormatPropertyFlags)`
function was called/used 18 thousand times in this file. One member of our `FormatDesc` struct was a "bitmask enum"
that had operators defined on it for type safety, similar to `DEFINE_ENUM_FLAG_OPERATORS`
(see [here](http://www.cplusplus.com/forum/general/44137/) or [there](https://blogs.eae.utah.edu/jkenkel/c-bitmasks-the-mess/)).

At this point, let's move onto actual code where things can be investigated outside of the whole Unity codebase.
Full source code is [**here in the gist**](https://gist.github.com/aras-p/b98382706eef727e2f17144be73d0661). Key parts:

```c++
#ifndef USE_ENUM_FLAGS
#define USE_ENUM_FLAGS 1
#endif
#ifndef USE_INLINE_TABLE
#define USE_INLINE_TABLE 1
#endif

enum FormatPropertyFlags { /* ...a bunch of stuff */ };
#if USE_ENUM_FLAGS
#define ENUM_FLAGS(T) inline T operator |(const T left, const T right) { return static_cast<T>(static_cast<unsigned>(left) | static_cast<unsigned>(right)); }
ENUM_FLAGS(FormatPropertyFlags);
#endif

struct FormatDesc { /* ...the struct */ };

#if USE_INLINE_TABLE
const FormatDesc& GetDesc(GraphicsFormat format)
{
#endif
    static const FormatDesc table[] = { /* ... the huge table itself */ };
#if !USE_INLINE_TABLE
const FormatDesc& GetDesc(GraphicsFormat format)
{
#endif
    return table[format];
}

UInt32 GetColorComponentCount(GraphicsFormat format)
{
    return GetDesc(format).colorComponents;
}
/* ... a bunch more functions similar to this one */
```

We have preprocessor defines to switch between the big table being defined as a static local variable inside `GetDesc`
function or a static global variable, and a define to switch whether "type safe enums" machinery is used or not.
I'm compiling for x64 with `cl.exe /O2 /Zi /GS- /GR- /EHsc- /MT main.cpp /link /OPT:REF /OPT:ICF` which is fairly typical
"Release build" flag soup.


<table class="table-cells">
<tr>
	<th width="25%">Compiler</th>
	<th width="10%">Time</th>
	<th width="10%">Time with /cgthreads1</th>
	<th width="10%">Exe size, KB</th>
	<th width="45%">Large symbols, KB</th>
</tr>
<tr><td>MSVC 2010 SP1</td>		<td class="ar      "> 1.8s</td>	<td class="ar     ">  -</td><td class="ar     "> 58</td> <td>12 GetDesc</td></tr>
<tr><td>MSVC 2015 Update 3</td>	<td class="ar bad3 ">15.1s</td>	<td class="ar bad1">42.6s</td><td class="ar bad1">189</td> <td>45 IsFloatFormat, 45 IsHalfFormat</td></tr>
<tr><td>MSVC 2017 15.3</td>		<td class="ar bad2 ">18.2s</td>	<td class="ar bad1">37.5s</td><td class="ar     "> 96</td> <td></td></tr>
</table>

Ok, so Visual C++ 2015/2017 is particularly slow at compiling this code pattern
(big table, type-safe-enum operators used in it) with optimizations turned on. And a big increase in compile time
compared to VS2010, hence I
[filed a bug for MS](https://developercommunity.visualstudio.com/content/problem/134889/vs2015-2017-slow-to-compile-large-in-function-tabl.html).

But what's even more strange, is that **code size is quite big too**, particularly in VS2015 case. *Each* of `IsFloatFormat` and
`IsHalfFormat` functions, which both are simple one-liners that just call `GetDesc`, compile into separate 45 kilobyte
chunks of code (I found that via [Sizer](/projSizer.html)).

VS2015 compiles `IsFloatFormat` into this:
```
mov         qword ptr [rsp+8],rbx
mov         qword ptr [rsp+10h],rbp
mov         qword ptr [rsp+18h],rsi
push        rdi
; some prep stuff that checks for above mentioned "is table initialized yet",
; and then if it's not; does the table initialization:
movdqa      xmm0,xmmword ptr [__xmm@00000000000000040000000400000005 (07FF7302AA430h)]
movdqa      xmmword ptr [__NULL_IMPORT_DESCRIPTOR+0E68h (07FF7302AC080h)],xmm0
movdqa      xmm0,xmmword ptr [__xmm@00000004000000050000000200000001 (07FF7302AA690h)]
movdqa      xmmword ptr [__NULL_IMPORT_DESCRIPTOR+0E98h (07FF7302AC0B0h)],xmm0
movdqa      xmm0,xmmword ptr [__xmm@00000004000000040000000000000003 (07FF7302AA680h)]
movdqa      xmmword ptr [__NULL_IMPORT_DESCRIPTOR+0ED8h (07FF7302AC0F0h)],xmm0
movdqa      xmm0,xmmword ptr [__xmm@00000008000000050000000400000004 (07FF7302AA710h)]
movdqa      xmmword ptr [__NULL_IMPORT_DESCRIPTOR+0F08h (07FF7302AC120h)],xmm0
movdqa      xmm0,xmmword ptr [__xmm@0000003e000000080000000800000005 (07FF7302AAAF0h)]
movdqa      xmmword ptr [__NULL_IMPORT_DESCRIPTOR+0F48h (07FF7302AC160h)],xmm0
movdqa      xmm0,xmmword ptr [__xmm@00000008000000050000000200000001 (07FF7302AA700h)]
movdqa      xmmword ptr [__NULL_IMPORT_DESCRIPTOR+0F78h (07FF7302AC190h)],xmm0
movdqa      xmm0,xmmword ptr [__xmm@00000004000000080000000000000003 (07FF7302AA6A0h)]
movdqa      xmmword ptr [__NULL_IMPORT_DESCRIPTOR+0FB8h (07FF7302AC1D0h)],xmm0
movdqa      xmm0,xmmword ptr [__xmm@0000000c000000050000000400000004 (07FF7302AA7A0h)]
movdqa      xmmword ptr [__NULL_IMPORT_DESCRIPTOR+0FE8h (07FF7302AC200h)],xmm0
movdqa      xmm0,xmmword ptr [__xmm@000000000000000c0000000c00000005 (07FF7302AA480h)]
movdqa      xmmword ptr [__NULL_IMPORT_DESCRIPTOR+1028h (07FF7302AC240h)],xmm0
mov         dword ptr [__NULL_IMPORT_DESCRIPTOR+0E50h (07FF7302AC068h)],1Ch
mov         qword ptr [__NULL_IMPORT_DESCRIPTOR+0E58h (07FF7302AC070h)],1010102h
mov         dword ptr [__NULL_IMPORT_DESCRIPTOR+0E60h (07FF7302AC078h)],1
mov         dword ptr [__NULL_IMPORT_DESCRIPTOR+0E64h (07FF7302AC07Ch)],4
mov         dword ptr [__NULL_IMPORT_DESCRIPTOR+0E78h (07FF7302AC090h)],1Ch
mov         word ptr [__NULL_IMPORT_DESCRIPTOR+0E7Ch (07FF7302AC094h)],2
mov         qword ptr [__NULL_IMPORT_DESCRIPTOR+0E80h (07FF7302AC098h)],rbp
mov         dword ptr [__NULL_IMPORT_DESCRIPTOR+0E88h (07FF7302AC0A0h)],1Ch
mov         qword ptr [__NULL_IMPORT_DESCRIPTOR+0E90h (07FF7302AC0A8h)],1010103h
mov         dword ptr [__NULL_IMPORT_DESCRIPTOR+0EA8h (07FF7302AC0C0h)],4
mov         dword ptr [__NULL_IMPORT_DESCRIPTOR+0EACh (07FF7302AC0C4h)],3
mov         dword ptr [__NULL_IMPORT_DESCRIPTOR+0EB0h (07FF7302AC0C8h)],1Ch
mov         word ptr [__NULL_IMPORT_DESCRIPTOR+0EB4h (07FF7302AC0CCh)],3
; repeat very similar thing for 45 more kilobytes...
```

Which is basically `GetDesc`, including the big table initializer, fully inlined into it. The initialization
is not done via some "simple data segment copy", but looks like carefully constructed entry by entry, field by field.
And then a very similar thing is *repeated* for `IsHalfFormat` function.

VS2017 does not do any of that; the optimizer "realizes" that table is purely constant data, puts it into a data
segment (yay!), and the `IsFloatFormat` function becomes fairly simple:
```
movsxd      rax,ecx
lea         r8,[__NULL_IMPORT_DESCRIPTOR+5C4h (07FF725393000h)]
imul        rdx,rax,38h
test        byte ptr [rdx+r8+30h],80h
je          IsFloatFormat+35h (07FF725391035h)
movzx       eax,byte ptr [rdx+r8+25h]
movzx       ecx,byte ptr [rdx+r8+24h]
add         ecx,eax
movzx       eax,byte ptr [rdx+r8]
xor         edx,edx
div         eax,ecx
cmp         eax,4
jne         IsFloatFormat+35h (07FF725391035h)
mov         al,1
ret
xor         al,al
ret
```

What if the table is moved to be a global variable, like my suggestion above? Passing `/DUSE_INLINE_TABLE=0` to the compiler
we get:

<table class="table-cells">
<tr>
	<th width="25%">Compiler</th>
	<th width="10%">Time</th>
	<th width="10%">Exe size, KB</th>
	<th width="45%">Large symbols, KB</th>
</tr>
<tr><td>MSVC 2010 SP1</td>		<td class="ar  "> 1.4s</td><td class="ar     "> 50</td> <td>2k dynamicinitializerfor'table'</td></tr>
<tr><td>MSVC 2015 Update 3</td>	<td class="ar  "> 2.1s</td><td class="ar     "> 96</td> <td></td></tr>
<tr><td>MSVC 2017 15.3</td>		<td class="ar  "> 2.7s</td><td class="ar     "> 96</td> <td></td></tr>
</table>

VS2017 generates *completely* identical code as before, just does it **6 times faster**. VS2015 also compiles it into
a data segment table like VS2017; does it 7 times faster, and the executable is **90 kilobytes smaller**.

VS2010 still emits the global table initializer function, and is a bit faster to compile. But it wasn't as slow to compile
to begin with.


What if we left table as a local variable, but just remove the single usage of type-safety enum flags? Passing `/DUSE_ENUM_FLAGS=0`
to the compiler we get:

<table class="table-cells">
<tr>
	<th width="25%">Compiler</th>
	<th width="10%">Time</th>
	<th width="10%">Exe size, KB</th>
</tr>
<tr><td>MSVC 2010 SP1</td>		<td class="ar  "> 0.2s</td><td class="ar     "> 46</td></tr>
<tr><td>MSVC 2015 Update 3</td>	<td class="ar  "> 0.4s</td><td class="ar     "> 96</td></tr>
<tr><td>MSVC 2017 15.3</td>		<td class="ar  "> 0.4s</td><td class="ar     "> 96</td></tr>
</table>

Whoa. All three compilers now "realize" that the table is pure simple data, put it into a data segment, and take
**a lot less time** to compile the whole thing.

And all that just because this function got removed from the source:
```c++
inline FormatPropertyFlags operator|(const FormatPropertyFlags left, const FormatPropertyFlags right)
{
	return static_cast<FormatPropertyFlags>(static_cast<unsigned>(left) | static_cast<unsigned>(right));
}
```

In this particular piece of code, that "type safe enum" machinery does not actually *do* anything useful, but in other
general code more type safety on enums & bit flags is a very useful thing to have! Quite a bit sad that it makes the compile
times go up :(

> Would using C++11 "enum classes" feature allow us having type safe enums, have bitmask operations on them (similar
> to [this approach](http://blog.bitwigglers.org/using-enum-classes-as-type-safe-bitmasks/)), and have good compile times?
> I don't know that yet. An excercise for the reader right now!


For reference, I also checked gcc (g++ 5.4 on Windows 10 Linux subsystem, with `-O2` flag) compile times and executable size
for each case above. In all cases compiled everything in 0.3 seconds on my machine, and executable size was the same.


### Summary

* Big table initializers as static local variables might be better as static global variables.
* Type-safe enum bitmask machinery is not "free" in terms of compilation time, when using optimized builds in MSVC.
* "Complex" table initializers (what is "complex" depends on table size & data in it) might get *a lot of code* emitted
  to set them up in MSVC (2015 at least); what looks "just a simple data" to you might not look so for the compiler.
* I keep on running into code structure patterns that are much slower to compile with MSVC compared to clang/gcc.
  Pretty sure the opposite cases exist too, just right now our MSVC builds are slower than clang builds, and hence
  that's where I'm looking at.
