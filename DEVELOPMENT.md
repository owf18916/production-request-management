# Development Guide

## Project Overview

Production Request Management System is a native PHP MVC application built with Object-Oriented principles. It provides a foundation for managing production requests with a focus on clean code architecture and security.

## Key Principles

### 1. MVC Architecture

**Model** - Database and business logic
```php
// app/Models/User.php
class User extends Model {
    protected string $table = 'users';
    public static function findByEmail(string $email): ?object {
        return self::findBy('email', $email);
    }
}
```

**View** - User interface templates
```php
// app/Views/dashboard/index.php
<h1><?php echo e($title); ?></h1>
```

**Controller** - Request handling and coordination
```php
// app/Controllers/Dashboard.php
class Dashboard extends Controller {
    public function index(): void {
        $this->view('dashboard/index');
    }
}
```

### 2. OOP Principles

- **Encapsulation**: Private/protected properties and methods
- **Inheritance**: Base classes (Controller, Model) extended by specific classes
- **Polymorphism**: Method overriding in subclasses
- **Abstraction**: Complex logic hidden behind simple interfaces

### 3. PSR Standards

- **PSR-4 Autoloading**: Namespaces map to directory structure
- **PSR-12 Code Style**: Followed throughout the codebase

## File Structure Guide

### Controllers

Location: `app/Controllers/`

```php
<?php
namespace App\Controllers;

use App\Controller;
use App\Session;

class Dashboard extends Controller {
    public function index(): void {
        // Check authentication
        if (!Session::has('user_id')) {
            $this->redirect(url('login'));
        }

        // Set title and pass data
        $this->setTitle('Dashboard')
             ->with('user_name', Session::get('user_name'));

        // Render view
        $this->view('dashboard/index');
    }
}
```

**Base Controller Methods:**
- `view($path, $data)` - Render view
- `json($data, $status)` - JSON response
- `redirect($url)` - Redirect user
- `validate($rules)` - Validate input
- `input($key)` - Get form input
- `with($key, $value)` - Pass data to view
- `method()` - Get request method

### Models

Location: `app/Models/`

```php
<?php
namespace App\Models;

use App\Model;

class User extends Model {
    protected string $table = 'users';
    protected string $primaryKey = 'id';
    
    protected array $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];
    
    protected array $hidden = [
        'password',
    ];

    // Custom methods
    public static function findByEmail(string $email): ?object {
        return self::findBy('email', $email);
    }

    public static function getActive(): array {
        $sql = "SELECT * FROM users WHERE is_active = 1";
        return \App\Database::results($sql);
    }
}
```

**Base Model Methods:**
- `find($id)` - Find by primary key
- `all()` - Get all records
- `create($data)` - Create record
- `update($id, $data)` - Update record
- `delete($id)` - Delete record
- `query($sql, $params)` - Execute custom query
- `paginate($perPage, $page)` - Paginate results
- `count()` - Count records

### Views

Location: `app/Views/`

```php
<?php
$content = ob_get_clean();
ob_start();
?>

<!-- View content here -->
<h1><?php echo e($title); ?></h1>

<?php
$content = ob_get_clean();
$data['content'] = $content;
extract($data);
require __DIR__ . '/../layouts/main.php';
?>
```

**Template Helpers:**
- `e($value)` - Escape HTML
- `url($path)` - Generate URL
- `csrfToken()` - Get CSRF token
- `session($key)` - Get session data
- `getFlash($key)` - Get flash message

### Routes

Location: `routes/web.php`

```php
<?php

// HTTP Methods
$router->get('/path', 'Controller@method');
$router->post('/path', 'Controller@method');
$router->put('/path', 'Controller@method');
$router->patch('/path', 'Controller@method');
$router->delete('/path', 'Controller@method');

// URL Parameters
$router->get('/users/{id}', 'User@show');
$router->get('/posts/{id}/comments/{comment_id}', 'Comment@show');

// Middleware (future)
$router->get('/dashboard', 'Dashboard@index')->middleware(['auth']);
```

## Database Operations

### Using Models

