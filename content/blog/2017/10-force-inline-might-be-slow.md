---
title: "Forced Inlining Might Be Slow"
date: 2017-10-09T12:16:30+03:00
tags: ["code", "compilers", "devtools"]
comments: true
---

We were upgrading from Visual Studio 2010 to 2015 compiler at work the other day, and I noticed that Release
configuration build times went up. Quite noticeably up: a full rebuild of "the engine" went from
~6 minutes to ~12 minutes. Something needed to be done!

Using the same [build profiler](/blog/2017/08/08/Unreasonable-Effectiveness-of-Profilers/) I had added before,
I quickly found one C++ source file that was taking 10 minutes to compile with VS2015, and a few others
that were a bit less extreme.

Just like before, the slow files tended to be heavy users of our SIMD math library, and I started to think
along the lines of *"oh no, will probably be a lot of work to investigate how to reduce all the complexity of it"*,
and so on. After all, it's a fractal of templates that I don't understand.

However, feedback from the [bug report](https://connect.microsoft.com/VisualStudio/feedback/details/3141368/cl-exe-in-vs2015-much-slower-to-compile-some-c-code-than-vs2010-template-heavy-5x-slower-with-o1-or-o2)
to Microsoft ("2015 got a lot slower than 2010 at compiling the same code"; worth notifying them about it in any case)
suggested that it's our usage of `__forceinline` that might be the culprit.


### Make two functions non-inlined, speed up the build twice

From the answers to my bug report:

> The basic root cause here is there are a few functions (one is `inverse_WorksFor_SingularAffineX`,
> but there are similar ones that appear to be stamped out by the same set of macros) which have deep,
> deep inline trees. Around 8000 individual inline instances; the majority of which come from
> `__forceinline` functions.

Looking at our code, I could see that pretty much everything in our math library used forced inlining.
The reason for that, AFAIK, was to work around "SIMD parameters are passed through stack" x64 ABI problem
that existed before Microsoft [added](https://en.wikipedia.org/wiki/X86_calling_conventions#Microsoft_vectorcall)
`__vectorcall` in VS 2013.

Most of the inlines make sense, however maybe we indeed had some functions that were long or *deep*? Turns out,
yes we did. We had a "robust matrix inverse" function implemented via SVD (singular value decomposition),
which was force-inlined, along with everything else it called. SVD inverse is: some matrix transposes,
5 Jacobi iterations that involve a bunch of quaternion-matrix conversions and transposes,
sorting of singular values, some factorization, some quat-matrix conversions again, etc. All that compiles down
to 6.5 kilobytes of machine code. That’s *way out* of “sensible thing to force inline”, IMHO!

So I moved `svdInverse` and a similar `svdOrthogonalize` functions from header into .cpp file.

**Full build got twice as fast**; from 11m54s down to 5m46s.

The slowest file with a lot of unit tests, that took 660 seconds to compile before, now compiled in 6.5 seconds.
That's a... **100x compile time speedup**. 

> Here's what could be useful to have in a compiler output: some sort of diagnostic switch that would tell
> me "hey, you marked this as force inlined, but the function compiles down to several kilobytes, and is used
> in more than a handful places".
>
> Since today investigating a thing like that is basically "read the source code, try to guess which functions
> end up being large". That, or do a special build without inlining, and use
> [Sizer](https://github.com/aras-p/sizer) / [SymbolSort](https://github.com/adrianstone55/SymbolSort) /
> [Bloaty McBloatface](https://github.com/google/bloaty) to figure out large functions.
> All that is fairly involved in a large codebase.

Then I made two other functions not be inlined too, and it got some additional build speedup. Full build was back to
about 5 minutes, by just slightly moving 4 functions in a multi-million line codebase. Not bad!

*This time*, the primary reason for build being slow was *not* template metaprogramming! :)


### Synthetic test for inlining compile times

To check whether it's not just something in "our code", I did a small synthetic test. It has nonsensical
functions that don’t do anything useful, but kinda “approximates” what we had in our codebase.

```c++
#if defined(_MSC_VER)
#define INL __forceinline
#else
#define INL inline __attribute__((always_inline))
#endif

// --- Basic stuff of what you might see in a "SSE2 SIMD math library" code
#include <emmintrin.h>

struct float4
{
    __m128 val;
    float4() { val = _mm_setzero_ps(); }
    float4(float x) { val = _mm_set1_ps(x); }
    float4(float x, float y) { val = _mm_set_ps(y, x, y, x); }
    float4(float x, float y, float z) { val = _mm_set_ps(0.f, z, y, x); }
    float4(float x, float y, float z, float w) { val = _mm_set_ps(w, z, y, x); }
    float4(__m128 v) { val = v; }
};

static INL float4 operator+(const float4& a, const float4& b) { return float4(_mm_add_ps(a.val, b.val)); }
static INL float4 operator-(const float4& a, const float4& b) { return float4(_mm_sub_ps(a.val, b.val)); }
static INL float4 operator*(const float4& a, const float4& b) { return float4(_mm_mul_ps(a.val, b.val)); }
static INL float4 operator/(const float4& a, const float4& b) { return float4(_mm_div_ps(a.val, b.val)); }
static INL float4 csum(const float4 &p)
{
    __m128 r = _mm_add_ps(p.val, _mm_castsi128_ps(_mm_shuffle_epi32(_mm_castps_si128(p.val), _MM_SHUFFLE(0, 3, 2, 1))));
    return _mm_add_ps(r, _mm_castsi128_ps(_mm_shuffle_epi32(_mm_castps_si128(r), _MM_SHUFFLE(1, 0, 3, 2))));
}
static INL float4 dot(const float4 &p0, const float4 &p1) { return csum(p0*p1); }
static INL float4 dot(const float4 &p) { return dot(p, p); }
static INL float4 rsqrt(const float4 &x)
{
    #define C0  9.999998e-01f
    #define C1  3.0000002e+00f
    #define C2  .5f
    #define C3  340282346638528859811704183484516925440.f
    __m128 e = _mm_mul_ps(_mm_rsqrt_ps((__m128) x.val), _mm_set_ps(C0, C0, C0, C0));
    e = _mm_min_ps(e, _mm_set_ps(C3, C3, C3, C3));
    return _mm_mul_ps(_mm_mul_ps(e, _mm_set_ps(C2, C2, C2, C2)), _mm_sub_ps(_mm_set_ps(C1, C1, C1, C1), _mm_mul_ps(_mm_mul_ps(x.val, e), e)));
}
static INL float4 normalize(const float4 &v)
{
    return v*rsqrt(dot(v));
}

// --- Functions that don't really make sense, I just kinda randomly
// approximated what we had, without pulling all of it into a repro.
// They don't do anything useful whatsoever; just for testing compile
// time performance.

static INL float4 ident() { return float4(0.f, 0.f, 0.f, 1.f); }
static INL float4 whatever1(const float4 &x, const float4 &y)
{
    return csum(x) / x + y;
}
static INL float4 whatever2(const float4& q1, const float4& q2)
{
    return whatever1(q1 * q2, q2 - q1) * (q1 + q2);
}
static INL float4 whatever3(const float4 &pq, const float4 &mask)
{
    const float c8 = 0.923879532511287f;
    const float s8 = 0.38268343236509f;
    const float g = 5.82842712474619f;

    float4 ch = float4(2) * (normalize(pq) - normalize(mask));
    float4 sh = pq * normalize(ch);
    float4 r = ((g*sh*sh - ch*ch) + sh / float4(s8, s8, s8, c8)) * mask;
    return normalize(r);
}
struct matrix
{
    float4 m0, m1, m2;
};
static INL float4 whateverIteration(matrix &s, int count = 5)
{
    matrix qm;
    float4 q, v = ident();
    for (int iter = 0; iter < count; iter++)
    {
        q = whatever3(s.m0, float4(0, 0, 1, 1));
        v = whatever2(v, q);
        v = normalize(v);
        q = whatever3(s.m1, float4(1, 0, 0, 1));
        v = whatever2(v, q);
        v = normalize(v);
        q = whatever3(s.m2, float4(0, 1, 0, 1));
        v = whatever2(v, q);
        v = normalize(v);
    }
    return v;
}

// Now, kinda "obviously" this function is a bit too large to be force-inlined.
// This is what I'm testing with; having & not having an "INL" on this function.
static INL float4 whateverDecomposition(const matrix &a, const float4 &u, const float4 &v)
{
    float4 r;
    matrix s = a;
    s.m0 = normalize(s.m0) + u;
    s.m1 = normalize(s.m1) * v;
    s.m2 = normalize(s.m2);
    r = whateverIteration(s);
    r = normalize(v) * u + (normalize(u) / v);
    s.m0 = s.m0 + r;
    s.m1 = s.m1 + r;
    s.m2 = s.m2 + r;
    r = whateverIteration(s);
    s.m0 = s.m0 / normalize(r);
    s.m1 = s.m1 / normalize(v+r);
    s.m2 = s.m2 / normalize(v*r);
    r = whateverIteration(s);
    s.m0 = s.m0 * s.m1;
    s.m1 = s.m1 * s.m2 - r;
    s.m2 = s.m2 * s.m0 + r;
    r = whateverIteration(s);
    return r;
}

int main(int argc, const char** argv)
{
    matrix a;
    a.m0 = (float)argv[0][0];
    a.m1 = (float)argc;
    a.m2 = (float)(argv[0][0] - argc);

    float4 u = a.m0;
    float4 v = a.m1;
    float4 e = whateverDecomposition(a, a.m0, a.m1);
    e = e + whateverDecomposition(a, a.m2, normalize(a.m1));
    e = e + whateverDecomposition(a, normalize(a.m2), e);
    e = e + whateverDecomposition(a, e, e);
    e = e + whateverDecomposition(a, normalize(e), normalize(e));
    e = e * whateverDecomposition(a, e, e);
    e = e - whateverDecomposition(a, e, e);
    e = e * whateverDecomposition(a, e, e);
    e = e + whateverDecomposition(a, e, e);
    float4 r = normalize(e);

    return (int)_mm_cvtss_f32(r.val);
}
```

I checked compile times under different compilers. Visual Studio was compiled with `/O2`, and clang/gcc with `-O2`, to get an “optimized build”.
Everything on the same machine (Core i7-5820K); clang/gcc via Windows 10 Linux subsystem thing.
In all cases compiled for x64 architecture. In the two timing cases, the only difference in source
was removing forced inlining from a single `whateverDecomposition` function.


<table class="table-cells">
<tr><th width="25%">Compiler</th><th width="25%">Time</th><th width="25%">Time without force inline</th><th width="25%">Speedup</th></tr>
<tr><td>MSVC 2010 SP1</td>		<td class="ar bad3">8.1s</td>	<td class="ar">0.4s</td><td class="ar">20x</td></tr>
<tr><td>MSVC 2015 Update 3</td>	<td class="ar bad1">19.8s</td>	<td class="ar">0.6s</td><td class="ar"><b>33x</b></td></tr>
<tr><td>MSVC 2017 15.3</td>		<td class="ar bad2">9.8s</td>	<td class="ar">0.7s</td><td class="ar">14x</td></tr>
<tr><td>g++ 5.4</td>			<td class="ar">1.5s</td>		<td class="ar">0.5s</td><td class="ar">3x</td></tr>
<tr><td>clang 3.8</td>			<td class="ar">0.5s</td>		<td class="ar">0.3s</td><td class="ar">1.7x</td></tr>
</table>

The conclusion seems to be that Visual Studio compiler is *way more* slow at deep inlining, compared
to gcc/clang. And VS2015 in particular had a compile regression there somewhere, with it being mostly gone in
VS2017.

*(I would have added all this to my
[bug report](https://connect.microsoft.com/VisualStudio/feedback/details/3141368/cl-exe-in-vs2015-much-slower-to-compile-some-c-code-than-vs2010-template-heavy-5x-slower-with-o1-or-o2)
in Microsoft Connect... except I don't seem to be able to add more comments on it. Sorry MS!)*
