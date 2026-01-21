# Authentication System Documentation

## Overview

The Production Request Management System now includes a complete, production-ready authentication system with:

- **User Authentication** with username/NIK and password login
- **Two User Roles**: Admin (Admin Produksi) and PIC (PIC Produksi)
- **Session-based Authentication** with secure session management
- **Role-based Access Control** (RBAC) middleware for admin-only routes
- **User-Conveyor Many-to-Many Relationship** for granular access control

## Database Tables

### 1. `users` Table
Stores user account information with the new authentication structure.

**Fields:**
- `id` (INT, PK, AUTO_INCREMENT) - User ID
- `nik` (VARCHAR 50, UNIQUE) - Employee/Staff NIK (Nomor Induk Karyawan)
- `username` (VARCHAR 50, UNIQUE) - Login username
- `password` (VARCHAR 255) - Bcrypt hashed password (cost 12)
- `full_name` (VARCHAR 100) - User's full name
- `role` (ENUM: 'admin', 'pic') - User role
- `last_login_at` (TIMESTAMP NULL) - Last login timestamp
- `created_at` (TIMESTAMP) - Account creation timestamp
- `updated_at` (TIMESTAMP) - Last update timestamp

**Indexes:**
- `idx_nik` - For fast NIK lookups
- `idx_username` - For fast username lookups
- `idx_role` - For role-based queries
- `idx_created_at` - For sorting by creation date

### 2. `master_conveyor` Table
Stores conveyor production line information.

**Fields:**
- `id` (INT, PK, AUTO_INCREMENT) - Conveyor ID
- `conveyor_name` (VARCHAR 100) - Conveyor name/identifier
- `status` (ENUM: 'active', 'inactive') - Operational status
- `created_by` (INT, FK) - User ID who created this conveyor
- `created_at` (TIMESTAMP) - Creation timestamp
- `updated_at` (TIMESTAMP) - Last update timestamp

**Constraints:**
- Foreign key to `users.id` (created_by)

**Indexes:**
- `idx_status` - For filtering active/inactive conveyors
- `idx_created_at` - For sorting by creation date

### 3. `user_conveyor` Table
Many-to-Many relationship linking users to conveyors for granular access control.

**Fields:**
- `id` (INT, PK, AUTO_INCREMENT) - Relationship ID
- `user_id` (INT, FK) - Reference to users table
- `conveyor_id` (INT, FK) - Reference to master_conveyor table
- `created_at` (TIMESTAMP) - Assignment timestamp

**Constraints:**
- Foreign keys with CASCADE delete
- UNIQUE KEY `(user_id, conveyor_id)` - Prevents duplicate assignments
- `idx_conveyor_id` - For reverse lookups

## Models

### User Model (`app/Models/User.php`)

**Key Methods:**

#### Authentication
```php
// Authenticate with username or NIK
$user = User::authenticate($identifier, $password);
// Returns: user object or null
```

#### User Retrieval
```php
// Get user by ID
$user = User::getUserById($id);

// Get user by username
$user = User::findByUsername($username);

// Get user by NIK
$user = User::findByNIK($nik);

// Get all users by role
$admins = User::getByRole('admin');
$pics = User::getByRole('pic');

// Get all active users
$users = User::getActive();
```

#### Conveyor Management
```php
// Get all conveyors for a user
$conveyors = User::getUserConveyors($userId);

// Assign conveyor to user
User::assignConveyor($userId, $conveyorId);

// Remove conveyor from user
User::removeConveyor($userId, $conveyorId);

// Check if user has access to conveyor
$hasAccess = User::hasConveyorAccess($userId, $conveyorId);
```

#### User Creation
```php
// Create new user with hashed password
User::createUser([
    'nik' => 'EMP001',
    'username' => 'john',
    'password' => 'password123',  // Will be hashed
    'full_name' => 'John Doe',
    'role' => 'pic'
]);
```

### Conveyor Model (`app/Models/Conveyor.php`)

