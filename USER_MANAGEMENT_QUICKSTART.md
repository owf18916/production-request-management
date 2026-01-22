# User Management Module - Quick Start Guide

## Accessing the User Management Module

### Admin User Management
1. **Login** as an admin user
2. Navigate to **http://localhost/admin/users**
3. You'll see a list of all users in the system

### Available Admin Functions

#### View All Users
- **URL:** `/admin/users`
- **Features:**
  - Table view with user details
  - Search by NIK, name, or username
  - Filter by role (Admin/PIC)
  - View assigned conveyors
  - Quick actions (Edit/Delete)

#### Create New User
- **URL:** `/admin/users/create`
- **Steps:**
  1. Click "Create New User" button
  2. Fill in user details:
     - NIK (must be unique, alphanumeric)
     - Full Name
     - Username (must be unique, alphanumeric + underscore)
     - Password (min 6 characters)
     - Role (Admin or PIC)
  3. Select conveyors to assign
  4. Click "Create User"
- **Validation:**
  - All fields are required except conveyors
  - Username and NIK must be unique
  - Password strength indicator shows password quality
  - Confirmation password must match

#### Edit User
- **URL:** `/admin/users/edit/{id}`
- **Features:**
  - Update user information
  - Change assigned conveyors
  - Optionally reset password
  - View user creation and login history
- **Restrictions:**
  - Role cannot be changed (contact admin if needed)
  - Cannot change username/NIK to existing ones

#### Delete User
- **Action:** Click "Delete" button on user list
- **Features:**
  - Confirmation dialog before deletion
  - Cannot delete own account
  - Cascades to remove conveyor assignments
  - Soft error if trying to delete self

### User Profile Management

#### View My Profile
- **URL:** `/profile`
- **Accessible to:** All logged-in users
- **Shows:**
  - Full user information
  - Assigned conveyors
  - Quick action buttons

#### Edit My Profile
- **URL:** `/edit-profile`
- **Features:**
  - Change full name only
  - Username, NIK, and role are read-only
- **Validation:**
  - Full name required, max 100 characters

#### Change Password
- **URL:** `/change-password`
- **Requirements:**
  - Current password verification
  - New password min 6 characters
  - New password must differ from current
  - Confirmation password must match
- **Features:**
  - Password strength indicator
  - Live validation
  - Eye icon to toggle password visibility

## Form Features

### Password Management
- **Show/Hide Toggle:** Click eye icon to reveal/hide password
- **Strength Indicator:** Visual indicator of password strength
  - Red: Weak (< 4 chars)
  - Yellow: Fair (4-5 chars)
  - Green: Strong (6+ chars)
- **Live Validation:** Red border if confirmation doesn't match

### Multi-Select Conveyors
- **Checkbox List:** Select multiple conveyors for assignment
- **Visual Feedback:** Checkmarks show selected items
- **Optional:** Can assign zero conveyors

### Search & Filter
- **Search Box:** Type to search by NIK, name, or username
- **Role Filter:** Dropdown to filter by admin or PIC role
- **Clear Button:** Reset search and filters to show all users

## Data Validation

### Field Requirements
| Field | Required | Format | Max Length | Notes |
|-------|----------|--------|-----------|-------|
| NIK | Yes | Alphanumeric | 50 | Must be unique |
| Full Name | Yes | Text | 100 | - |
| Username | Yes | Alphanumeric + _ | 50 | Must be unique |
| Password | Yes* | Min 6 chars | - | Optional on edit |
| Role | Yes | admin/pic | - | Cannot change |
| Conveyors | No | IDs | - | Multi-select |

*Required when creating, optional when editing

### Error Messages
The system provides clear error messages for:
- Duplicate NIK/Username
- Password mismatch
- Invalid format
- Missing required fields
- Security token expiration (CSRF)

## Security Features

### Protected Routes
- **Admin Routes:** Protected by Admin middleware
  - Only users with admin role can access
  - `/admin/users/*` routes
- **Profile Routes:** Protected by Authenticate middleware
  - All logged-in users can access own profile
  - `/profile`, `/edit-profile`, `/change-password`

### Password Security
- Passwords hashed with PHP password_hash()
- Current password verified before changes
- No plaintext passwords stored
- Automatic hashing on create/update

### CSRF Protection
- All forms include CSRF tokens
- Invalid tokens rejected with error message

### Input Protection
- All user inputs sanitized
- XSS prevention with htmlspecialchars()
- SQL injection prevented with prepared statements

## Troubleshooting

### Cannot Access User Management
- Verify you're logged in
- Check if you have admin role
- Admin users can only manage users if they have 'admin' role

### Password Change Failed
- Verify current password is correct
- New password must be different from current
- Password must be at least 6 characters
- Confirm password must match new password

### Duplicate Username/NIK Error
- Choose a unique username and NIK
- Check with admin if you need to reuse credentials
- Edit page allows changing to different value

### Lost Admin Access
- Contact the system administrator
- Database administrator may need to reset roles

## Common Tasks

### Creating a New PIC User
1. Go to `/admin/users`
2. Click "Create New User"
3. Fill in details with Role = "PIC"
4. Select 1 or more conveyors
5. Click "Create User"
6. Share credentials securely with the user

### Assigning User to Conveyor
1. Go to `/admin/users`
2. Click "Edit" on the user
3. Check conveyor checkboxes
4. Click "Update User"

### Resetting User Password
1. Go to `/admin/users`
2. Click "Edit" on the user
3. Enter new password
4. Click "Update User"
5. Share temporary password securely

### Deactivating a User
1. Go to `/admin/users`
2. Find the user to deactivate
3. Click "Delete" button
4. Confirm deletion
5. User account and conveyor assignments removed

## Tips & Best Practices

1. **Password Selection:** Use strong passwords with mixed case and numbers
2. **Unique Identifiers:** NIK should be the employee ID or unique identifier
3. **Username Naming:** Use lowercase with underscores (e.g., john_doe)
4. **Conveyor Assignment:** Assign users to conveyors they'll actually work with
5. **Regular Updates:** Keep user information current in the system
6. **Account Management:** Promptly remove users who no longer need access
7. **Role Management:** Use 'pic' role for production floor users, 'admin' for managers

## Screenshots & Workflow

### Admin User List Workflow
```
Login → Dashboard → Admin Panel → User Management List
  ↓
  ├─→ Create User ─→ Form ─→ Store
  ├─→ Edit User ─→ Form ─→ Update
  ├─→ Delete User ─→ Confirm ─→ Delete
  └─→ Search/Filter → View Results
```

### User Profile Workflow
```
Logged In → Profile Link
  ↓
  ├─→ View Profile (Read-only)
  ├─→ Edit Profile ─→ Update Name
  └─→ Change Password ─→ Update
```

## Support

For issues or questions:
- Check [USER_MANAGEMENT_IMPLEMENTATION.md](USER_MANAGEMENT_IMPLEMENTATION.md) for technical details
- Review validation messages for specific errors
- Contact your system administrator for access issues
