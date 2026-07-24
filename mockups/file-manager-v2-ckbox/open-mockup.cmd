@echo off
setlocal
cd /d "%~dp0"

if not exist "dist\index.html" (
  echo Build belum tersedia. Menjalankan npm install dan npm run build...
  call npm.cmd install --no-audit --no-fund
  if errorlevel 1 exit /b 1
  call npm.cmd run build
  if errorlevel 1 exit /b 1
)

start "" "http://127.0.0.1:4177"
php -S 127.0.0.1:4177 -t dist
