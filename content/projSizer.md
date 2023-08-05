---
title: Sizer - executable size breakdown (2007)
comments: true
sharing: true
footer: true
menusection: proj
url: projSizer.html
---

Command line tool that reports size of things (functions, data, classes, templates, object files) in a Windows exe/dll. Extracts info from debug information (.pdb) file.

Originally based on code by [Fabian "ryg" Giesen](https://fgiesen.wordpress.com/), nowadays uses the [MolecularMatters/raw_pdb](https://github.com/MolecularMatters/raw_pdb) PDB parsing library.

### Download

Latest binaries and source code can be found on
[**github.com/aras-p/sizer**](https://github.com/aras-p/sizer). Bug reports, suggestions and patches are welcome!

Changelog is [on github here](https://github.com/aras-p/sizer/blob/main/changelog.md).


### Usage

1. Compile your Windows executable, with debug symbol database (.pdb).
1. Run `Sizer.exe <path-to-exe-or-pdb-file>` from command line. Optionally redirect stdout to a file.
1. Output is a text file that lists functions, aggregated templates, data, classes/namespaces and object files, sorted by size, largest to smallest.

Running it on an executable will try to find where the .pdb file is, and parse that. You can point it at the pdb file directly, if it is located somewhere else.

Output will look something like this (shortened here for illustration purposes):

```txt
Functions by size (kilobytes, min 10.00):
  847.90: gpu_shader_create_info_init                      gpu_shader_create_info.obj
  123.14: ccl::integrate_surface<16777659>                 kernel_sse41.obj
   32.72: MOD_solidify_nonmanifold_modifyMesh              MOD_solidify_nonmanifold.obj
   29.77: ZSTD_compressBlock_lazy2_dedicatedDictSearch_row zstd_lazy.obj
   27.02: MANTA::initializeRNAMap                          MANTA_main.obj
    ...more

Aggregated templates by size (kilobytes, min 20.00 / 3):
 1243.26 # 1458: tbb::interface9::internal::dynamic_grainsize_mode::work_balance
  611.85 #    9: ccl::integrate_surface
  572.58 # 1852: blender::index_mask::IndexMask::foreach_segment
  275.80 #  274: blender::fn::multi_function::build::detail::execute_materialized
  246.76 #  540: std::vector::_Emplace_reallocate
    ...more

Data by size (kilobytes, min 10.00):
 1379.37: datatoc_preview_blend                 preview.blend.obj
 1269.85: aud::JOSResampleReader::m_coeff       JOSResampleReaderCoeff.obj
  966.40: datatoc_preview_grease_pencil_blend   preview_grease_pencil.blend.obj
  864.69: datatoc_startup_blend                 startup.blend.obj
  822.76: datatoc_splash_png                    splash.png.obj
  512.00: btdf_split_sum_ggx                    eevee_lut.obj
    ...more

BSS by size (kilobytes, min 10.00):
  128.00: gamtab                                effects.obj
  128.00: BLI_color_to_srgb_table               * Linker *
   64.00: buffer                                path_util.obj
   64.00: jitter                                workbench_resources.obj
    ...more

Classes/Namespaces by code size (kilobytes, min 20.00):
22338.55: <global>
 3152.52: ccl
  877.12: Manta
  202.43: ccl::`anonymous namespace'
  181.28: blender::ed::space_node
  168.31: blender::geometry
  152.83: blender::bke
  137.08: tinygltf
  128.29: blender::meshintersect
  117.79: MANTA
    ...more

Object files by code size (kilobytes, min 30.00):
 2827.16: volume.obj
 1360.22: kernel_sse41.obj
 1244.13: kernel.cpp.obj
 1130.73: kernel_avx2.obj
  870.89: gpu_shader_create_info.obj
  611.08: volume_to_mesh.obj
  524.63: points_to_volume.obj
    ...more

Overall code:  52791.85 kb
Overall data:  15670.52 kb
Overall BSS:    1364.63 kb
```

### Notes

* The results are only as good as the information you put in. If you compile without debug information, the PDB will only contain
relatively rough info about public symbols and not much else. For example, data declared static will be reported as belonging to
the nearest public symbol - it's stored like that in the PDB, and there's no way of getting more detailed information. So if
you see something weird, like a 14k uninitialized array that you know for a fact is only 1k big, what's happened is that all
the uninitialized data following that symbol has been attributed to it. Compile your executable *with debug symbols on*
(they don't blow up the size, since everything is in a separate PDB file).
* The sizes reported are raw, uncompressed sizes. Most application installers will compress the executable, and compression may vary wildly depending on the code itself.
* The tool itself can run on Mac or Linux too, if you happen to have your .pdb file there. It does not use any Windows specific libraries (like `msdia_*.dll`) anymore.

