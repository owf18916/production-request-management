# Obstacles & Solutions Summary

**Last Updated:** January 22, 2026  
**Project:** Production Request Management System  
**Status:** Used for User Management, Master ATK, and Master Checksheet modules

---

## Critical Obstacles Encountered & Solutions

### 🔴 OBSTACLE 1: Method Signature Incompatibility

**Problem:**
```php
// ❌ WRONG - Instance method with wrong parameter type
public function update(int $id, array $data): bool { }

// ❌ WRONG - setTitle with void return type
public function setTitle(string $title): void { }
```

**Root Cause:** Child class methods didn't match parent class signatures or lacked required static keyword.

**Solution Applied:**
```php
// ✅ CORRECT - Static method with mixed $id type
public static function update(mixed $id, array $data): bool { }

// ✅ CORRECT - setTitle returns self
public function setTitle(string $title): self 
{
    $this->data['title'] = $title;
    return $this;
}
```

**When to Apply:** On all new Model classes implementing CRUD methods (create, update, delete, search, findById)

**Files Applied To:**
- app/Models/User.php
- app/Models/MasterATK.php
- app/Models/MasterChecksheet.php

---

### 🔴 OBSTACLE 2: Layout System Not Rendering

**Problem:**
```php
// ❌ WRONG - Views calling non-existent layout method
<?php
$this->layout('layouts/main');
?>
<div>Content here</div>
```

**Root Cause:** Controller's `view()` method didn't wrap views with layout template; views attempted to call layout() method that doesn't exist.

**Solution Applied in Controller:**
```php
// ✅ CORRECT - Use output buffering to capture and wrap view
public function view(string $view, array $data = []): void
{
    extract($data);
    ob_start();
    require base_path("app/Views/{$view}.php");
    $content = ob_get_clean();
    
    require base_path('app/Views/layouts/main.php');
}
```

**View Implementation:**
```php
<!-- ✅ CORRECT - View starts directly with HTML, no layout() call -->
<div class="min-h-screen bg-gray-100">
    <!-- Content here -->
</div>
```

**When to Apply:** On all new view files - they should start directly with HTML, not call layout()

**Files Affected:**
- All views in app/Views/

---

### 🔴 OBSTACLE 3: Empty PHP Tags at View Start

**Problem:**
```php
<!-- ❌ WRONG - Empty PHP tags at start cause rendering issues -->
<?php ?>
<div class="container">...</div>
```

**Root Cause:** Empty opening/closing PHP tags sometimes interfered with output buffering and layout system.

**Solution Applied:**
```php
<!-- ✅ CORRECT - Start directly with HTML content -->
<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <!-- Content -->
</div>
```

**When to Apply:** ALL view files must start directly with HTML, never with empty PHP tags

**Files Applied To:**
- app/Views/auth/login.php
- app/Views/auth/register.php
- app/Views/dashboard/admin.php
- app/Views/dashboard/index.php
- app/Views/admin/master/atk/index.php
- app/Views/admin/master/atk/create.php
- app/Views/admin/master/atk/edit.php
- app/Views/admin/master/checksheet/index.php
- app/Views/admin/master/checksheet/create.php
- app/Views/admin/master/checksheet/edit.php

---

### 🔴 OBSTACLE 4: Duplicate Routes

**Problem:**
```php
// ❌ WRONG - Same endpoint mapped to multiple controllers
$router->get('/admin/users', 'Admin@users', ['middleware' => ['Authenticate', 'Admin']]);
$router->get('/admin/users', 'User@index', ['middleware' => ['Authenticate', 'Admin']]);
```

**Root Cause:** Routes added without checking existing routes for duplicates; second route overrides first.

**Solution Applied:**
```php
// ✅ CORRECT - Always verify no duplicates before adding new routes
// Verify existing routes with: grep_search or read_file on routes/web.php BEFORE adding

// Master ATK routes
$router->get('/admin/master/atk', 'MasterATK@index', ['middleware' => ['Authenticate', 'Admin']]);
$router->get('/admin/master/atk/create', 'MasterATK@create', ['middleware' => ['Authenticate', 'Admin']]);
// ... (7 total routes)

// Master Checksheet routes (separate section, no conflicts)
$router->get('/admin/master/checksheet', 'MasterChecksheet@index', ['middleware' => ['Authenticate', 'Admin']]);
$router->get('/admin/master/checksheet/create', 'MasterChecksheet@create', ['middleware' => ['Authenticate', 'Admin']]);
// ... (7 total routes)
```

**Prevention Checklist:**
1. Read existing routes/web.php before adding new routes
2. Check current route patterns with grep_search
3. Use unique namespaces/controller names for new modules
4. Add new route sections clearly separated in code