**Key Methods:**

```php
// Get all conveyors
$conveyors = Conveyor::getAll();

// Get only active conveyors
$active = Conveyor::getActive();

// Find specific conveyor
$conveyor = Conveyor::findById($id);

// Create new conveyor
Conveyor::createConveyor([
    'conveyor_name' => 'Line A',
    'status' => 'active',
    'created_by' => 1
]);

// Update conveyor
Conveyor::updateConveyor($id, ['status' => 'inactive']);

// Delete conveyor
Conveyor::deleteConveyor($id);

// Get users assigned to conveyor
$users = Conveyor::getConveyorUsers($conveyorId);

// Get user count in conveyor
$count = Conveyor::getUserCount($conveyorId);
```

## Controllers

### Auth Controller (`app/Controllers/Auth.php`)

**Routes:**
- `GET /login` → `showLoginForm()` - Display login page
- `POST /login` → `login()` - Handle login submission
- `GET /logout` → `logout()` - Handle user logout

**Features:**
- CSRF token validation
- Input validation with error messages
- Session regeneration after login
- Role-based dashboard redirection (Admin → /dashboard/admin, PIC → /dashboard)
- Remember me functionality
- Secure password verification

## Middleware

### Authenticate Middleware (`app/Middleware/Authenticate.php`)
Checks if user is logged in. Redirects to login page if not authenticated.

**Usage:**
```php
$router->get('/dashboard', 'Dashboard@index', ['middleware' => 'Authenticate']);
```

### Admin Middleware (`app/Middleware/Admin.php`)
Checks if user has 'admin' role. Returns false if user is not admin.

**Usage:**
```php
$router->get('/admin/settings', 'Admin@settings', 
    ['middleware' => ['Authenticate', 'Admin']]
);
```

### PIC Middleware (`app/Middleware/Pic.php`)
Checks if user has 'pic' role. Returns false if user is not PIC.

**Usage:**
```php
$router->get('/dashboard', 'Dashboard@index', 
    ['middleware' => ['Authenticate', 'Pic']]
);
```

## Views

### Login View (`app/Views/auth/login.php`)

**Features:**
- Modern responsive design with TailwindCSS gradient background
- Dual-field login (username or NIK)
- Password visibility toggle
- Client-side validation with Alpine.js
- Server-side error display
- Remember me checkbox
- Demo credentials display
- Loading state on form submission
- Security-focused: CSRF token, secure password handling

**Form Validation:**
- Identifier: minimum 2 characters
- Password: minimum 6 characters
- Real-time error messages
- Form submission disabled until valid

## Security Features

### Password Security
```php
// Passwords are hashed using bcrypt with cost of 12
$hash = Security::hashPassword($password);
$verified = Security::verifyPassword($password, $hash);
```

### Session Management
- Session regeneration after login
- Automatic session timeout (1 hour default)
- CSRF token generation and verification
- HttpOnly, Secure, and SameSite cookie flags

### Input Validation
- Server-side validation of all inputs
- XSS prevention through output escaping
- Email/identifier sanitization
- CSRF token verification on all POST requests

## Seed Data

The database includes the following sample users and conveyors:

### Users
| NIK | Username | Password | Full Name | Role |
|-----|----------|----------|-----------|------|
| ADM001 | admin | admin123 | Administrator Produksi | admin |
| PIC001 | pic | pic123 | PIC Produksi | pic |

### Conveyors
| ID | Name | Status | Created By |
|----|------|--------|-----------|
| 1 | Conveyor A | active | admin |
| 2 | Conveyor B | active | admin |
| 3 | Conveyor C | inactive | admin |

### Assignments
- **Admin**: Assigned to all 3 conveyors
- **PIC**: Assigned to Conveyor A and B

## Setup Instructions

### 1. Import Database Schema

**Using PowerShell (Windows):**
```powershell
.\import-schema.ps1
```

**Using Batch (Windows):**
```batch
import-schema.bat
```

