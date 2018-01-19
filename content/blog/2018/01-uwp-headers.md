---
title: "UWP/WinRT Headers are Fun (not)"
date: 2018-01-19T10:49:18+02:00
tags: ["code", "rant"]
comments: true
---
As established before, `<windows.h>` is [a bit of a mess](/blog/2018/01/12/Minimizing-windows.h/)
that has accumulated over 30+ years. Symbols in global namespace, preprocessor macros, *ugh*:

```
#include <windows.h>
// my code
void* GetObject(...);
// welp, GetObject is actually GetObjectW now
```

So naturally, someone at Microsoft decided it's time to make a "v2" API set for programming Windows,
without any of the horrors of the past, using more modern approaches, and so on. And so in 2012
[Windows Runtime](https://en.wikipedia.org/wiki/Windows_Runtime) was born.

No more preprocessor hijacking identifiers! No more global namespaces! *...hmm. or is it?* Try this
(tested with Windows SDK 10.0.16299.0, VS2017):

```
class Plane;
#include <windows.ui.core.h>
```

What does the compiler say?
```txt
Windows.Foundation.Numerics.h(490): error C2371: 'ABI::Windows::Foundation::Numerics::Plane': redefinition; different basic types
Windows.Foundation.Numerics.h(317): note: see declaration of 'ABI::Windows::Foundation::Numerics::Plane'
```
Compiling with `/W3` tells more detail on *why* that happens:

```txt
Windows.Foundation.Numerics.h(317): warning C4099: 'Plane': type name first seen using 'class' now seen using 'struct'
test.cpp(2): note: see declaration of 'Plane'
```

Lo and behold, turns out that `Windows.Foundation.Numerics.h` (which is included by *a lot* of WinRT headers) has this:
```
namespace ABI {
    namespace Windows {
        namespace Foundation {
            namespace Numerics {                
                typedef struct Plane Plane;
            } 
        } 
    } 
} 
```

The code *tried* to be namespace-aware, but `typedef struct Plane Plane` apparently is not. Why does it have
that thing in the first place? No idea!

But this means you can't forward-declare classes/structs, that match WinRT structs/classes
(even inside namespaces!), before including WinRT headers. Your own forward-declarations have to come
*after* WinRT header inclusion.

Since that pattern in headers essentially creates code like this, which is a compile error on all C++ compilers:

```
class Plane;
namespace Test
{
    typedef struct Plane Plane;
    struct Plane { int a; };
}
// clang 5.0.0:
// warning: struct 'Plane' was previously declared as a class [-Wmismatched-tags]
// error: definition of type 'Plane' conflicts with typedef of the same name
//
// vs2017:
// warning C4099: 'Plane': type name first seen using 'class' now seen using 'struct'
// error C2371: 'Test::Plane': redefinition; different basic types
//
// gcc 7.2:
// error: using typedef-name 'Test::Plane' after 'struct'
```

"Great" job, WinRT headers. At least in WinAPI times I could undo most of the damage with some `#undef`...

*:sadpanda:*