**Files Affected:**
- routes/web.php

---

### 🔴 OBSTACLE 5: Database Table Not Found

**Problem:**
```bash
# ❌ WRONG - Migration file created but table not actually in database
mysql> DESCRIBE master_atk;
# Error: Table 'production_request_db.master_atk' doesn't exist
```

**Root Cause:** Migration SQL file created but not executed; assumed file creation = table exists.

**Solution Applied:**
```bash
# ✅ CORRECT - Execute migration immediately after creating SQL file
mysql -u root production_request_db --execute="CREATE TABLE IF NOT EXISTS master_atk (...)"

# Verify with:
mysql -u root production_request_db --execute="DESCRIBE master_atk;"
```

**Prevention Checklist:**
1. Create migration SQL file with proper schema
2. **Immediately execute** the migration with mysql command
3. **Immediately verify** with DESCRIBE command
4. Don't proceed until DESCRIBE shows correct table structure

**Files Affected:**
- database/migrations/003_create_master_atk.sql
- database/migrations/004_create_master_checksheet.sql

---

## Successful Patterns Established

### ✅ Model Structure Template

```php
<?php

namespace App\Models;

use App\Model;
use App\Database;

class MasterModule extends Model
{
    protected string $table = 'master_module';
    protected string $primaryKey = 'id';

    protected array $fillable = [
        'kode_module',
        'nama_module',
        'created_by',
    ];

    /**
     * Get all records
     */
    public static function getAll(): array
    {
        $sql = "SELECT * FROM master_module ORDER BY nama_module ASC";
        return Database::results($sql);
    }

    /**
     * Find by ID
     */
    public static function findById(int $id): ?object
    {
        $sql = "SELECT * FROM master_module WHERE id = ?";
        return Database::row($sql, [$id]);
    }

    /**
     * Create - STATIC method
     */
    public static function create(array $data): bool
    {
        $fillable = (new static())->fillable;
        $columns = [];
        $values = [];
        $placeholders = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $fillable)) {
                $columns[] = $key;
                $values[] = $value;
                $placeholders[] = '?';
            }
        }

        $sql = "INSERT INTO master_module (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";

        try {
            Database::query($sql, $values);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Update - STATIC method with MIXED $id
     */
    public static function update(mixed $id, array $data): bool
    {
        $fillable = (new static())->fillable;
        $updates = [];
        $values = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $fillable)) {
                $updates[] = "$key = ?";
                $values[] = $value;
            }
        }

        if (empty($updates)) {
            return false;
        }

        $updates[] = "updated_at = ?";
        $values[] = date('Y-m-d H:i:s');
        $values[] = $id;

        $sql = "UPDATE master_module SET " . implode(', ', $updates) . " WHERE id = ?";

        try {
            Database::query($sql, $values);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Delete - STATIC method with MIXED $id
     */
    public static function delete(mixed $id): bool
    {
        $sql = "DELETE FROM master_module WHERE id = ?";
        try {
            Database::query($sql, [$id]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Search
     */
    public static function search(string $query): array
    {
        $query = "%{$query}%";
        $sql = "SELECT * FROM master_module 
                WHERE kode_module LIKE ? OR nama_module LIKE ? 
                ORDER BY nama_module ASC";
        return Database::results($sql, [$query, $query]);
    }

    /**
     * Check if kode exists
     */
    public static function kodeExists(string $kode): bool
    {
        $sql = "SELECT id FROM master_module WHERE kode_module = ?";
        return Database::row($sql, [$kode]) !== null;
    }

    /**
     * Count records
     */
    public static function count(): int
    {
        $sql = "SELECT COUNT(*) as count FROM master_module";
        $result = Database::row($sql);
        return $result->count ?? 0;
    }
}
```

**Key Points:**
- ALL methods are `static`
- Update/Delete use `mixed $id` parameter
- Uses prepared statements for all queries
- Error handling with try-catch
- Timestamps auto-managed in update
- 8 methods total: getAll, findById, create, update, delete, search, kodeExists, count

---

### ✅ Controller Structure Template

