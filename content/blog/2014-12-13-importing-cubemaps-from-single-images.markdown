---
tags: [ ]
comments: true
date: 2014-12-13T00:00:00Z
title: Importing cubemaps from single images
url: /blog/2014/12/13/importing-cubemaps-from-single-images/
---

*So [this tweet](https://twitter.com/bmcnett/status/543646517983080449) on EXR format in texture pipeline and replies on cubemaps made me write this...*

Typically skies or environment maps are authored as regular 2D textures, and then turned into cubemaps at "import time". There are various cubemap layouts commonly used: lat-long, spheremap, cross-layout etc.

In Unity 4 we had the pipeline where the user had to pick which projection the source image is using. But for Unity 5, [ReJ](https://twitter.com/__ReJ__) realized that it's just boring useless work! You can tell these projections apart quite easily by looking at image aspect ratio.

So now we default to "automatic" cubemap projection, which goes like this:

* If aspect is 4:3 or 3:4, it's a horizontal or vertical cross layout.
* If aspect is square, it's a sphere map.
* If aspect is 6:1 or 1:6, it's six cubemap faces in a row or column.
* If aspect is 1:1.85, it's a lat-long map.

Now, some images don't quite match these exact ratios, so the code is doing some heuristics. Actual code looks like this right now:

``` c
float longAxis = image.GetWidth();
float shortAxis = image.GetHeight();
bool definitelyNotLatLong = false;
if (longAxis < shortAxis)
{
    Swap (longAxis, shortAxis);
    // images that have height > width are never latlong maps
    definitelyNotLatLong = true;
}

const float aspect = shortAxis / longAxis;
const float probSphere = 1-Abs(aspect - 1.0f);
const float probLatLong = (definitelyNotLatLong) ?
    0 :
    1-Abs(aspect - 1.0f / 1.85f);
const float probCross = 1-Abs(aspect - 3.0f / 4.0f);
const float probLine = 1-Abs(aspect - 1.0f / 6.0f);
if (probSphere > probCross &&
    probSphere > probLine &&
    probSphere > probLatLong)
{
    // sphere map
    return kGenerateSpheremap;
}
if (probLatLong > probCross &&
    probLatLong > probLine)
{
    // lat-long map
    return kGenerateLatLong;
}
if (probCross > probLine)
{
    // cross layout
    return kGenerateCross;
}
// six images in a row
return kGenerateLine;
```

So that's it. There's no point in forcing your artists to paint lat-long maps, and use some external software to convert to cross layout, or something.

Now of course, you can't just look at image aspect and determine *all possible* projections out of it. For example, both spheremap and "angular map" are square. But in my experience heuristics like the above are good enough to cover most common use cases (which seem to be: lat-long, cross layout or a sphere map).
