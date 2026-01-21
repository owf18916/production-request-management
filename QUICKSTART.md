# Quick Start Guide

Get up and running with Production Request Management System in 5 minutes!

## 1. Configure Database (1 min)

Edit `.env`:
```env
DB_HOST=localhost
DB_NAME=production_request_db
DB_USER=root
DB_PASSWORD=
```

## 2. Create Database (1 min)

```bash
mysql -u root
> CREATE DATABASE production_request_db CHARACTER SET utf8mb4;
> EXIT;
```

## 3. Import Schema (1 min)

**Windows Command Prompt:**
```cmd
mysql -u root production_request_db < database/migrations/001_initial_schema.sql
```

**Windows PowerShell:**
```powershell
Get-Content database/migrations/001_initial_schema.sql | mysql -u root production_request_db
```

**Mac/Linux:**
```bash
mysql -u root production_request_db < database/migrations/001_initial_schema.sql
```

## 4. Start Server (1 min)

Using Laragon:
1. Right-click Laragon in system tray
2. Click "Start All" or just "Apache"

## 5. Access Application (1 min)

Navigate to: **http://localhost/production-request-management/public/**

## Login

Use any demo account:
- **Email**: admin@example.com
- **Password**: admin123

## 🎉 You're Ready!

The application is now running. Explore:

- **Dashboard**: http://localhost/production-request-management/public/dashboard
- **Requests**: http://localhost/production-request-management/public/requests
- **API**: http://localhost/production-request-management/public/api/requests

## 📚 Next Steps

- Read [README.md](README.md) for full documentation
- Check [DEVELOPMENT.md](DEVELOPMENT.md) to learn the architecture
- Review [INSTALLATION.md](INSTALLATION.md) for detailed setup

## 🔧 Common Tasks

### Create a Controller
```php
// app/Controllers/MyController.php
<?php
namespace App\Controllers;

use App\Controller;

class MyController extends Controller {
    public function index(): void {
        $this->setTitle('My Page');
        $this->view('my/index');
    }
}
```

Add route in `routes/web.php`:
```php
$router->get('/my-page', 'MyController@index');
```

### Create a Model
```php
// app/Models/MyModel.php
<?php
namespace App\Models;

use App\Model;

class MyModel extends Model {
    protected string $table = 'my_table';
    protected array $fillable = ['name', 'email'];
}
```

### Use Database
```php
// Get all records
$records = MyModel::all();

// Find one
$record = MyModel::find(1);

// Create
MyModel::create(['name' => 'John', 'email' => 'john@example.com']);

// Update
MyModel::update(1, ['name' => 'Jane']);

// Delete
MyModel::delete(1);
```

### Handle Forms
```php
if ($this->method() === 'POST') {
    // Validate CSRF
    if (!Session::verifyToken($this->input('_csrf_token'))) {
        $this->redirect(url('back'));
    }

    // Validate input
    $errors = $this->validate([
        'name' => 'required',
        'email' => 'required|email',
    ]);

    if (!empty($errors)) {
        Session::flash('error', 'Please correct errors');
        $this->redirect(url('back'));
    }

    // Process
    MyModel::create($this->all());
    
    Session::flash('success', 'Created successfully');
    $this->redirect(url('success'));
}
```

## 🆘 Troubleshooting

### 404 Errors
- Check mod_rewrite is enabled
- Verify route exists in `routes/web.php`
- Restart Apache

### Database Connection Failed
- Verify MySQL is running
- Check `.env` credentials
- Ensure database exists

### Views Not Found
- Check path matches namespace (e.g., `requests/index` = `app/Views/requests/index.php`)
- Verify file has `.php` extension

## 📖 Quick Reference

### URLs
```php
url('/dashboard')           // Generate URL
url('requests/1')          // With path
currentUrl()               // Current page URL
```

### Output
```php
e($variable)              // Escape HTML (XSS prevention)
csrfToken()              // Get CSRF token
```

### Sessions
```php
Session::put('key', $value);    // Store
Session::get('key');             // Retrieve
Session::has('key');             // Check exists
Session::forget('key');          // Delete
Session::destroy();              // Clear all
```

### Security
```php
hashPassword($password)                    // Hash password
verifyPassword($raw, $hash)               // Verify password
Security::escape($value)                  // Escape HTML
Security::sanitize($input, 'email')       // Sanitize input
Session::verifyToken($token)              // Check CSRF token
```

### Responses
```php
$this->view('path/view', $data);          // Render view
$this->json(['status' => 'ok'], 200);     // JSON response
$this->redirect(url('path'));              // Redirect
```

### Input
```php
$this->input('field')       // Get single input
$this->all()               // Get all input
$this->has('field')        // Check exists
$this->method()            // GET, POST, etc.
```

### Validation
```php
$errors = $this->validate([
    'email' => 'required|email',
    'name' => 'required|min:3|max:50',
    'age' => 'numeric|min:18',
]);
```

## 💡 Tips

1. **Always escape output**: Use `e()` to prevent XSS
2. **Always validate input**: Use `$this->validate()`
3. **Always check CSRF**: Use `Session::verifyToken()`
4. **Use models for queries**: Don't write raw SQL
5. **Use flash messages**: For feedback to users
6. **Check authentication**: Before accessing user data
7. **Use namespaces**: Follow PSR-4 standard

## 📞 Need Help?

1. Check the main [README.md](README.md)
2. Review [DEVELOPMENT.md](DEVELOPMENT.md) for examples
3. See [INSTALLATION.md](INSTALLATION.md) for setup issues
4. Check error logs in `storage/logs/`

---

**Happy Coding!** 🚀