```php
// Create
User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => hashPassword('secret'),
    'role' => 'user',
]);

// Read
$user = User::find(1);
$user = User::findBy('email', 'john@example.com');
$users = User::all();
$users = User::paginate(15, 1);

// Update
User::update(1, [
    'name' => 'Jane Doe',
    'role' => 'admin',
]);

// Delete
User::delete(1);

// Count
$total = User::count();
```

### Custom Queries

```php
// Using Database class
$users = Database::results("SELECT * FROM users WHERE role = ?", ['admin']);
$user = Database::row("SELECT * FROM users WHERE id = ?", [1]);

// Using Model custom methods
class User extends Model {
    public static function getByRole(string $role): array {
        $sql = "SELECT * FROM users WHERE role = ?";
        return Database::query($sql, [$role])->fetchAll(PDO::FETCH_OBJ);
    }
}
```

## Form Handling

### Create a Form

```php
<!-- app/Views/users/create.php -->
<form method="POST" action="<?php echo url('users'); ?>" class="space-y-4">
    <input type="hidden" name="_csrf_token" value="<?php echo e(csrfToken()); ?>">
    
    <div>
        <label>Name</label>
        <input type="text" name="name" required>
    </div>
    
    <div>
        <label>Email</label>
        <input type="email" name="email" required>
    </div>
    
    <button type="submit">Create</button>
</form>
```

### Handle Form Submission

```php
class User extends Controller {
    public function store(): void {
        if ($this->method() !== 'POST') {
            $this->redirect(url('users/create'));
        }

        // Verify CSRF token
        if (!Session::verifyToken($this->input('_csrf_token'))) {
            Session::flash('error', 'CSRF token invalid');
            $this->redirect(url('users/create'));
        }

        // Validate input
        $errors = $this->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (!empty($errors)) {
            Session::flash('error', 'Please correct the errors');
            $this->redirect(url('users/create'));
        }

        // Create user
        User::create([
            'name' => $this->input('name'),
            'email' => $this->input('email'),
            'password' => hashPassword($this->input('password')),
        ]);

        Session::flash('success', 'User created successfully');
        $this->redirect(url('users'));
    }
}
```

## Authentication

### Login

```php
$email = $this->input('email');
$password = $this->input('password');

$user = User::findByEmail($email);

if ($user && verifyPassword($password, $user->password)) {
    Session::put('user_id', $user->id);
    Session::put('user_name', $user->name);
    Session::put('user_role', $user->role);
    
    $this->redirect(url('dashboard'));
}
```

### Check Authentication

```php
if (!Session::has('user_id')) {
    $this->redirect(url('login'));
}

$userId = Session::get('user_id');
$userRole = Session::get('user_role');
```

### Logout

```php
Session::destroy();
$this->redirect(url('/'));
```

## Input Validation

### Built-in Rules

```php
$this->validate([
    'name' => 'required',
    'email' => 'required|email',
    'age' => 'required|numeric|min:18|max:100',
    'password' => 'required|min:6',
    'password_confirmation' => 'required|confirmed',
]);
```

**Available Rules:**
- `required` - Field is required
- `email` - Valid email format
- `numeric` - Numeric value
- `min:length` - Minimum length
- `max:length` - Maximum length
- `confirmed` - Matches field_confirmation

### Custom Validation

```php
private function validateCustom(array $rules): array {
    $errors = [];
    
    foreach ($rules as $field => $checks) {
        // Custom validation logic
    }
    
    return $errors;
}
```

## Security Best Practices

### 1. CSRF Protection

Always include CSRF token in forms:
```php
<input type="hidden" name="_csrf_token" value="<?php echo csrfToken(); ?>">
```

Verify in controller:
```php
if (!Session::verifyToken($this->input('_csrf_token'))) {
    // Invalid token
}
```

### 2. XSS Prevention

Always escape output:
```php
<?php echo e($userInput); ?>
<!-- or -->
<?php echo Security::escape($userInput); ?>
```

### 3. SQL Injection Prevention

Always use prepared statements:
```php
// Good
$users = Database::results("SELECT * FROM users WHERE email = ?", [$email]);

// Bad (avoid)
$users = Database::results("SELECT * FROM users WHERE email = '$email'");
```

### 4. Password Security

```php
// Hash on create
$hashedPassword = hashPassword($rawPassword);

// Verify on login
if (verifyPassword($rawPassword, $hashedPassword)) {
    // Valid
}
```