```php
<?php

namespace App\Controllers;

use App\Controller;
use App\Session;
use App\Security;
use App\Models\MasterModule as MasterModuleModel;

class MasterModule extends Controller
{
    /**
     * Index - List all
     */
    public function index(): void
    {
        $search = $this->input('search');
        
        if ($search) {
            $modules = MasterModuleModel::search($search);
        } else {
            $modules = MasterModuleModel::getAll();
        }

        $this->setTitle('Master Module Management');
        $this->view('admin/master/module/index', [
            'modules' => $modules,
            'search' => $search,
            'totalCount' => count($modules),
        ]);
    }

    /**
     * Create - Show form
     */
    public function create(): void
    {
        $this->setTitle('Add Master Module');
        $this->view('admin/master/module/create');
    }

    /**
     * Store - Save new record
     */
    public function store(): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('/admin/master/module', 'error', 'Invalid request');
        }

        $kode = $this->input('kode_module');
        $nama = $this->input('nama_module');

        $errors = [];

        // Validation
        if (!$kode) {
            $errors['kode_module'] = 'Kode module is required';
        } elseif (strlen($kode) > 50) {
            $errors['kode_module'] = 'Kode must not exceed 50 characters';
        } elseif (!preg_match('/^[A-Z0-9\-]+$/', $kode)) {
            $errors['kode_module'] = 'Kode must contain only uppercase letters, numbers, and hyphens';
        } elseif (MasterModuleModel::kodeExists($kode)) {
            $errors['kode_module'] = 'Kode already exists';
        }

        if (!$nama) {
            $errors['nama_module'] = 'Nama module is required';
        } elseif (strlen($nama) > 150) {
            $errors['nama_module'] = 'Nama must not exceed 150 characters';
        }

        if (!empty($errors)) {
            $this->setTitle('Add Master Module');
            $this->view('admin/master/module/create', [
                'errors' => $errors,
                'kode_module' => $kode,
                'nama_module' => $nama,
            ]);
            return;
        }

        // Save
        $success = MasterModuleModel::create([
            'kode_module' => $kode,
            'nama_module' => $nama,
            'created_by' => session('user_id'),
        ]);

        if ($success) {
            $this->redirect('/admin/master/module', 'success', 'Module created successfully');
        } else {
            $this->redirect('/admin/master/module/create', 'error', 'Failed to create module');
        }
    }

    /**
     * Edit - Show edit form
     */
    public function edit(int $id): void
    {
        $module = MasterModuleModel::findById($id);
        if (!$module) {
            $this->redirect('/admin/master/module', 'error', 'Module not found');
        }

        $this->setTitle('Edit Master Module');
        $this->view('admin/master/module/edit', ['module' => $module]);
    }

    /**
     * Update - Save changes
     */
    public function update(int $id): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('/admin/master/module', 'error', 'Invalid request');
        }

        $module = MasterModuleModel::findById($id);
        if (!$module) {
            $this->redirect('/admin/master/module', 'error', 'Module not found');
        }

        $kode = $this->input('kode_module');
        $nama = $this->input('nama_module');

        $errors = [];

        // Validation
        if (!$kode) {
            $errors['kode_module'] = 'Kode module is required';
        } elseif (strlen($kode) > 50) {
            $errors['kode_module'] = 'Kode must not exceed 50 characters';
        } elseif (!preg_match('/^[A-Z0-9\-]+$/', $kode)) {
            $errors['kode_module'] = 'Kode must contain only uppercase letters, numbers, and hyphens';
        } elseif ($kode !== $module->kode_module && MasterModuleModel::kodeExists($kode)) {
            $errors['kode_module'] = 'Kode already exists';
        }

        if (!$nama) {
            $errors['nama_module'] = 'Nama module is required';
        } elseif (strlen($nama) > 150) {
            $errors['nama_module'] = 'Nama must not exceed 150 characters';
        }

        if (!empty($errors)) {
            $this->setTitle('Edit Master Module');
            $this->view('admin/master/module/edit', [
                'module' => $module,
                'errors' => $errors,
            ]);
            return;
        }

        // Update
        $success = MasterModuleModel::update($id, [
            'kode_module' => $kode,
            'nama_module' => $nama,
        ]);

        if ($success) {
            $this->redirect('/admin/master/module', 'success', 'Module updated successfully');
        } else {
            $this->redirect("/admin/master/module/edit/{$id}", 'error', 'Failed to update module');
        }
    }

    /**
     * Delete - Remove record
     */
    public function delete(int $id): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('/admin/master/module', 'error', 'Invalid request');
        }

        $module = MasterModuleModel::findById($id);
        if (!$module) {
            $this->redirect('/admin/master/module', 'error', 'Module not found');
        }

        $success = MasterModuleModel::delete($id);

        if ($success) {
            $this->redirect('/admin/master/module', 'success', 'Module deleted successfully');
        } else {
            $this->redirect('/admin/master/module', 'error', 'Failed to delete module');
        }
    }

    /**
     * Search - AJAX response
     */
    public function search(): void
    {
        header('Content-Type: application/json');
        $query = $this->input('q', '');
        
        if (strlen($query) < 2) {
            echo json_encode(['results' => []]);
            exit;
        }

        $results = MasterModuleModel::search($query);
        echo json_encode(['results' => $results]);
        exit;
    }

    /**
     * Set page title
     */
    protected function setTitle(string $title): self
    {
        $this->data['title'] = $title;
        return $this;
    }
}
```

