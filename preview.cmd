cd /D "%~dp0"
del /Q public
hugo --logLevel info -w server
