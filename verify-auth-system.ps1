# Authentication System Verification Script - PowerShell
# Checks if all required files exist

Clear-Host
Write-Host "Authentication System Verification" -ForegroundColor Cyan
Write-Host "===================================" -ForegroundColor Cyan
Write-Host ""

$checks = @(
    "database/migrations/001_initial_schema.sql",
    "app/Models/User.php",
    "app/Models/Conveyor.php",
    "app/Controllers/Auth.php",
    "app/Middleware/Authenticate.php",
    "app/Middleware/Admin.php",
    "app/Middleware/Pic.php",
    "app/Views/auth/login.php",
    "routes/web.php",
    "AUTHENTICATION.md",
    "TESTING_AUTHENTICATION.md",
    "AUTHENTICATION_IMPLEMENTATION_SUMMARY.md",
    ".env",
    "config/database.php"
)

$passed = 0
$total = $checks.Count

foreach ($file in $checks) {
    if (Test-Path $file -PathType Leaf) {
        Write-Host "OK  $file" -ForegroundColor Green
        $passed++
    } else {
        Write-Host "MISSING  $file" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "===================================" -ForegroundColor Cyan
Write-Host "Results: $passed / $total files found" -ForegroundColor Cyan
Write-Host "===================================" -ForegroundColor Cyan


