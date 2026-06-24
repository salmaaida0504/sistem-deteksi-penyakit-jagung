@echo off
chcp 65001 >nul
echo ============================================
echo   BHUMI -- FastAPI Inference Server
echo   Model : MobileNetV3-Small
echo   Port  : 8001
echo ============================================
echo.

:: Selalu pindah ke folder python\ terlebih dahulu
cd /d "%~dp0"

:: Pastikan venv ada
if not exist "venv\Scripts\uvicorn.exe" (
    echo [ERROR] venv tidak ditemukan di folder ini!
    echo Pastikan start_server.bat ada di folder: bhumi-skripsi\python\
    pause
    exit /b 1
)

echo [OK] Menjalankan FastAPI dari: %CD%
echo.
.\venv\Scripts\uvicorn.exe predict_service:app --host 0.0.0.0 --port 8001 --reload

pause
