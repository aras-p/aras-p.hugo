---
title: "Minimizing <windows.h>"
date: 2018-01-12T23:03:59+02:00
tags: ["code", "devtools"]
comments: true
---
I've been looking at our *(Unity game engine)* C++ codebase header file hygiene a bit, and this is a short rant-post about `<windows.h>`.

On all Microsoft platforms, our [precompiled header](https://en.wikipedia.org/wiki/Precompiled_header) was including
`<windows.h>` since about *forever*, and so effectively the whole codebase was getting all the symbols and macros from
it. Now of course, the part of the codebase that *actually* needs to use Windows API is *very small*, so I thought "hey,
let's see what happens if I stop making windows headers be included everywhere".


#### But why would you want that?

Good question! I don't know if I do, yet. Conceptually, since only very small part of the codebase needs to use
stuff from windows headers, it "feels wrong" to include it everywhere. It might still make sense to *keep* it in the
precompiled header, if that helps with build performance.

> "Precompiled headers, grandpa?! What about C++ modules?"
>
> It's true that C++ modules perhaps one day will make precompiled headers obsolete. Good to see that the major compilers
> ([MSVC](https://blogs.msdn.microsoft.com/vcblog/2017/05/05/cpp-modules-in-visual-studio-2017/),
> [clang](https://clang.llvm.org/docs/Modules.html), [gcc](https://gcc.gnu.org/wiki/cxx-modules)) have some level
> of implementation of them now.
>
> Not all our platforms are on these compilers; and some others are not on recent enough versions yet. So... I dunno, some years
> in the future? But maybe we'll just be writing Rust by then, who knows :)

Anyway. Just how large `<windows.h>` actually is? In VS2017 (version 15.5, Windows SDK 10.0.16299), after preprocessing
and ignoring empty lines, it ends up being **69100 lines** of actual code. When defining `WIN32_LEAN_AND_MEAN` before inclusion
to cut down some rarely used parts, it ends up being **33375 lines**. That's not *massive*, but not trivially small either.

For comparison, here are line counts (after preprocessing, removing empty lines) of various STL headers in the same compiler:

* `<vector>` 20354
* `<string>` 26355
* `<unordered_map>` 24291
* `<map>` 21144
* `<algorithm>` 18578
* vector+string+unordered_map+algorithm 35957
* `<cstdlib>` 1800; just `<stdlib.h>` itself: 1184. C was small and neat!


#### Actual problem with windows.h: macros

*Back to task at hand!* So, remove `<windows.h>` from precompiled header, hit build and off you go, perhaps sprinkling a now-needed windows
include here and there, right? Of course, wrong :)

Others have said it in [harsher words](http://lolengine.net/blog/2011/3/4/fuck-you-microsoft-near-far-macros), but the gist is:
Windows headers define a lot of symbols as preprocessor macros. While `min` and `max` are optional (define `NOMINMAX` before including),
some others like `near` and `far` are not.

And then of course a bunch of function names that Windows maps to either ANSI or Unicode variants with a preprocessor.
`SendMessage`, `GetObject`, `RegisterClass`, `DrawText`, `CreateFile` and so on. Good thing these are obscure names that another
codebase would never want to use... ***oh wait***. These are the exact function names that caused problems in my attempt.

If you have a central place where you include the windows headers, you could `#undef` them after inclusion, and make your own code
explicitly do `SendMessageW` instead of using a macro.

But then there's some 3rd party libraries that include windows.h from their own header files. I'm looking at you,
[Enlighten](https://www.siliconstudio.co.jp/middleware/enlighten/en/), [Oculus PC SDK](https://developer.oculus.com/pc/),
[CEF](https://bitbucket.org/chromiumembedded/cef) and others. Ugh!


#### Modular/smaller windows.h

There are other ways to approach this problem, of course.

One is to not have any Windows-specific things in many/any of your header files, wrap everything into a nice little
set of Windows-specific source files, only include windows headers there, and go on doing fun things. That would be ideal,
but is not a five minute job in a large existing codebase.

If you don't need many things from Windows headers, you could declare the minimal set that you need yourself, manually.
Contrary to what some Stack Overflow threads say ("why `WAIT_OBJECT_0` is defined to be `STATUS_WAIT_0 + 0`? because the values might not
be the same in the future!" -- err nope, they can't realistically *ever* change their values), it's not a terribly
dangerous thing to do. Windows API is one of the *most stable* APIs in the history of computing, after all.

Here's an example of a minimal set of manually defined Windows things that Direct3D 9 needs, from [@zeuxcg](https://zeuxcg.org/):
[**Minimal set of headers for D3D9**](https://gist.github.com/zeux/4c763996ce8e45eb8077).

Another example is hand-crafted, quite extensive windows headers replacement from [@ArvidGerstmann](https://www.arvid.io/):
[**Modular Windows.h Header File**](https://github.com/Leandros/WindowsHModular).

However, even Windows headers themselves can be used in a much more modular way directly! What I found so far is this:

* Preprocessor macro `_AMD64_` (for x64), `_X86_` (for x86) or `_ARM_` (for ARM) has to be defined by you, and then,
* You can **include one of the many "smaller" headers**:
	* `<windef.h>` to get most of the base types (DWORD, HWND, ...) as well as atomic functions and stuff. 7811 lines of actual code.
	* `<fileapi.h>` for file functions (CreateFile, FindFirstFile, ...), 7773 lines.
	* `<synchapi.h>` for synchronization primitives (WaitForMultipleObjects, InitializeCriticalSection, ...), 8450 lines.
	* `<debugapi.h>` for IsDebuggerPresent, OutputDebugString etc. No need to
	  [include whole thing](https://twitter.com/Ca1ne/status/862678559428628481) for it :)
* The Windows SDK headers, at least in Win10 versions, seem to be structured in a fairly stable way, and with same partition
  of these smaller header files across Win32, XboxOne and UWP platforms.



*This is all*.
