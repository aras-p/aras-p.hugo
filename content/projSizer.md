---
layout: page
title: Sizer - executable size breakdown (2007)
comments: true
sharing: true
footer: true
menusection: proj
url: projSizer.html
---

Command line tool that reports size of things (functions, data, classes, templates, object files) in a Visual Studio compiled exe/dll. Extracts info from debug information (.pdb) file.

Based on code by [Fabian "ryg" Giesen](https://fgiesen.wordpress.com/).

### Download

Latest binaries and source code can be found on
[**github.com/aras-p/sizer/releases**](https://github.com/aras-p/sizer/releases). Patches are welcome!

### Usage

1. Compile your executable with Visual Studio, with symbol database (.pdb).
1. Run `Sizer.exe <path-to-exe-file>` from command line. Optionally redirect stdout to a file.
1. Output is a text file that lists functions, aggregated templates, data, classes/namespaces and object files, sorted by size, largest to smallest.

It requires either having Visual Studio 2015..2003 to be installed, or having `msdia140/120/110/100/90.dll` in Sizer's folder on in path. Since I'm not sure what
are distribution terms of msdia*.dll, I'm not including it in the download.

Output will look like this:

````
Functions by size (kilobytes):
    3.37: _input                              input.obj
    2.04: _woutput                            woutput.obj
    1.99: _output                             output.obj
    1.51: CWnd::OnWndMsg                      wincore.obj
    1.47: ATL::AtlIAccessibleInvokeHelper     wincore.obj
    1.17: TZip::Add                           XZip.obj
    1.05: __strgtold12                        strgtold.obj
    1.02: GetOpenGLInfo                       BugSystemInfo.obj
    1.00: crc_table                           &lt;no objfile&gt;
    0.98: CWnd::FilterToolTipMessage          tooltip.obj
    0.93: __crtLCMapStringA                   a_map.obj
    0.93: Uploader::DoUpload                  Uploader.obj
    ...more

Aggregated templates by size (kilobytes):
    1.58 #    3: std::vector::_Insert_n
    0.79 #    8: std::basic_string::append
    0.53 #    6: std::basic_string::assign

Data by size (kilobytes):
   10.46: __NULL_IMPORT_DESCRIPTOR            OPENGL32.dll
    6.00: _afxMsgCache 	wincore.obj

BSS by size (kilobytes):
    4.00: _bufin                              * Linker *

Classes/Namespaces by code size (kilobytes):
   74.54: &lt;global&gt;
   11.43: CWnd
    3.68: Uploader
    ...more

Object files by code size (kilobytes):
   17.37: wincore.obj
   13.05: XZip.obj
    8.82: Dlg.obj
    4.02: Uploader.obj
    3.88: BugSystemInfo.obj
    ...more

Overall code:   142.77 kb
Overall data:    52.75 kb
Overall BSS:      4.00 kb
````

### Changelog

Change log is [on github here](https://github.com/aras-p/sizer/blob/master/changelog.txt).


### Notes

* The results are only as good as the information you put in. If you compile without debug information, the PDB will only contain
relatively rough info about public symbols and not much else. For example, data declared static will be reported as belonging to
the nearest public symbol - it's stored like that in the PDB, and there's no way of getting more detailed information. So if
you see something weird, like a 14k uninitialized array that you know for a fact is only 1k big, what's happened is that all
the uninitialized data following that symbol has been attributed to it. Compile your executable *with debug symbols on*
(they don't blow up the size, since everything is in a separate PDB file).
* The sizes reported are raw, uncompressed sizes. Most application installers will compress the executable, and compression may vary wildly depending on the code itself.