### 5. Session Security

```php
// Start session
Session::start();

// Store data
Session::put('user_id', $userId);

// Retrieve data
$userId = Session::get('user_id');

// Clear sensitive data
Session::forget('password');

// Destroy on logout
Session::destroy();
```

## Helper Functions

### Global Helpers

```php
// Environment
env('DB_HOST', 'localhost')

// URLs
url('/dashboard')
currentUrl()

// Output
e($value)  // Escape HTML

// Sessions
session('user_id')
csrfToken()
getFlash('success')

// Security
hashPassword($password)
verifyPassword($password, $hash)

// Configuration
config('app.name')
config('database.mysql.host')

// Debugging
dd($variable)  // Dump and die
dump($variable)  // Dump

// Utilities
isEmpty($value)
formatDate($date)
arrayGet($array, 'key.nested')
```

## Creating a New Feature

### Step 1: Create Database Table

```sql
CREATE TABLE products (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description LONGTEXT,
    price DECIMAL(10, 2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Step 2: Create Model

```php
<?php
namespace App\Models;

use App\Model;

class Product extends Model {
    protected string $table = 'products';
    protected array $fillable = ['name', 'description', 'price'];
}
```

### Step 3: Create Controller

```php
<?php
namespace App\Controllers;

use App\Controller;
use App\Models\Product;

class Products extends Controller {
    public function index(): void {
        $products = Product::paginate(15, $_GET['page'] ?? 1);
        $this->setTitle('Products')
             ->with('products', $products)
             ->view('products/index');
    }

    public function show(int $id): void {
        $product = Product::find($id);
        if (!$product) {
            http_response_code(404);
            die('Product not found');
        }
        $this->setTitle('Product')
             ->with('product', $product)
             ->view('products/show');
    }
}
```

### Step 4: Create Views

```php
<!-- app/Views/products/index.php -->
<div class="products">
    <?php foreach ($products['data'] as $product): ?>
        <div class="product">
            <h2><?php echo e($product->name); ?></h2>
            <p><?php echo e($product->description); ?></p>
            <p>$<?php echo e($product->price); ?></p>
        </div>
    <?php endforeach; ?>
</div>
```

### Step 5: Create Routes

```php
$router->get('/products', 'Products@index');
$router->get('/products/{id}', 'Products@show');
```

## Testing

### Manual Testing

1. Use browser to navigate application
2. Test all user flows
3. Check database for data integrity
4. Verify error handling

### Automated Testing (Future)

When PHPUnit is added:
```bash
composer test
```

## Performance Tips

1. **Cache database results** for frequently accessed data
2. **Use pagination** for large datasets
3. **Index database columns** used in WHERE clauses
4. **Minimize database queries** - use joins instead of N+1
5. **Use lazy loading** for relationships
6. **Compress CSS/JS** for production
7. **Cache static assets** with proper headers

## Deployment

### Production Checklist

- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Update `APP_URL` to production domain
- [ ] Configure production database
- [ ] Ensure HTTPS is enabled
- [ ] Set secure session cookies in `php.ini`
- [ ] Restrict file permissions (755, 644)
- [ ] Enable all security headers
- [ ] Set up error logging (not displayed to users)
- [ ] Configure automated backups
- [ ] Set up monitoring and alerts

## Useful Resources

- [PHP PSR Standards](https://www.php-fig.org/)
- [OWASP Security Guidelines](https://owasp.org/)
- [RESTful API Design](https://restfulapi.net/)
- [TailwindCSS Documentation](https://tailwindcss.com/docs)
- [Alpine.js Documentation](https://alpinejs.dev/)

## Contributing

Follow these guidelines when contributing:

1. **Code Style** - Follow PSR-12
2. **Naming** - Use clear, descriptive names
3. **Comments** - Add comments for complex logic
4. **Security** - Always validate and sanitize input
5. **Testing** - Test all new features
6. **Documentation** - Update docs when adding features

## Troubleshooting

### Common Issues

1. **Routes not working** - Check .htaccess mod_rewrite
2. **Database errors** - Verify connection credentials
3. **Session not persisting** - Check session.save_path
4. **Files not found** - Check file paths and permissions
5. **CSRF token errors** - Ensure sessions are enabled
