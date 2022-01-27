cd /D "%~dp0"
rmdir /Q /S public
hugo.exe
copy public\index.xml public\atom.xml
wsl.exe rsync -az --force --progress -e ssh public/ nearaz@aras-p.info:~/aras-p.info/
rmdir /Q /S public
pause