**Using MySQL CLI:**
```bash
mysql -u root -h localhost production_request_db < database/migrations/001_initial_schema.sql
```

### 2. Verify Installation

Check that all tables were created:
```sql
-- Check users table
SELECT * FROM users;

-- Check conveyors
SELECT * FROM master_conveyor;

-- Check assignments
SELECT u.username, mc.conveyor_name FROM user_conveyor uc
JOIN users u ON u.id = uc.user_id
JOIN master_conveyor mc ON mc.id = uc.conveyor_id;
```

### 3. Test Login

1. Navigate to `http://localhost/production-request-management/public/login`
2. Try admin credentials:
   - Username: `admin`
   - Password: `admin123`
3. Try PIC credentials:
   - Username: `pic`
   - Password: `pic123`

## Usage Examples

### Login a User
```php
<?php
// In controller or view
use App\Models\User;
use App\Session;

// Attempt authentication
$user = User::authenticate('admin', 'admin123');

if ($user) {
    // Store in session
    Session::put('user_id', $user->id);
    Session::put('user_role', $user->role);
    
    // Redirect to dashboard
    header('Location: /dashboard');
}
?>
```

### Check User Conveyors
```php
<?php
use App\Models\User;

$userId = 2; // PIC user
$conveyors = User::getUserConveyors($userId);

foreach ($conveyors as $conveyor) {
    echo $conveyor->conveyor_name; // Conveyor A, Conveyor B
}
?>
```

### Assign User to Conveyor
```php
<?php
use App\Models\User;

$userId = 2;
$conveyorId = 3; // Conveyor C

if (User::assignConveyor($userId, $conveyorId)) {
    echo "Conveyor assigned successfully";
}
?>
```

### Protect Admin Routes
```php
<?php
// In routes/web.php
$router->get('/admin/dashboard', 'Admin@dashboard', 
    ['middleware' => ['Authenticate', 'Admin']]
);

// In controller
class Admin extends Controller
{
    public function dashboard()
    {
        // Only admin users can reach here
        $this->view('admin/dashboard');
    }
}
?>
```

## Configuration

### Password Hashing
Edit `config/app.php`:
```php
'security' => [
    'password_hash_algo' => PASSWORD_BCRYPT,
    'password_hash_options' => ['cost' => 12],
],
```

### Session Timeout
Edit `app/Session.php`:
```php
private const SESSION_LIFETIME = 3600; // 1 hour in seconds
```

## API Usage

The authentication system can also be used with AJAX requests. Include CSRF token in requests:

```javascript
// Get CSRF token from session
const csrfToken = document.querySelector('[name="_csrf_token"]').value;

// Make authenticated request
fetch('/api/requests', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': csrfToken
    },
    body: JSON.stringify({
        title: 'New Request'
    })
});
```

## Troubleshooting

### Login Issues
1. **"Invalid username/NIK or password"**
   - Verify username/NIK exists in database
   - Check password is correct
   - Ensure user role is 'admin' or 'pic'

2. **"Security token expired"**
   - Clear browser cache
   - Try again - CSRF token regenerates on each page load

3. **Database connection error**
   - Check `.env` file has correct database credentials
   - Verify MySQL is running
   - Ensure database `production_request_db` exists

### Session Issues
1. **Logged out automatically**
   - Session timeout is 1 hour by default
   - Clear browser cookies and try login again

2. **Session data not persisting**
   - Verify session handler in PHP configuration
   - Check `/tmp` directory permissions (Linux/Mac)
   - Check AppData/Local/Temp permissions (Windows)

## Next Steps

1. **Add password reset functionality**
2. **Implement email verification**
3. **Add two-factor authentication**
4. **Create user management dashboard**
5. **Implement audit logging for login attempts**
6. **Add IP-based rate limiting**
7. **Create role and permission system**

## Support

For issues or questions:
1. Check this documentation
2. Review error logs in `logs/` directory
3. Check PHP error logs
4. Review DEVELOPMENT.md for coding guidelines
