# Production Request Management System - Database Schema Importer
# PowerShell script for Windows users to import initial database schema
# Usage: .\import-schema.ps1

# Check if MySQL is installed
$mysqlPath = Get-Command mysql -ErrorAction SilentlyContinue
if (-not $mysqlPath) {
    Write-Host "Error: mysql command not found. Make sure MySQL is installed and in PATH." -ForegroundColor Red
    exit 1
}

# Check if database schema file exists
$schemaFile = "database/migrations/001_initial_schema.sql"
if (-not (Test-Path $schemaFile)) {
    Write-Host "Error: Schema file not found at $schemaFile" -ForegroundColor Red
    exit 1
}

# Get database credentials
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Database Import Configuration" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$dbHost = "localhost"
$dbName = "production_request_db"
$dbUser = "root"

Write-Host "Database Host: $dbHost"
Write-Host "Database Name: $dbName"
Write-Host "Database User: $dbUser"
Write-Host ""

$dbPassword = Read-Host "Enter MySQL password (press Enter if none)" -AsSecureString
$dbPasswordPlain = [System.Runtime.InteropServices.Marshal]::PtrToStringAuto([System.Runtime.InteropServices.Marshal]::SecureStringToCoTaskMemUnicode($dbPassword))

# Import schema
Write-Host ""
Write-Host "Importing schema..." -ForegroundColor Yellow

try {
    if ([string]::IsNullOrWhiteSpace($dbPasswordPlain)) {
        Get-Content $schemaFile | mysql -h $dbHost -u $dbUser $dbName
    } else {
        Get-Content $schemaFile | mysql -h $dbHost -u $dbUser -p$dbPasswordPlain $dbName
    }
    
    Write-Host ""
    Write-Host "✓ Schema imported successfully!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Database tables created:" -ForegroundColor Cyan
    Write-Host "  - users"
    Write-Host "  - production_requests"
    Write-Host "  - request_comments"
    Write-Host "  - request_attachments"
    Write-Host "  - audit_logs"
    Write-Host "  - activity_logs"
    Write-Host "  - password_reset_tokens"
    Write-Host ""
    Write-Host "Sample data loaded:" -ForegroundColor Cyan
    Write-Host "  - 3 demo users (admin, manager, user)"
    Write-Host "  - 1 sample production request"
    Write-Host ""
    Write-Host "Ready to use! Access at: http://localhost/production-request-management/public/" -ForegroundColor Green
    
} catch {
    Write-Host "Error importing schema: $_" -ForegroundColor Red
    Write-Host ""
    Write-Host "Troubleshooting:" -ForegroundColor Yellow
    Write-Host "1. Check if MySQL is running"
    Write-Host "2. Verify database credentials"
    Write-Host "3. Ensure database 'production_request_db' exists"
    Write-Host "4. Check file permissions on schema file"
    exit 1
}
