---
layout: page
title: DX9 Summer 2003 SDK Sample Framework Changes
comments: true
sharing: true
footer: true
section: texts
url: /texts/dx9upd_common.html
---

<p>
<em>Hey, this text is really obsolete! Who cares about 2003 Sep SDK now? :)</em>
</p>

<H3>What?</H3>
<p>
DX9 SDK Summer Update (2003 Sep) changes to "sample framework" code. I've just thrown old code into CVS, and diff'ed
new one on that. There are very big chances that I've missed something here.
</p>

<H3>New files</H3>
<p>
<code>dxstdafx.cpp</code> and <code>dxstdafx.h</code> - these are just common includes for precompiled headers.
</p>

<H3>The changes</H3>
<p>
These are scary! I've tried to <strong>bold</strong> the "serious" changes (like stuff added/removed). Usually these are in header files.
<ul>
<li>
	<code>d3dapp.h</code><br>
	<ul>
	<li><strong>new:</strong> <code>HRESULT LaunchReadme()</code></li>
	<li><strong>new:</strong> <code>bool m_bCreateMultithreadDevice</code></li>
	<li><strong>new:</strong> <code>bool m_bAllowDialogBoxMode</code></li>
	<li><code>BuildPresentParamsFromSettings()</code> became virtual</li>
	</ul>
</li>
<li>
	<code>d3dapp.cpp</code><br>
	<ul>
	<li>Includes replaced by common include file</li>
	<li>Incorrect comments appeared at <code>Create()</code> :)</li>
	<li><code>Create()</code>: pass false to AdjustWindowRect() if no menu present</li>
	<li><code>ChooseInitialD3DSettings()</code>: m_d3dSettings.SetDeviceClip( false );</li>
	<li>More comments at MsgProc()</li>
	<li><code>MsgProc(), WM_PAINT</code>: remove m_bWindowed from if; catch device lost on Present().</li>
	<li><code>MsgProc()</code>: IDM_HELP handler added.</li>
	<li><code>HandlePossibleSizeChange()</code>: set Windowed_Width and Windowed_Height.</li>
	<li><code>HandlePossibleSizeChange()</code>: check for device loss after Reset3DEnvironment().</li>
	<li>More comments at Initialize3DEnvironment()</li>
	<li><code>Initialize3DEnvironment()</code>: Added multithreaded option.</li>
	<li><code>BuildPresentParamsFromSettings()</code>: Added device clip option. Use Windowed_Width and Windowed_Height in windowed case.
		Added code to allow dialog boxes in fullscreen case.</li>
	<li>More comments at Reset3DEnvironment()</li>
	<li><code>ToggleFullscreen()</code>: Added GetClientRect( m_hWnd, &amp;m_rcWindowClient ) at end.</li>
	<li><code>UserSelectNewDevice()</code>: Added bool bDialogBoxMode and bOldWindowed. Bunch of code for dialog boxes in
		fullscreen case instead of just ToggleFullscreen().</li>
	<li>More comments at Run()</li>
	<li><code>Run()</code>: remember HRESULT of Render3DEnvironment().</li>
	<li>More comments at Render3DEnvironment()</li>
	<li><code>Render3DEnvironment()</code>: Display error instead of returning HRESULT if Reset3DEnvironment() fails.
		Added m_fElapsedTime = fElapsedAppTime; as else case just before Render().</li>
	<li><code>DisplayErrorMsg()</code>: Change HRESULT_FROM_WIN32(ERROR_FILE_NOT_FOUND) to 0x80070002.</li>
	<li><code>LaunchReadme()</code> added.</li>
	</ul>
</li>
<li>
	<code>d3denumeration.h</code><br>
	<ul>
	<li><strong>new:</strong> <code>IDirect3D9* CD3DEnumeration::GetD3D()</code></li>
	</ul>
</li>
<li>
	<code>d3denumeration.cpp</code><br>
	<ul>
	<li>Includes replaced by common include file</li>
	</ul>
</li>
<li>
	<code>d3dfile.h</code><br>
	<ul>
	<li><strong>new:</strong> <code>HRESULT CD3DMesh::SetVertexDecl()</code></li>
	<li>Const-ified lots of strings: CD3DMesh: Create(), CD3DMesh(); CD3DFrame: FindMesh(), FindFrame(), CD3DFrame(); CD3DFile: Create(), CreateFromResource().</li>
	</ul>
</li>
<li>
	<code>d3dfile.cpp</code><br>
	<ul>
	<li>Includes replaced by common include file</li>
	<li><code>CD3DMesh::SetVertexDecl()</code> added.</li>
	<li>Changes due to const-ified strings in header file.</li>
	<li><code>CD3DMesh::RestoreDeviceObjects()</code>: pass D3DXMESH_MANAGED to CloneMeshFVF().</li>
	</ul>
</li>
<li>
	<code>d3dfont.cpp</code><br>
	<ul>
	<li>Includes replaced by common include file</li>
	</ul>
</li>
<li>
	<code>d3dres.h</code><br>
	<ul>
	<li><strong>new:</strong> <code>IDC_DEVICECLIP_CHECK</code></li>
	<li><strong>new:</strong> <code>IDM_HELP</code></li>
	</ul>
</li>
<li>
	<code>d3dsaver.h</code><br>
	<ul>
	<li>Const-ify string in ParseCommandLine().</li>
	</ul>
</li>
<li>
	<code>d3dsaver.cpp</code><br>
	<ul>
	<li><code>SwitchToRenderUnit()</code>: added "&amp;&amp; !m_bErrorMode" to if before "store rendertarget surface desc".</li>
	<li><code>Render3DEnvironment()</code>: at end of render units loop, Render() only if( !m_bErrorMode ). Present() everything only
		if( !m_bErrorMode ).</li>
	</ul>
