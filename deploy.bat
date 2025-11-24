@echo off
REM Deployment script for fresh installation on Windows

echo ======================================
echo Binhan Ambulance Management System
echo Fresh Installation Script (Windows)
echo ======================================
echo.

REM Check if .env exists
if not exist .env (
    echo [ERROR] .env file not found!
    echo [INFO] Please copy .env.example to .env and configure database settings
    exit /b 1
)

echo [OK] .env file found
echo.

REM Install dependencies
echo [STEP] Installing Composer dependencies...
call composer install --no-interaction --prefer-dist --optimize-autoloader
if errorlevel 1 (
    echo [ERROR] Composer install failed!
    exit /b 1
)
echo [OK] Composer dependencies installed
echo.

echo [STEP] Installing NPM dependencies...
call npm install
if errorlevel 1 (
    echo [ERROR] NPM install failed!
    exit /b 1
)
echo [OK] NPM dependencies installed
echo.

REM Generate app key if not set
findstr /C:"APP_KEY=base64:" .env >nul
if errorlevel 1 (
    echo [STEP] Generating application key...
    php artisan key:generate
    echo [OK] Application key generated
    echo.
)

REM Run migrations and seeders
echo [STEP] Running database migrations...
php artisan migrate --force
if errorlevel 1 (
    echo [ERROR] Migration failed!
    exit /b 1
)
echo [OK] Migrations completed
echo.

echo [STEP] Running database seeders...
php artisan db:seed --force
if errorlevel 1 (
    echo [ERROR] Seeding failed!
    exit /b 1
)
echo [OK] Seeders completed
echo.

REM Build assets
echo [STEP] Building frontend assets...
call npm run build
if errorlevel 1 (
    echo [ERROR] Asset build failed!
    exit /b 1
)
echo [OK] Assets built successfully
echo.

REM Clear caches
echo [STEP] Clearing caches...
php artisan optimize:clear
php artisan permission:cache-reset
echo [OK] Caches cleared
echo.

echo ======================================
echo Installation Complete!
echo ======================================
echo.
echo Test Accounts:
echo    Admin:      admin@binhan.com / password
echo    Dispatcher: dispatcher@binhan.com / password
echo    Accountant: accountant@binhan.com / password
echo    Driver:     driver@binhan.com / password
echo.
echo Start the server with:
echo    php artisan serve
echo.
pause