**Key Points:**
- `setTitle()` returns `self` with `return $this;`
- 7 methods: index, create, store, edit, update, delete, search
- CSRF validation on all POST methods
- Comprehensive input validation
- Flash messages for user feedback
- JSON response for search (AJAX)

---

### ✅ Database Migration Template

```sql
CREATE TABLE IF NOT EXISTS master_module (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kode_module VARCHAR(50) UNIQUE NOT NULL,
    nama_module VARCHAR(150) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT NOT NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_kode_module (kode_module),
    INDEX idx_nama_module (nama_module)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Key Points:**
- Unique constraint on kode field
- Foreign key on created_by -> users table
- Indexes on searchable columns
- Timestamps: created_at (immutable), updated_at (auto-updated)
- UTF8MB4 charset for full Unicode support

---

### ✅ Route Registration Template

```php
// Master Module routes - ALWAYS verify no duplicates first
$router->get('/admin/master/module', 'MasterModule@index', ['middleware' => ['Authenticate', 'Admin']]);
$router->get('/admin/master/module/create', 'MasterModule@create', ['middleware' => ['Authenticate', 'Admin']]);
$router->post('/admin/master/module/store', 'MasterModule@store', ['middleware' => ['Authenticate', 'Admin']]);
$router->get('/admin/master/module/edit/{id}', 'MasterModule@edit', ['middleware' => ['Authenticate', 'Admin']]);
$router->post('/admin/master/module/update/{id}', 'MasterModule@update', ['middleware' => ['Authenticate', 'Admin']]);
$router->post('/admin/master/module/delete/{id}', 'MasterModule@delete', ['middleware' => ['Authenticate', 'Admin']]);
$router->get('/admin/master/module/search', 'MasterModule@search', ['middleware' => ['Authenticate', 'Admin']]);
```

**Key Points:**
- 7 routes total for each module
- Admin middleware on all routes
- RESTful naming convention
- Search route returns JSON for AJAX

---

### ✅ View Template Structure

**index.php:**
```php
<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header with stats -->
        <!-- Search box -->
        <!-- Add button -->
        <!-- Data table with delete modal (Alpine.js) -->
    </div>
</div>
```

**create.php:**
```php
<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <!-- Form with validation hints -->
        <!-- Info box with tips -->
    </div>
</div>
```

**edit.php:**
```php
<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <!-- Pre-populated form -->
        <!-- Timestamps display -->
    </div>
</div>
```

**Key Points:**
- Views start directly with `<div>`, NO empty PHP tags
- NO layout() calls in views
- Layout handled by Controller's view() method with output buffering
- TailwindCSS for styling
- Alpine.js for interactivity (modals, validation)

---

### ✅ Navbar Integration Template

Add to [app/Views/layouts/main.php](app/Views/layouts/main.php) main navigation:

```php
<!-- Main Navigation (after other admin links) -->
<a href="<?php echo url('/admin/master/module'); ?>" class="text-gray-700 hover:text-gray-900 font-medium flex items-center">
    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
    </svg>
    Module Name
</a>
```

Add to dropdown menu:

```php
<!-- User Dropdown Menu -->
<a href="<?php echo url('/admin/master/module'); ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Master Module</a>
```

---

## Implementation Checklist for New Modules

Use this checklist when implementing new master modules to avoid all known obstacles:

- [ ] **Model Created** (`app/Models/ModuleName.php`)
  - [ ] All methods are `static`
  - [ ] Update/delete use `mixed $id` parameter
  - [ ] 8 methods implemented: getAll, findById, create, update, delete, search, kodeExists, count
  - [ ] Prepared statements for all queries
  - [ ] Error handling with try-catch

- [ ] **Controller Created** (`app/Controllers/ModuleName.php`)
  - [ ] 7 methods implemented: index, create, store, edit, update, delete, search
  - [ ] `setTitle()` returns `self` with `return $this;`
  - [ ] CSRF validation on POST methods
  - [ ] Comprehensive input validation
  - [ ] Flash messages for feedback
  - [ ] JSON response for search

- [ ] **Views Created** (3 files: index, create, edit)
  - [ ] All views start with clean HTML `<div>`, NO empty PHP tags
  - [ ] NO layout() calls in any view
  - [ ] TailwindCSS styling applied
  - [ ] Alpine.js for interactivity

- [ ] **Database Migration**
  - [ ] SQL file created in `database/migrations/`
  - [ ] Migration **EXECUTED** immediately (not just file created)
  - [ ] Table verified with `DESCRIBE` command
  - [ ] Unique constraint on kode field
  - [ ] Foreign key on created_by
  - [ ] Indexes on searchable columns

- [ ] **Routes Configured**
  - [ ] Verified NO duplicate routes exist
  - [ ] 7 routes added to `routes/web.php`
  - [ ] Admin middleware on all routes
  - [ ] All routes in separate code section

- [ ] **Navbar Integration**
  - [ ] Main navigation link added
  - [ ] Dropdown menu link added
  - [ ] Admin role check in place

- [ ] **Syntax & Error Checking**
  - [ ] PHP syntax validation: `php -l app/Models/ModuleName.php`
  - [ ] PHP syntax validation: `php -l app/Controllers/ModuleName.php`
  - [ ] No errors in VS Code diagnostics

---

## Quick Reference Commands

```bash
# Verify table exists
mysql -u root production_request_db --execute="DESCRIBE table_name;"

