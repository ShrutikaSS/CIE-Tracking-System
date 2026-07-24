@echo off
echo ===================================================
echo   Starting CIE Marks Tracking System Local Server
echo ===================================================
echo.
echo Launching PHP Built-in Web Server at http://localhost:8000 ...
echo Press Ctrl+C to stop the server.
echo.

where php >nul 2>nul
if %errorlevel% neq 0 (
    echo Error: PHP CLI was not found in your system PATH.
    echo Please make sure PHP (or XAMPP php.exe) is installed and added to PATH.
    echo Default XAMPP location: C:\xampp\php\php.exe
    pause
    exit /b 1
)

start http://localhost:8000/setup.php
php -S localhost:8000