</li>
<li>
	<code>d3dsettings.h</code><br>
	<ul>
	<li><strong>new:</strong> <code>bool CD3DSettings::bDeviceClip;</code>, plus setter method.</li>
	<li>Const-ify strings in ComboBoxAdd() and ComboBoxContainsText().</li>
	<li><strong>new:</strong> <code>void CD3DSettingsDialog::DeviceClipChanged();</code></li>
	</ul>
</li>
<li>
	<code>d3dsettings.cpp</code><br>
	<ul>
	<li>Includes replaced by common include file</li>
	<li>Const-ify strings in ComboBoxAdd() and ComboBoxContainsText().</li>
	<li><code>DialogProc()</code>: added handler for IDC_DEVICECLIP_CHECK.</li>
	<li><code>AdapterChanged()</code>: add code to if( m_d3dSettings.IsWindowed ) - starts at "// Set the windowed".</li>
	<li><code>WindowedFullscreenChanged()</code>: update device clip checkbox.</li>
	<li><code>AdapterFormatChanged()</code>: add bool bHasWindowedBackbuffer before combos loop. Add code at end of combos loop (starts at "// Count the number").
		Add code after combos loop - "if(!bHasWindowedBackbuffer)" statement.</li>
	<li><code>DeviceClipChanged();</code> added.</li>
	</ul>
</li>
<li>
	<code>d3dutil.h</code><br>
	<ul>
	<li><strong>removed:</strong> <code>D3DUtil_InitMaterial()</code></li>
	<li><strong>removed:</strong> <code>D3DUtil_InitLight()</code></li>
	<li><strong>removed:</strong> <code>D3DUtil_CreateTexture()</code></li>
	<li>Const-ify return strings in D3DUtil_D3DFormatToString().</li>
	<li>Lots of shuffles, member renames to <strong>CD3DArcBall</strong>. Too numerous to track them down :)</li>
	<li><strong>new:</strong> <code>enum D3DUtil_CameraKeys</code>, plus defines after it.</li>
	<li><strong>CD3DCamera refactored</strong> into CBaseCamera, CFirstPersonCamera and CModelViewCamera. Lots of changes here.</li>
	</ul>
</li>
<li>
	<code>d3dutil.cpp</code><br>
	Lots of changes, see header file above.
</li>
<li>
	<code>ddutil.h</code><br>
	<ul>
	<li>Const-ify strings in CreateSurfaceFromBitmap(), DrawBitmap() and DrawText().</li>
	</ul>
</li>
<li>
	<code>ddutil.cpp</code><br>
	<ul>
	<li>Does not have const-ified versions that are in header file. <strong>A bug?</strong></li>
	</ul>
</li>
<li>
	<code>didevimg.h</code> and <code>didevimg.cpp</code><br>
	<ul>
	<li>Const-ify string in DrawTooltip().</li>
	</ul>
</li>
<li>
	<code>diutil.cpp</code><br>
	<ul>
	<li><code>AddDevice()</code>: move "if(m_AddDeviceCallback)" after SetActionMap().</li>
	</ul>
</li>
<li>
	<code>dmutil.h</code> and <code>dmutil.cpp</code><br>
	<ul>
	<li>Const-ify lots of strings.</li>
	</ul>
</li>
<li>
	<code>dsutil.cpp</code><br>
	<ul>
	<li>Replace SAFE_DELETE(apDSBuffer) with SAFE_DELETE_ARRAY(apDSBuffer).</li>
	</ul>
</li>
<li>
	<code>dxutil.h</code> and <code>dxutil.cpp</code><br>
	<ul>
	<li>Const-ify lots of strings.</li>
	<li><code>DXUtil_FindMediaFileCch()</code>: lots of additional logic.</li>
	<li><code>DXUtil_ReadStringRegKeyCch(), DXUtil_ReadIntRegKey(), DXUtil_ReadBoolRegKey(), DXUtil_ReadGuidlRegKey()</code>: return S_FALSE instead of E_FAIL.
		Copy default value.</li>
	<li><strong>new:</strong> <code>DXUtil_ReadFloatRegKey(), DXUtil_WriteFloatRegKey()</code></li>
	<li><code>DXUtil_LaunchReadme()</code>: lots of additional logic.</li>
	</ul>
</li>
<li>
	<code>netclient.h</code><br>
	<ul>
	<li><strong>new:</strong> <code>#define MAX_PLAYER_NAME 14</code></li>
	<li>Const-ify strings in CNetClientWizard() and SetPlayerName().</li>
	</ul>
</li>
<li>
	<code>netclient.cpp</code><br>
	<ul>
	<li>Const-ify strings in CNetClientWizard() and SetPlayerName().</li>
	<li><strong>Limit player name size</strong> (MAX_PLAYER_NAME instead of MAX_PATH).</li>
	</ul>
</li>
<li>
	<code>netconnect.h</code> and <code>netconnect.cpp</code><br>
	<ul>
	<li><strong>new:</strong> <code>#define MAX_PLAYER_NAME 14</code></li>
	<li>Const-ify strings in CNetConnectWizard(), SetPlayerName(), SetSessionName() and SetPreferredProvider().</li>
	<li><strong>Limit player name size</strong> (MAX_PLAYER_NAME instead of MAX_PATH).</li>
	</ul>
</li>
<li>
	<code>SessionInfo.h</code> and <code>netconnect.cpp</code><br>
	<ul>
	<li>Const-ify string in AddMessage().</li>
	<li>Add some (ULONGLONG) and (DWORD) casts.</li>
	</ul>
</li>
</ul>
</p>


<? include '../common/foot.php'; ?>
