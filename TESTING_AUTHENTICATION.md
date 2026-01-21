# Authentication System Testing Guide

## Quick Start Testing

### Prerequisites
- Laragon (or similar local development environment) running
- MySQL/MariaDB running
- Application accessible at `http://localhost/production-request-management/`

### Step 1: Import Database Schema

Open PowerShell in the project root and run:
```powershell
.\import-schema.ps1
```

Or using MySQL CLI:
```bash
mysql -u root -h localhost production_request_db < database/migrations/001_initial_schema.sql
```

### Step 2: Verify Database Setup

Run these queries to confirm setup:

```sql
-- Check users were created
SELECT id, nik, username, full_name, role FROM users;

-- Expected output:
-- 1, ADM001, admin, Administrator Produksi, admin
-- 2, PIC001, pic, PIC Produksi, pic

-- Check conveyors
SELECT id, conveyor_name, status FROM master_conveyor ORDER BY id;

-- Expected output:
-- 1, Conveyor A, active
-- 2, Conveyor B, active
-- 3, Conveyor C, inactive

-- Check user-conveyor assignments
SELECT u.username, mc.conveyor_name 
FROM user_conveyor uc
JOIN users u ON u.id = uc.user_id
JOIN master_conveyor mc ON mc.id = uc.conveyor_id
ORDER BY u.username, mc.conveyor_name;

-- Expected output:
-- admin, Conveyor A
-- admin, Conveyor B
-- admin, Conveyor C
-- pic, Conveyor A
-- pic, Conveyor B
```

### Step 3: Test Login Page

1. Open browser and navigate to:
   ```
   http://localhost/production-request-management/public/login
   ```

2. Verify the page displays correctly:
   - [ ] Title shows "Production Request Management System"
   - [ ] Login form is visible
   - [ ] Demo credentials section shows at bottom
   - [ ] "Remember me" checkbox is visible
   - [ ] Form has responsive design (test on mobile view too)

### Step 4: Test Admin Login

**Test Case: Admin Login - Valid Credentials**

1. Username/NIK field: Enter `admin`
2. Password field: Enter `admin123`
3. Click "Sign In"

**Expected Result:**
- ✓ Page redirects to dashboard
- ✓ "Welcome back, Administrator Produksi" message appears
- ✓ Session persists across page refreshes

**Test Case: Admin Login - With NIK**

1. Username/NIK field: Enter `ADM001`
2. Password field: Enter `admin123`
3. Click "Sign In"

**Expected Result:**
- ✓ Successfully logs in (NIK works as identifier)

### Step 5: Test PIC Login

**Test Case: PIC Login - Valid Credentials**

1. Username/NIK field: Enter `pic`
2. Password field: Enter `pic123`
3. Click "Sign In"

**Expected Result:**
- ✓ Page redirects to dashboard
- ✓ "Welcome back, PIC Produksi" message appears

### Step 6: Test Form Validation (Client-side)

**Test Case: Empty Fields**

1. Leave username/NIK empty
2. Try to submit (button should be disabled)

**Expected Result:**
- ✓ Submit button is disabled (grayed out)
- ✓ No form submission occurs

**Test Case: Password Too Short**

1. Username: Enter `admin`
2. Password: Enter `12345` (5 characters)
3. Try to submit

**Expected Result:**
- ✓ Error message: "Password must be at least 6 characters"
- ✓ Submit button remains disabled

**Test Case: Invalid Username Length**

1. Username: Enter `a` (1 character)
2. Password: Enter `password`
3. Try to submit

**Expected Result:**
- ✓ Error message: "Username or NIK must be at least 2 characters"
- ✓ Submit button remains disabled

### Step 7: Test Form Validation (Server-side)

**Test Case: Invalid Credentials**

1. Username: Enter `admin`
2. Password: Enter `wrongpassword`
3. Click "Sign In"

**Expected Result:**
- ✓ Error message: "Invalid username/NIK or password"
- ✓ Remains on login page
- ✓ Form is cleared

**Test Case: Non-existent User**

1. Username: Enter `nonexistent`
2. Password: Enter `password123`
3. Click "Sign In"

**Expected Result:**
- ✓ Error message: "Invalid username/NIK or password"
- ✓ Remains on login page

### Step 8: Test Password Visibility Toggle

1. Go to login page
2. Enter password: `admin123`
3. Click the eye icon on the right side of password field

**Expected Result:**
- ✓ Password becomes visible (shows as text)
- ✓ Icon changes to indicate visibility is on
- ✓ Clicking again hides the password

### Step 9: Test Remember Me

**Test Case: Remember Me - Enabled**

1. Username: `admin`
2. Password: `admin123`
3. Check "Remember me" checkbox
4. Click "Sign In"

**Expected Result:**
- ✓ Logs in successfully
- ✓ Cookie is set (verify in browser DevTools → Application → Cookies)

### Step 10: Test Logout

1. Login as admin
2. Look for logout option in navigation/menu
3. Click logout

**Expected Result:**
- ✓ Redirects to login page
- ✓ Success message: "You have been logged out successfully"
- ✓ Session data is cleared
- ✓ Cannot access protected pages without re-logging in

### Step 11: Test Session Timeout

1. Login as admin
2. Open browser DevTools (F12)
3. Go to Storage/Session Storage
4. Manually delete the session cookie

**Expected Result:**
- ✓ When trying to access protected page, user is redirected to login
- ✓ Error message: "Please login first"

### Step 12: Test CSRF Protection

1. Login as admin
2. Open browser DevTools → Network tab
3. Inspect a POST request (e.g., login form submission)
4. Verify `_csrf_token` is included

**Expected Result:**
- ✓ POST requests include `_csrf_token` parameter
- ✓ Token varies with each page load

