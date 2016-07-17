---
layout: page
title: Sizer - executable size breakdown (2007)
comments: true
sharing: true
footer: true
menusection: proj
url: projSizer.html
---

<p>
Command line tool that reports size of things (functions, data, classes, templates, object files) in a Visual Studio compiled exe/dll. Extracts info from debug information (.pdb) file.
</p>
<p>
Based on code by Fabian "ryg" Giesen (<a href='http://farbrausch.com/~fg/'>farbrausch.com/~fg</a>).
</p>

<h3>Download</h3>
<p>
Version 0.1.6 <a href="files/sizer/Sizer-0.1.6.zip"><strong>here</strong></a> (136 KB). Includes executable, source and project file for Visual Studio 2010.
</p>
<p>
Also, you can just grab it from github: <a href="https://github.com/aras-p/sizer">github.com/aras-p/sizer</a>.
Patches are welcome.
</p>

<h3>Usage</h3>
<ol>
<li>Compile your executable with Visual Studio, with symbol database (.pdb).</li>
<li>Run <tt>Sizer.exe &lt;path-to-exe-file&gt;</tt> from command line. Optionally redirect stdout to a file.</li>
<li>Output is a text file that lists functions, aggregated templates, data, classes/namespaces and object files, sorted by size, largest to smallest.</li>
</ol>
<p>
It requires either having Visual Studio 2003/2005/2008/2010/2012 to be installed, or having <tt>msdia71/80/90/100/110.dll</tt> in Sizer's folder on in path. Since I'm not sure what
are distribution terms of msdia*.dll, I'm not including it in the download.
</p>
<p>Output will look like this:</p>
<pre class='listing'>
Functions by size (kilobytes):
    3.37: _input                                             input.obj
    2.04: _woutput                                           woutput.obj
    1.99: _output                                            output.obj
    1.51: CWnd::OnWndMsg                                     wincore.obj
    1.47: ATL::AtlIAccessibleInvokeHelper                    wincore.obj
    1.17: TZip::Add                                          XZip.obj
    1.05: __strgtold12                                       strgtold.obj
    1.02: GetOpenGLInfo                                      BugSystemInfo.obj
    1.00: crc_table                                          &lt;no objfile&gt;
    0.98: CWnd::FilterToolTipMessage                         tooltip.obj
    0.93: __crtLCMapStringA                                  a_map.obj
    0.93: Uploader::DoUpload                                 Uploader.obj
    <em>...more</em>

Aggregated templates by size (kilobytes):
    1.58 #    3: std::vector::_Insert_n
    0.79 #    8: std::basic_string::append
    0.53 #    6: std::basic_string::assign

Data by size (kilobytes):
   10.46: __NULL_IMPORT_DESCRIPTOR                           OPENGL32.dll
    6.00: _afxMsgCache 	wincore.obj

BSS by size (kilobytes):
    4.00: _bufin                                             * Linker *

Classes/Namespaces by code size (kilobytes):
   74.54: &lt;global&gt;
   11.43: CWnd
    3.68: Uploader
    <em>...more</em>

Object files by code size (kilobytes):
   17.37: wincore.obj
   13.05: XZip.obj
    8.82: Dlg.obj
    4.02: Uploader.obj
    3.88: BugSystemInfo.obj
    <em>...more</em>

Overall code:   142.77 kb
Overall data:    52.75 kb
Overall BSS:      4.00 kb
</pre>
</p>

<h3>Changelog</h3>

<p>
Change log is <a href="https://github.com/aras-p/sizer/blob/master/changelog.txt">on github here</a>.
</p>


<h3>Notes</h3>
<ul>
<li>The results are only as good as the information you put in. If you compile without debug information, the PDB will only contain
relatively rough info about public symbols and not much else. For example, data declared static will be reported as belonging to
the nearest public symbol - it's stored like that in the PDB, and there's no way of getting more detailed information. So if
you see something weird, like a 14k uninitialized array that you know for a fact is only 1k big, what's happened is that all
the uninitialized data following that symbol has been attributed to it. Compile your executable <em>with debug symbols on</em>
(they don't blow up the size, since everything is in a separate PDB file).</li>
<li>The sizes reported are raw, uncompressed sizes. Most application installers will compress the executable, and compression may vary wildly depending on the code itself.</li>
<li>On some executables it takes ages to read the PDB information - often it can read 10000 symbols in a second, but on some executables it takes a minute or two. I don't
quite know why; profiler says all the time is spent in DIA dll.</li>
</ul>