# Create table from migration
mysql -u root production_request_db --execute="CREATE TABLE IF NOT EXISTS table_name (...)"

# Check PHP syntax
php -l "path/to/file.php"

# List all tables
mysql -u root production_request_db --execute="SHOW TABLES;"

# Check route conflicts
grep -n "'/admin/path'" routes/web.php
```

---

## Modules Completed Using This Pattern

1. ✅ **User Management** (6 routes, 2 views, 1 controller, 1 model)
2. ✅ **Master ATK** (7 routes, 3 views, 1 controller, 1 model, database table)
3. ✅ **Master Checksheet** (7 routes, 3 views, 1 controller, 1 model, database table)
4. ✅ **Request ATK** (7 routes, 5 views, 1 controller, 1 model, 2 database tables, auto-generated request numbers, status workflow)

All modules implemented **without errors** following this pattern.

---

## New Obstacles Encountered in Request ATK Module

### 🔴 OBSTACLE 6: Route Matching Priority Issue

**Problem:**
```
User accesses: /requests/atk
Router matches: /requests/{id} (treating 'atk' as parameter id)
Result: Shows wrong page with wrong controller action
```

**Root Cause:** Router processes routes sequentially; generic parameterized routes `/requests/{id}` match before specific static routes `/requests/atk` because regex pattern `[^/]+` matches anything.

**Solution Applied in Router.php:**
```php
// ✅ CORRECT - Two-pass matching strategy
public function dispatch(string $method, string $path): void
{
    // First pass: match exact static routes (no parameters)
    foreach ($this->routes as $route) {
        if ($route->matches($method, $path) && strpos($route->getPath(), '{') === false) {
            $this->handleRoute($route);
            return;
        }
    }

    // Second pass: match dynamic routes (with parameters)
    foreach ($this->routes as $route) {
        if ($route->matches($method, $path)) {
            $this->handleRoute($route);
            return;
        }
    }

    http_response_code(404);
    die('404 - Route not found');
}
```

**Prevention Checklist:**
1. Specific static routes MUST come before generic parameterized routes
2. Always prioritize exact matches over pattern matches
3. When adding new specific routes, verify they won't be matched by existing generic routes

**Files Applied To:**
- app/Router.php

---

### 🔴 OBSTACLE 7: Database Column Name Mismatch

**Problem:**
```php
// ❌ WRONG - Assumed column name based on similar tables
$sql = "SELECT ra.*, u1.nama as requester FROM request_atk ra
        LEFT JOIN users u1 ON ra.requested_by = u1.id";
// Error: Unknown column 'u1.nama' in 'field list'
```

**Root Cause:** Copied query from similar table but users table has `full_name` not `nama`; didn't verify actual database schema.

**Solution Applied:**
```php
// ✅ CORRECT - Always verify table structure first
// Step 1: Check table structure
mysql> DESCRIBE users;
// Shows: full_name VARCHAR(100)

// Step 2: Use correct column name in query
$sql = "SELECT ra.*, u1.full_name as requester FROM request_atk ra
        LEFT JOIN users u1 ON ra.requested_by = u1.id";
