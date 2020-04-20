@echo off
cls

NET SESSION >nul 2>&1
IF %ERRORLEVEL% EQU 0 (
    ECHO Administrator PRIVILEGES Detected! 
) ELSE (
    ECHO NOT AN ADMIN!
	pause
	exit
)

echo start change hosts file
echo %NEWLINE%^127.0.0.1 pohodnik.tk>>%WINDIR%\System32\drivers\etc\hosts
echo %NEWLINE%^127.0.0.1 api.pohodnik.tk>>%WINDIR%\System32\drivers\etc\hosts
color 0a
echo hosts file changed successfuly
@pause