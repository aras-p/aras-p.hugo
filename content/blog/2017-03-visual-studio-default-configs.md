+++
title = "How does Visual Studio pick default config/platform?"
tags = ['code', 'devtools']
comments = true
date = "2017-03-23T10:23:56+02:00"
+++

Everyone using Visual Studio is probably familiar with these dropdowns, that contain build configurations
(Debug/Release is typical) and platforms (Win32/x64 is typical):

[{{<img width="300px" src="/img/blog/2017-03/vsconfig-standard.png">}}](/img/blog/2017-03/vsconfig-standard.png)

When opening a fresh new solution (`.sln`) file, which ones does Visual Studio pick as default? The answer is
more complex than the question is :)

After you have opened a solution and picked a config, it is stored in a hidden binary file
(VS2010: `{solutionname}.suo`, VS2015: `.vs/{solutionname}/v14/.suo`) that contains various
user-machine-specific settings, and should not be put into version control. However what I am interested is
what is the default configuration/platform when you open a solution for the first time.


#### Default Platform

Platform names in VS *solution* can be arbitrary identifiers, however platform names defined in the *project*
files have to match an installed compiler toolchain (e.g. `Win32` and `x64` are toolchain names for 32 and
64 bit Windows on Intel CPUs, respectively).

Turns out, default platform is ***first one from an alphabetically, case insensitive, sorted list*** of all
*solution* platforms.

This means if you have `Win32` and `x64` as the solution platforms, then 32 bit one will be the default.
That probably explains why in recent VS versions (at least since 2015), the built-in project creation
wizard started naming them as `x86` and `x64` instead -- this conveniently makes `x64` be default since it
sorts first.

A note again, platform names in the *project* have to be from a predefined set; so they have to stay `Win32`
and `x64` and so on. Even if you use VS for editing files in a makefile-type project that invokes compiler
toolchains that VS does not even know about (e.g. WebGL -- for that project, you still have to pick whether
you want to name the platform as Win32 or x64).


#### Default Configuration

When you have `Debug` and `Release` configurations, VS picks Debug as the default one. What if you have more
configurations, that are more complex names (e.g. we might want to have a `Debug WithProfiler LumpedBuild`)?
Which one will be the default one?

So, a pop quiz time! If all projects in the solution end up having this set of configurations, which one will
VS use by default?

```
Foo
Foo_Bar
Foo Bar
Alot
AlotOfBanjos
Alot Of Bees
Debug
Debug_Lumped
Debug_Baloney
DebugBaloney
```

You might have several guesses, and they all would make some sense:

* `Foo` since it's the first one in the solution file,
* `Alot` since it's the first one alphabetically (and hey that's how VS chooses default platform),
* `Debug` since VS probably has some built-in logic to pick "debug" first.

*Of course* all these guesses are wrong! Out of the list above, VS will pick `Debug_Baloney` as the default
one. *But why?!*

***The logic seems to be something like this*** (found in this
[stackoverflow answer](http://stackoverflow.com/a/41445987), except it needed an addition for the underscore case).
Out of all configurations present:

1. Sort them (almost) alphabetically, case insensitive,
2. But put configs that start with `debug` before all others,
3. Also config that is another one with a ` Whatever` or `_Whatever` added, go before it. So `A_B` goes before
    `A`; `Debug All` goes before `Debug` (but `DebugAll` goes after `Debug`).
4. And now pick the first one from the list!

I do hope there is a good explanation for this, and today VS team probably can't change it because doing so would
upset three bazillion existing projects that learned to accidentally or on purpose to depend on this.


However this means that today in our code I have to write things like this:

```csharp
// We want to put spaces into default config of the 1st native program;
// see ConfigurationToVSName. Default configs in each programs might be
// separate sets, so e.g. in the editor there might be "debug_lump" and
// the standalone might only have a longer one "debug_lump_il2cpp", and
// if both were space-ified VS would pick "debug lump il2cpp" as default.
var defaultSolutionConfig = nativePrograms[0].ValidConfigurations.Default;
GenerateSolutionFile(nativePrograms, solutionGuid, projectGuids, defaultSolutionConfig);
GenerateHelperScripts();
foreach (var np in nativePrograms)
{
    GenerateProjectFile(np, projectGuids, defaultSolutionConfig);
    GenerateFiltersFile(np);
}
```

and this:

```csharp
// Visual Studio has no way to indicate which configuration should be default
// in a freshly opened solution, but uses logic along the lines of
// http://stackoverflow.com/a/41445987 to pick default one:
//
// Out of all configurations present:
// 1. Sort them (almost) alphabetically, case insensitive,
// 2. But put configs that start with "debug" before all others,
// 3. Also config that is another one with a " Whatever" or "_Whatever" added,
//    go before it. So "A_B" goes before "A"; "Debug All" goes before "Debug"
//    (but "DebugAll" goes after "Debug").
// 4. And now pick the first one from the list!
//
// Our build configs are generally underscore-separated things, e.g. "debug_lump_il2cpp".
// To make the default config be the first one, replace underscores in it with
// spaces; that will make it sort before other things (since space is before
// underscore in ascii), as long as it starts with "debug" name.
string ConfigurationToVSName(CApplicationConfig config, CApplicationConfig defaultConfig)
{
    if (config.IdentifierNoPlatform != defaultConfig.IdentifierNoPlatform)
        return config.IdentifierNoPlatform;
    return config.IdentifierNoPlatform.Replace('_', ' ');
}

```

[{{<imgright width="150" src="/img/blog/2017-03/vsconfig-default.png">}}](/img/blog/2017-03/vsconfig-default.png)

This is all trivial code of course, but figuring out the logic for what VS ends up doing
did took some experimentation. Oh well, now I know! And you know too, even if you never wanted
to know :)

And voilà, `debug_lump` is picked by VS as the default in our auto-generated project files, which is
what I wanted here. Without any extra logic, it was picking `debug_lump_dev_il2cpp` since that
sorts before `debug_lump` as per rules above.

That's it for now!
