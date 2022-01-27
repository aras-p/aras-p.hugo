cd /D "%~dp0"
del /Q public
hugo -v -w server