```

**Prevention Checklist:**
1. BEFORE writing JOIN queries, run `DESCRIBE` on all tables being joined
2. Verify exact column names and types
3. Don't assume column names from similar tables - column naming can vary
4. Document actual column names in comments if they differ from expected pattern

**Files Applied To:**
- app/Models/RequestATK.php (6 methods updated)

---

### 🔴 OBSTACLE 8: Wrong View Path in Controller

**Problem:**
```php
// ❌ WRONG - Copy-paste from different controller
public function index(): void
{
    // ...
    $this->view('requests/index', [  // WRONG path!
        'requests' => $requests,
    ]);
}
```

**Root Cause:** Copied logic from Request@index (Production Requests) without updating view path; assumed folder naming.

**Solution Applied:**
```php
// ✅ CORRECT - Use module-specific folder naming
public function index(): void
{
    // ...
    $this->view('request_atk/index', [  // CORRECT path!
        'requests' => $requests,
    ]);
}
```

**Prevention Checklist:**
1. For new modules, create dedicated view folder: `app/Views/{module_name}/`
2. Use consistent naming: Model → ModuleName, View folder → module_name
3. Always verify view files exist BEFORE coding controller references
4. Test controller methods immediately after creating to catch path errors

**Files Affected:**
- app/Controllers/RequestATK.php (line 57)

---

### 🔴 OBSTACLE 9: Session Class Access in View Files

**Problem:**
```php
// ❌ WRONG - Cannot access Session class directly in views
<?php if (Session::get('user_role') === 'admin'): ?>
    <a href="...">Admin Link</a>
<?php endif; ?>

// Error: Class "Session" not found in view
```

**Root Cause:** View files don't have access to class namespaces; they need helper functions instead of static class methods.

**Solution Applied:**
```php
// ✅ CORRECT - Use helper function in views
<?php if (session('user_role') === 'admin'): ?>
    <a href="...">Admin Link</a>
<?php endif; ?>
```

**Prevention Checklist:**
1. In Controller methods: use `Session::get()` or `Session::get('key', 'default')`
2. In View files (.php): use `session()` helper function
3. In Models: use `Session` class via namespace if needed
4. Document both patterns clearly in code

**Pattern Comparison:**
```php
// Controller/Model - Direct class access
$userId = Session::get('user_id');

// View - Helper function
<?php echo session('user_name', 'Guest'); ?>
```

**Files Applied To:**
- app/Views/dashboard/index.php (line 92)

---

### 🔴 OBSTACLE 10: Database Enum Migration with Existing Data

**Problem:**
```bash
# ❌ WRONG - Try to change enum directly with incompatible data
ALTER TABLE request_atk MODIFY COLUMN status 
    enum('pending','accepted','rejected','completed');

# Error: Data truncated for column 'status' at row 1
# (old data has 'approved' which is not in new enum)
```

**Root Cause:** Old data in column used `'approved'` status; new enum doesn't include it; database rejects modification.

**Solution Applied:**
```bash
# ✅ CORRECT - Three-step migration process

# Step 1: Expand enum to include BOTH old and new values
ALTER TABLE request_atk MODIFY COLUMN status 
    enum('pending','approved','accepted','rejected','completed');

# Step 2: Migrate old data to new value
UPDATE request_atk SET status = 'accepted' WHERE status = 'approved';
UPDATE request_atk_history SET status = 'accepted' WHERE status = 'approved';

# Step 3: Shrink enum to only new values
ALTER TABLE request_atk MODIFY COLUMN status 
    enum('pending','accepted','rejected','completed');
```

**Prevention Checklist:**
1. When changing enum values, plan 3-step process
2. Expand enum first to include old AND new values
3. Migrate data in second step
4. Remove old values in final step
5. Verify data migrated correctly between each step

**Commands to Verify:**
```bash
# Check status values before migration
SELECT DISTINCT status FROM request_atk;

# After migration
SELECT DISTINCT status FROM request_atk;  # Should show only new values
```

**Files Applied To:**
- database/migrations (request_atk and request_atk_history tables)

---

### 🔴 OBSTACLE 11: Role-Based View Separation with Identical UI

**Problem:**
```
Admin and PIC both need to view request list
UI is 95% identical (only data differs)
But need different URLs: /requests/atk vs /admin/requests/atk
Question: How to prevent cross-access and direct users to correct URL?
```

**Root Cause:** Two separate routes/controllers for same feature creates confusion; users don't know which URL to use.

**Solution Applied - Two-Layer Protection:**

Layer 1 - **Controller Redirect:**
```php
// ✅ In RequestATK@show() method
if (session('user_role') === 'admin') {
    $this->redirect(url("admin/requests/atk/{$id}"));  // Force redirect
}
```

Layer 2 - **View Conditional Links:**
```php
// ✅ In dashboard/index.php
<?php if (session('user_role') === 'admin'): ?>
    <a href="<?php echo url('admin/requests/atk'); ?>">View Requests</a>
<?php else: ?>
    <a href="<?php echo url('requests/atk'); ?>">View Requests</a>
