@echo off
REM Production Request Management System - Database Schema Importer
REM Batch script for Windows Command Prompt users to import initial database schema
REM Usage: import-schema.bat

echo.
echo ========================================
echo Database Import Configuration
echo ========================================
echo.

REM Check if mysql command exists
where mysql >nul 2>nul
if %errorlevel% neq 0 (
    echo Error: mysql command not found. Make sure MySQL is installed and in PATH.
    pause
    exit /b 1
)

REM Check if schema file exists
if not exist "database\migrations\001_initial_schema.sql" (
    echo Error: Schema file not found at database\migrations\001_initial_schema.sql
    pause
    exit /b 1
)

echo Database Host: localhost
echo Database Name: production_request_db
echo Database User: root
echo.

set /p dbPassword="Enter MySQL password (press Enter if none): "

echo.
echo Importing schema...
echo.

if "%dbPassword%"=="" (
    mysql -u root production_request_db < database\migrations\001_initial_schema.sql
) else (
    mysql -u root -p%dbPassword% production_request_db < database\migrations\001_initial_schema.sql
)

if %errorlevel% equ 0 (
    echo.
    echo ^[OK^] Schema imported successfully!
    echo.
    echo Database tables created:
    echo   - users
    echo   - production_requests
    echo   - request_comments
    echo   - request_attachments
    echo   - audit_logs
    echo   - activity_logs
    echo   - password_reset_tokens
    echo.
    echo Sample data loaded:
    echo   - 3 demo users (admin, manager, user^)
    echo   - 1 sample production request
    echo.
    echo Ready to use! Access at: http://localhost/production-request-management/public/
    echo.
) else (
    echo.
    echo [ERROR] Failed to import schema
    echo.
    echo Troubleshooting:
    echo 1. Check if MySQL is running
    echo 2. Verify database credentials
    echo 3. Ensure database 'production_request_db' exists
    echo 4. Check file permissions on schema file
    echo.
    pause
    exit /b 1
)

pause
