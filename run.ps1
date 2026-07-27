# PowerShell Launcher for CIE Tracking System
Write-Host "===================================================" -ForegroundColor Cyan
Write-Host "  CIE Activity Marks Tracking System - Server Start" -ForegroundColor Cyan
Write-Host "===================================================" -ForegroundColor Cyan

$phpPath = Get-Command php -ErrorAction SilentlyContinue

if (-not $phpPath) {
    if (Test-Path "C:\xampp\php\php.exe") {
        $env:Path += ";C:\xampp\php"
        Write-Host "[INFO] Added C:\xampp\php to current session PATH" -ForegroundColor Yellow
    } else {
        Write-Host "[ERROR] PHP is not installed or not in PATH." -ForegroundColor Red
        Write-Host "Please install PHP / XAMPP or add php.exe to environment PATH." -ForegroundColor Red
        Pause
        Exit
    }
}

Write-Host "[SUCCESS] PHP runtime detected: $(php -v | Select-Object -First 1)" -ForegroundColor Green
Write-Host "[INFO] Opening browser at http://localhost:8000/setup.php ..." -ForegroundColor Cyan
Start-Process "http://localhost:8000/setup.php"

Write-Host "[INFO] Server listening on http://localhost:8000 (Press Ctrl+C to terminate)" -ForegroundColor Green
php -S localhost:8000
