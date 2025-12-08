@echo off
setlocal enabledelayedexpansion

REM ----- CONFIGURATION -----
set MYSQL_PATH="C:\xampp\mysql\bin\mysqldump.exe"
set OUT_DIR="C:\xampp\htdocs\oc\p4l1\sql\dumps"
set DB_NAME=tomtroc
set DB_USER=root
set DB_PASS=

REM ----- DATE AU FORMAT yyyymmdd_hhmm -----
for /f "tokens=1-5 delims=/ " %%d in ("%date% %time%") do (
    set YYYY=%%f
    set MM=%%e
    set DD=%%d
)
set HH=%time:~0,2%
set MN=%time:~3,2%

REM Corriger espace éventuel sur HH (<10)
if "%HH:~0,1%"==" " set HH=0%HH:~1,1%

set TS=%YYYY%%MM%%DD%_%HH%%MN%

REM ----- NOM DU FICHIER -----
set FILE=%OUT_DIR%\tomtroc.%TS%.sql

REM ----- EXECUTION DU DUMP -----
echo Dump de la base %DB_NAME%...
%MYSQL_PATH% -u %DB_USER% %DB_PASS% %DB_NAME% > "%FILE%"

echo Dump terminé :
echo %FILE%

endlocal
pause
