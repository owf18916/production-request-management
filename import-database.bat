@echo off
REM Import Complete Database Schema for Production Request Management System
REM This script imports all tables and sample data

setlocal enabledelayedexpansion

echo ========================================
echo Production Request Management System
echo Database Import Script
echo ========================================
echo.

REM Set the project path
set SCRIPT_DIR=%~dp0
set DB_NAME=production_request_db
set DB_USER=root
set SCHEMA_FILE=%SCRIPT_DIR%database\migrations\000_complete_schema.sql

echo Database: %DB_NAME%
echo Schema File: %SCHEMA_FILE%
echo.

REM Check if schema file exists
if not exist "%SCHEMA_FILE%" (
    echo [ERROR] Schema file not found: %SCHEMA_FILE%
    echo Please make sure you are running this script from the project root directory.
    pause
    exit /b 1
)

REM Prompt for MySQL password
echo Enter MySQL password (press Enter if no password):
set /p DB_PASSWORD=

echo.
echo Importing database schema...
echo.

REM Import the schema
if "%DB_PASSWORD%"=="" (
    mysql -u %DB_USER% %DB_NAME% < "%SCHEMA_FILE%"
) else (
    mysql -u %DB_USER% -p%DB_PASSWORD% %DB_NAME% < "%SCHEMA_FILE%"
)

REM Check if import was successful
if %ERRORLEVEL% equ 0 (
    echo.
    echo [SUCCESS] Database imported successfully!
    echo.
    echo Tables created:
    echo - users
    echo - production_requests
    echo - request_comments
    echo - request_attachments
    echo - audit_logs
    echo - activity_logs
    echo - password_reset_tokens
    echo - master_conveyor
    echo - user_conveyor
    echo - master_atk
    echo - request_atk
    echo - request_atk_history
    echo - master_checksheet
    echo - request_checksheet
    echo - request_checksheet_history
    echo - request_id
    echo - request_id_details
    echo - request_id_history
    echo - request_memo
    echo - request_memo_history
    echo.
    echo Sample users created:
    echo - admin / admin123
    echo - pic / pic123
    echo.
    pause
) else (
    echo.
    echo [ERROR] Database import failed!
    echo Please check:
    echo 1. MySQL is running
    echo 2. Database %DB_NAME% exists
    echo 3. MySQL credentials are correct
    echo 4. Schema file exists and is readable
    echo.
    pause
    exit /b 1
)