### Step 13: Test Role-Based Access

**Test Case: Admin Accessing Admin Routes**

1. Login as admin
2. Navigate to `/dashboard/admin`

**Expected Result:**
- ✓ Page loads successfully

**Test Case: PIC Trying to Access Admin Routes**

1. Login as pic
2. Try to navigate to `/dashboard/admin`

**Expected Result:**
- ✓ Redirected to login or error page
- ✓ Error message: "You do not have permission to access this page"

**Test Case: Non-authenticated User Accessing Protected Route**

1. Open `/dashboard` directly (without logging in)

**Expected Result:**
- ✓ Redirected to login page
- ✓ Error message: "Please login first"

### Step 14: Test Conveyor Access

**Manual Database Test:**

```php
// In a test controller or file
use App\Models\User;

// Get PIC conveyors
$conveyors = User::getUserConveyors(2); // PIC user

echo "PIC has access to: ";
foreach ($conveyors as $c) {
    echo $c->conveyor_name . ", ";
}
// Expected: "PIC has access to: Conveyor A, Conveyor B"
```

### Step 15: Test Password Hashing

**Verify in Database:**

```sql
-- Check that passwords are hashed
SELECT username, LENGTH(password) as password_length FROM users;

-- Expected: passwords should be 60 characters (bcrypt length)
-- admin, 60
-- pic, 60

-- Verify plain passwords are NOT stored
SELECT username, password FROM users WHERE username = 'admin';
-- Should NOT show "admin123" in plain text
```

## Browser Compatibility Testing

Test on different browsers:

- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari
- [ ] Edge

Test on different devices:

- [ ] Desktop (1920x1080)
- [ ] Tablet (768x1024)
- [ ] Mobile (375x667)

## Performance Testing

### Login Speed
1. Clear browser cache
2. Login as admin
3. Check how long it takes
4. Expected: < 2 seconds

### Database Performance
```sql
-- Check query performance
EXPLAIN SELECT * FROM users WHERE username = 'admin';
-- Should use idx_username index

EXPLAIN SELECT * FROM user_conveyor 
JOIN master_conveyor ON user_conveyor.conveyor_id = master_conveyor.id
WHERE user_conveyor.user_id = 2;
-- Should use appropriate indexes
```

## Security Testing

### Test 1: SQL Injection
1. Username: `admin' OR '1'='1`
2. Password: `anything`
3. Click "Sign In"

**Expected Result:**
- ✓ Login fails with "Invalid username/NIK or password"
- ✓ No error messages leak database information

### Test 2: XSS Prevention
1. Username: `<script>alert('XSS')</script>`
2. Password: `password`
3. Click "Sign In"

**Expected Result:**
- ✓ Script does NOT execute
- ✓ Treated as regular text

### Test 3: Session Fixation
1. Get session ID from first login
2. Logout
3. Try to use same session ID

**Expected Result:**
- ✓ Session is invalid (not recognized)

### Test 4: CSRF Attack Simulation
1. Create a form with POST to /login but without CSRF token
2. Try to submit

**Expected Result:**
- ✓ Request fails with "CSRF token validation failed"

## Error Handling Testing

### Test 1: Database Connection Error
1. Stop MySQL
2. Try to login
3. Start MySQL

**Expected Result:**
- ✓ Clear error message (not generic 500 error)
- ✓ User-friendly message displayed

### Test 2: Missing .env File
1. Temporarily rename `.env`
2. Try to login
3. Restore `.env`

**Expected Result:**
- ✓ Application uses defaults from config
- ✓ No fatal errors

## Automated Testing (Unit Tests)

Create tests in `tests/` directory:

```php
<?php
// tests/AuthenticationTest.php

class AuthenticationTest
{
    public function testAdminLoginSuccess()
    {
        $user = User::authenticate('admin', 'admin123');
        assert($user !== null);
        assert($user->role === 'admin');
    }
    
    public function testInvalidPasswordFails()
    {
        $user = User::authenticate('admin', 'wrongpassword');
        assert($user === null);
    }
    
    public function testUserConveyorAssignment()
    {
        $assigned = User::assignConveyor(2, 3);
        assert($assigned === true);
        
        $hasAccess = User::hasConveyorAccess(2, 3);
        assert($hasAccess === true);
    }
    
    public function testConveyorRetrieval()
    {
        $conveyors = User::getUserConveyors(2);
        assert(count($conveyors) >= 2);
    }
}
?>
```

## Checklist Summary

- [ ] Database schema imported successfully
- [ ] Admin login works with username
- [ ] Admin login works with NIK
- [ ] PIC login works
- [ ] Invalid credentials rejected
- [ ] Form validation works (client-side)
- [ ] Form validation works (server-side)
- [ ] Password visibility toggle works
- [ ] Remember me sets cookie
- [ ] Logout clears session
- [ ] Session timeout works
- [ ] CSRF token present in requests
- [ ] Role-based access control works
- [ ] Non-authenticated users redirected to login
- [ ] Conveyor access controlled properly
- [ ] No SQL injection possible
- [ ] No XSS vulnerability
- [ ] Error messages are user-friendly
- [ ] Application works on mobile devices
- [ ] Performance is acceptable

## Reporting Issues

When reporting a bug, include:
1. Steps to reproduce
2. Expected result
3. Actual result
4. Browser and OS
5. Error logs (if any)

Example:
```
Title: Admin login fails with valid credentials
Steps:
1. Navigate to /login
2. Enter username: admin
3. Enter password: admin123
4. Click Sign In

Expected: Redirected to dashboard
Actual: Error message "Invalid username/NIK or password"
Browser: Chrome 120.0
OS: Windows 11
Error: None visible in console
```
