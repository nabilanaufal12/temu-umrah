@echo off
title TEMU UMRAH - Laravel Server
cd /d "%~dp0"
echo ======================================================
echo   Memulai Server Laravel TEMU UMRAH...
echo ======================================================
start http://127.0.0.1:8000
php artisan serve --host=127.0.0.1 --port=8000
pause
