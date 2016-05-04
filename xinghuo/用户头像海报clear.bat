@echo off
set /p num=È·ÈÏÉ¾³ıÂğ£¿(y/n):
if %num%==y (
cd /d "data/avatar"
del /s /q /f *.*
for /d %%i in (*) do rd /s /q "%%i"
cd /d "../poster"
del /s /q /f *.*
for /d %%i in (*) do rd /s /q "%%i"
cd /d "../temp"
del /s /q /f user*.*
pause
)
if %num%==n (
exit
)
set num=