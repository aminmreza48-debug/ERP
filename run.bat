@echo off
cd /d "%~dp0"
title SIPERMA ERP Server (Port 8080)
echo ======================================================
echo    SIPERMA ERP - Server Berjalan di Port 8080
echo ======================================================
echo Buka browser ke: http://localhost:8080
echo Tekan CTRL+C untuk menghentikan server.
echo ======================================================
C:\xampp\php\php.exe -S 0.0.0.0:8080 -t "public"
pause

