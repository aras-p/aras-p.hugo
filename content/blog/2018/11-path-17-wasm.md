---
title: "Pathtracer 17: WebAssembly"
date: 2018-11-16T19:07:10+03:00
tags: ['rendering', 'code', 'web']
comments: true
---

*Introduction and index of this series [is here](/blog/2018/03/28/Daily-Pathtracer-Part-0-Intro/)*.

> Someone at work posted a "Web Development With Assembly" meme as a joke, and I pulled off a "well, actually"
> card pointing to [WebAssembly](https://webassembly.org/). At that point I just had to make my toy path
> tracer work there.

So here it is: [**aras-p.info/files/toypathtracer**](/files/toypathtracer/)

[{{<img src="/img/blog/2018/rt-wasm.jpg">}}](/img/blog/2018/rt-wasm.jpg)

#### Porting to WebAssembly

The "porting" process was super easy, I was quite impressed how painless it was. Basically it was:

1. Download & install the official [Emscripten SDK](http://kripken.github.io/emscripten-site/docs/getting_started/downloads.html),
   and follow the instructions there.
1. Compile my source files, very similar to invoking `gcc` or `clang` on the command line, just Emscripten compiler is `emcc`.
   This was the full command line I used: `emcc -O3 -std=c++11 -s WASM=1 -s ALLOW_MEMORY_GROWTH=1 -s EXTRA_EXPORTED_RUNTIME_METHODS='["cwrap"]' -o toypathtracer.js main.cpp ../Source/Maths.cpp ../Source/Test.cpp`
1. Modify the existing code to make both threads & SIMD (two things that Emscripten/WebAssembly lacks at the moment) optional.
   Was just a couple dozen lines of code starting [here in this commit](https://github.com/aras-p/ToyPathTracer/commit/344e9cefa70#diff-7c18e8f11c4765dcb696caf52e81c77f).
1. Write the "main" C++ entry point file that is specific for WebAssembly, and the HTML page to host it.

How to structure the main thing in C++ vs HTML? I basically followed the "[Emscripting a C library to Wasm](https://developers.google.com/web/updates/2018/03/emscripting-a-c-library)" doc by Google, and "[Update a canvas from wasm](https://www.hellorust.com/demos/canvas/)" Rust
example (my case is not Rust, but things were fairly similar). My C++ entry file is here ([main.cpp](https://github.com/aras-p/ToyPathTracer/blob/17-wasm/Cpp/Emscripten/main.cpp)), and the HTML page is here ([toypathtracer.html](https://github.com/aras-p/ToyPathTracer/blob/17-wasm/Cpp/Emscripten/toypathtracer.html)). All pretty simple.

*And that's basically it!*

#### Ok how fast does it run?

At the moment WebAssembly does not have SIMD, and does not have "typical" (shared memory) multi-threading support.

> The Web almost got multi-threading at start of 2018, but then [Spectre and Meltdown](https://meltdownattack.com/) happened,
> and threading got promptly turned off. As soon as you have ability to run fast atomic instructions on a thread, you can
> build a really high precision timer, and as soon as you have a high precision timer, you can start measuring things that
> reveal what sort of thing got into the CPU caches. Having "just" that is enough to start building basic forms of these attacks.
>
> By now the whole industry (CPU, OS, browser makers) scrambled to fix these vulnerabilities, and threading might be coming back
> to Web soon. However at this time it's not enabled by default in any browsers yet.

All this means that the performance numbers of WebAssembly will be substantially lower than other CPU implementations --
after all, it will be running on just one CPU core, and without any of the [SIMD speedups](/blog/2018/04/11/Daily-Pathtracer-8-SSE-HitSpheres/)
we have done earlier.

Anyway, the results I have are below *(higher numbers are better)*. You can try yourself at [aras-p.info/files/toypathtracer](/files/toypathtracer/)

<table cellpadding="5">
<tr><th>Device</th><th>OS</th><th>Browser</th><th>Mray/s</th></tr>
<tr><td rowspan="3">Intel Core i9 8950HK 2.9GHz (MBP 2018)</td><td rowspan="3">macOS 10.13</td><td>Safari 11</td><td>5.8</td></tr>
<tr><td>Chrome 70</td><td>5.3</td></tr>
<tr><td>Firefox 63</td><td>5.1</td></tr>
<tr><td>Intel Xeon W-2145 3.7GHz</td><td>Windows 10</td><td>Chrome 70</td><td>5.3</td></tr>
<tr><td rowspan="3">AMD ThreadRipper 1950X 3.4GHz</td><td rowspan="3">Windows 10</td><td>Firefox 64</td><td>4.7</td></tr>
<tr><td>Chrome 70</td><td>4.6</td></tr>
<tr><td>Edge 17</td><td>4.5</td></tr>
<tr><td>iPhone XS / XR (A12)</td><td>iOS 12</td><td>Safari</td><td>4.4</td></tr>
<tr><td>iPhone 8+ (A11)</td><td>iOS 12</td><td>Safari</td><td>4.0</td></tr>
<tr><td>iPhone SE (A9)</td><td>iOS 12</td><td>Safari</td><td>2.5</td></tr>
<tr><td>Galaxy Note 9 (Snapdragon 845)</td><td>Android 8.1</td><td>Chrome</td><td>2.0</td></tr>
<tr><td>iPhone 6 (A8)</td><td>iOS 12</td><td>Safari</td><td>1.7</td></tr>
</table>

For reference, if I turn off threading & SIMD in the regular C++ version, I get 7.0Mray/s on the Core i9 8950HK MacBookPro. So WebAssembly
at 5.1-5.8 Mray/s is slightly slower, but not "a lot". Is nice!

All code is on [github at `17-wasm` tag](https://github.com/aras-p/ToyPathTracer/tree/17-wasm).