<?php endif; ?>
```

**Benefits:**
- Admin trying `/requests/atk` gets redirected automatically
- UI cards show correct link based on role
- Users never see wrong URL in navigation
- Prevents accidental access to wrong view

**Prevention Checklist:**
1. In Admin views: Show admin URL only when user_role === 'admin'
2. In Controller: Redirect admin users from PIC routes to admin routes
3. In Dashboard: Conditional buttons based on user role
4. Test both roles: login as admin, verify links point to admin URLs

**Files Applied To:**
- app/Controllers/RequestATK.php (create and show methods)
- app/Views/dashboard/index.php (conditional buttons)

---

### 🔴 OBSTACLE 12: HTML Structure Corruption in Conditional Replacements

**Problem:**
```php
// ❌ WRONG - Orphaned closing tags after replacement
<?php endif; ?>
    New Request              <!-- Duplicate text -->
</a>                         <!-- Orphaned closing tag -->
```

**Root Cause:** When adding conditional blocks, replaced text didn't capture complete HTML structure; old closing tags remained orphaned.

**Solution Applied:**
```php
// ✅ CORRECT - Verify complete structure in replacement
<?php if (session('user_role') === 'admin'): ?>
    <a href="...">View Requests</a>
<?php else: ?>
    <a href="...">View Requests</a>
    <a href="...">New Request</a>  <!-- Second button for PIC only -->
<?php endif; ?>
</div>  <!-- Complete structure with closing container -->
```

**Prevention Checklist:**
1. When replacing with conditionals, capture the ENTIRE block including all closing tags
2. After replacement, verify HTML structure visually
3. Test in browser to ensure buttons/elements render correctly
4. Run `php -l` to catch syntax errors
5. Check for orphaned HTML tags in source

**Files Applied To:**
- app/Views/dashboard/index.php

---

### 🔴 OBSTACLE 13: Status Workflow Implementation

**Problem:**
```
Linear workflow needed:
pending → approved → completed
But how to handle:
- Multiple status transitions?
- Different options based on current status?
- Preventing invalid transitions?
```

**Root Cause:** Initial implementation only had Approve/Reject buttons; needed more complex workflow with intermediate states.

**Solution Applied - Workflow Matrix:**

```php
// ✅ CORRECT - Status transition logic by current status
if ($currentStatus === 'pending') {
    $validNextStatuses = ['accepted', 'rejected'];
    // Show options: Accept, Reject
} elseif ($currentStatus === 'accepted') {
    $validNextStatuses = ['completed', 'rejected'];
    // Show options: Close, Reject
} else {
    $validNextStatuses = [];
    // No options: Locked (completed/rejected)
}
```

**View Implementation:**
```php
<!-- ✅ CORRECT - Dynamic dropdown based on status -->
<?php if ($request->status === 'pending' || $request->status === 'accepted'): ?>
    <select name="status">
        <?php if ($request->status === 'pending'): ?>
            <option value="accepted">Accept</option>
            <option value="rejected">Reject</option>
        <?php elseif ($request->status === 'accepted'): ?>
            <option value="completed">Close</option>
            <option value="rejected">Reject</option>
        <?php endif; ?>
    </select>
<?php endif; ?>
```

**Database Status Enum:**
```sql
-- ✅ CORRECT - All possible statuses in enum
ALTER TABLE request_atk MODIFY COLUMN status 
    enum('pending','accepted','rejected','completed');
```

**Status Labels Mapping:**
```php
// ✅ CORRECT - Display friendly labels
$statusLabels = [
    'pending' => 'Pending',
    'accepted' => 'Accepted',
    'rejected' => 'Rejected',
    'completed' => 'Closed',  // Display as "Closed" not "Completed"
];
```

**Prevention Checklist:**
1. Define complete workflow matrix BEFORE implementation
2. Map all valid transitions with conditions
3. Update database enum with ALL possible status values
4. Create mapping arrays for friendly label display
5. Lock form when no valid transitions exist
6. Document workflow in comments for future maintainers

**Workflow Documentation:**
```
PENDING → (Accept) → ACCEPTED → (Close) → COMPLETED
        ↘ (Reject) ↗             ↘ (Reject)
REJECTED (Terminal state - no further transitions)
```

**Files Applied To:**
- app/Models/RequestATK.php
- app/Controllers/RequestATK.php
- app/Views/admin/request_atk/admin_show.php
- app/Views/request_atk/index.php, show.php
- app/Views/admin/request_atk/admin_index.php

---

### 🔴 OBSTACLE 14: Incorrect CSRF Implementation Methods

**Problem:**
```php
// ❌ WRONG - Using non-existent validateCSRF() method
if (!$this->validateCSRF()) {
    $this->redirect('/path', 'error', 'Invalid request');
}

