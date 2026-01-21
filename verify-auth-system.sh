#!/bin/bash
# Authentication System Verification Script
# Checks if all components are properly implemented

echo "=================================="
echo "Authentication System Verification"
echo "=================================="
echo ""

# Color codes
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Counter
TOTAL=0
PASSED=0

# Function to check file existence
check_file() {
    local file=$1
    local description=$2
    ((TOTAL++))
    
    if [ -f "$file" ]; then
        echo -e "${GREEN}✓${NC} $description: $file"
        ((PASSED++))
    else
        echo -e "${RED}✗${NC} $description: $file (NOT FOUND)"
    fi
}

# Function to check directory
check_dir() {
    local dir=$1
    local description=$2
    ((TOTAL++))
    
    if [ -d "$dir" ]; then
        echo -e "${GREEN}✓${NC} $description: $dir"
        ((PASSED++))
    else
        echo -e "${RED}✗${NC} $description: $dir (NOT FOUND)"
    fi
}

# Function to check file contains text
check_contains() {
    local file=$1
    local text=$2
    local description=$3
    ((TOTAL++))
    
    if grep -q "$text" "$file" 2>/dev/null; then
        echo -e "${GREEN}✓${NC} $description"
        ((PASSED++))
    else
        echo -e "${RED}✗${NC} $description"
    fi
}

echo "Checking Database Files..."
check_file "database/migrations/001_initial_schema.sql" "Database schema"
check_contains "database/migrations/001_initial_schema.sql" "CREATE TABLE.*users" "Users table"
check_contains "database/migrations/001_initial_schema.sql" "CREATE TABLE.*master_conveyor" "Conveyor table"
check_contains "database/migrations/001_initial_schema.sql" "CREATE TABLE.*user_conveyor" "Many-to-many table"
check_contains "database/migrations/001_initial_schema.sql" "INSERT INTO.*users" "User seed data"
echo ""

echo "Checking Models..."
check_file "app/Models/User.php" "User model"
check_contains "app/Models/User.php" "public static function authenticate" "authenticate() method"
check_contains "app/Models/User.php" "public static function getUserConveyors" "getUserConveyors() method"
check_contains "app/Models/User.php" "public static function assignConveyor" "assignConveyor() method"

check_file "app/Models/Conveyor.php" "Conveyor model"
check_contains "app/Models/Conveyor.php" "public static function getAll" "getAll() method"
check_contains "app/Models/Conveyor.php" "public static function getConveyorUsers" "getConveyorUsers() method"
echo ""

echo "Checking Controllers..."
check_file "app/Controllers/Auth.php" "Auth controller"
check_contains "app/Controllers/Auth.php" "public function showLoginForm" "showLoginForm() method"
check_contains "app/Controllers/Auth.php" "public function login" "login() method"
check_contains "app/Controllers/Auth.php" "public function logout" "logout() method"
echo ""

echo "Checking Middleware..."
check_file "app/Middleware/Authenticate.php" "Authentication middleware"
check_contains "app/Middleware/Authenticate.php" "public static function handle" "handle() method"

check_file "app/Middleware/Admin.php" "Admin middleware"
check_contains "app/Middleware/Admin.php" "user_role.*admin" "Admin role check"

check_file "app/Middleware/Pic.php" "PIC middleware"
check_contains "app/Middleware/Pic.php" "user_role.*pic" "PIC role check"
echo ""

echo "Checking Views..."
check_file "app/Views/auth/login.php" "Login view"
check_contains "app/Views/auth/login.php" "x-data.*loginForm" "Alpine.js integration"
check_contains "app/Views/auth/login.php" "identifier" "Username/NIK field"
check_contains "app/Views/auth/login.php" "showPassword" "Password toggle"
check_contains "app/Views/auth/login.php" "remember_me" "Remember me checkbox"
echo ""

echo "Checking Routes..."
check_file "routes/web.php" "Routes file"
check_contains "routes/web.php" "/login" "Login route"
check_contains "routes/web.php" "Auth@showLoginForm" "Login view route"
check_contains "routes/web.php" "Auth@login" "Login submission route"
check_contains "routes/web.php" "Auth@logout" "Logout route"
check_contains "routes/web.php" "Authenticate" "Authenticate middleware"
check_contains "routes/web.php" "Admin" "Admin middleware"
echo ""

echo "Checking Documentation..."
check_file "AUTHENTICATION.md" "Authentication documentation"
check_file "TESTING_AUTHENTICATION.md" "Testing guide"
check_file "AUTHENTICATION_IMPLEMENTATION_SUMMARY.md" "Implementation summary"
echo ""

echo "Checking Support Files..."
check_file ".env" "Environment config"
check_file "config/database.php" "Database config"
check_file "app/Session.php" "Session manager"
check_file "app/Security.php" "Security utilities"
check_file "helpers/functions.php" "Helper functions"
echo ""

echo "=================================="
echo "Verification Summary"
echo "=================================="
echo -e "Passed: ${GREEN}$PASSED / $TOTAL${NC}"

if [ $PASSED -eq $TOTAL ]; then
    echo -e "${GREEN}✓ All components are in place!${NC}"
    echo ""
    echo "Next steps:"
    echo "1. Import database: mysql -u root production_request_db < database/migrations/001_initial_schema.sql"
    echo "2. Visit: http://localhost/production-request-management/public/login"
    echo "3. Test with admin / admin123"
    exit 0
else
    echo -e "${RED}✗ Some components are missing. Please check above.${NC}"
    exit 1
fi