// ❌ WRONG - Using non-existent csrf_field() helper function
<?php echo csrf_field(); ?>
```

**Root Cause:** Implemented CSRF validation based on generic framework patterns without checking the actual project's Session-based implementation. Assumed validateCSRF() method exists in base Controller and csrf_field() helper exists in functions.php, but neither exist in this project.

**Solution Applied in Controller:**
```php
// ✅ CORRECT - Use Session::verifyToken() for CSRF validation
$csrfToken = $this->input('_csrf_token');
if (!Session::verifyToken($csrfToken)) {
    Session::flash('error', 'Security token expired. Please try again.');
    $this->redirect(url('/path'));
}
```

**Solution Applied in Views:**
```php
<!-- ✅ CORRECT - Use hidden input with csrfToken() helper -->
<form method="POST" action="<?php echo url('path/to/action'); ?>">
    <input type="hidden" name="_csrf_token" value="<?php echo csrfToken(); ?>">
    
    <!-- Form fields here -->
    <button type="submit">Submit</button>
</form>
```

**CSRF Pattern Summary:**

| Location | Method | Example |
|----------|--------|---------|
| Controller - Get Token | `$this->input('_csrf_token')` | `$csrfToken = $this->input('_csrf_token');` |
| Controller - Validate | `Session::verifyToken($token)` | `if (!Session::verifyToken($csrfToken))` |
| Controller - Error Flash | `Session::flash('error', msg)` | `Session::flash('error', 'Token expired');` |
| View - Token Field Name | `_csrf_token` (hidden input name) | `name="_csrf_token"` |
| View - Get Token Value | `csrfToken()` helper function | `value="<?php echo csrfToken(); ?>"` |

**Prevention Checklist:**
1. For CSRF token validation in controllers: Use `Session::verifyToken()` NOT `validateCSRF()`
2. For CSRF token in forms: Use direct HTML `<input type="hidden">` with `csrfToken()` helper, NOT `csrf_field()`
3. Always check `app/Session.php` for session methods available
4. Always check `helpers/functions.php` for available helper functions before using custom ones
5. Document CSRF pattern clearly when creating new modules
6. Test form submission after implementing CSRF to verify token is being sent correctly

**Locations to Check for Available Methods/Functions:**
- Base Controller methods: Read `app/Controller.php`
- Session methods: Read `app/Session.php` 
- Helper functions: Read `helpers/functions.php`
- Actual CSRF pattern: Search other controllers with `grep "Session::verifyToken"`

**Working Example from Existing Code:**
```php
// From app/Controllers/MasterATK.php
$csrfToken = $this->input('_csrf_token');
if (!Session::verifyToken($csrfToken)) {
    Session::flash('error', 'Security token expired. Please try again.');
    $this->redirect(url('/admin/master/atk'));
}
```

**Files Applied To:**
- app/Controllers/RequestChecksheet.php (store() and updateStatus() methods)
- app/Views/request_checksheet/create.php
- app/Views/admin/request_checksheet/admin_show.php

---

## Modules Completed Using This Pattern

1. ✅ **User Management** (6 routes, 2 views, 1 controller, 1 model)
2. ✅ **Master ATK** (7 routes, 3 views, 1 controller, 1 model, database table)
3. ✅ **Master Checksheet** (7 routes, 3 views, 1 controller, 1 model, database table)
4. ✅ **Request ATK** (7+3 routes, 5 views, 1 controller, 1 model, 2 database tables, auto-generated request numbers, status workflow)

All modules implemented **without errors** following these patterns and solutions.

---

## For Next Implementation Prompts

**Pass this message to prevent obstacles:**

> Implementasikan [NEW MODULE] dengan memperhatikan semua obstacle-obstacle berikut agar tidak terulang lagi:
> 1. SEMUA Model methods harus `static` dengan parameter `mixed $id` pada update/delete
> 2. Controller setTitle() HARUS return `self` dengan `return $this;`
> 3. SEMUA views dimulai dengan HTML langsung (tidak ada `<?php ?>` kosong di awal)
> 4. TIDAK BOLEH ada `$this->layout()` di dalam views - Controller handle semuanya
> 5. Verify routes di web.php SEBELUM add route baru - jangan ada duplikasi
> 6. Database migration HARUS di-execute langsung (tidak cukup hanya buat file SQL) dan verify dengan DESCRIBE
> 7. Navbar integration: tambah link di main nav DAN dropdown menu
> 8. Gunakan template pattern dari OBSTACLES_AND_SOLUTIONS.md
> 9. CSRF validation: Gunakan `Session::verifyToken()` di controller, gunakan `<input type="hidden" name="_csrf_token" value="<?php echo csrfToken(); ?>">` di view - JANGAN gunakan validateCSRF() atau csrf_field()
