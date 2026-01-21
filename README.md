# Production Request Management System

A modern, native PHP MVC application built with OOP principles. Compatible with PHP 7.4 and 8.2+.

## Features

- **MVC Architecture** - Clean separation of concerns
- **OOP Principles** - Fully object-oriented with namespaces (PSR-4)
- **Database** - MySQL with PDO prepared statements
- **Security** - CSRF protection, XSS prevention, password hashing
- **Frontend** - TailwindCSS + Alpine.js
- **Clean URLs** - SEO-friendly routing via .htaccess
- **No External Frameworks** - Pure PHP foundation

## Technology Stack

- **Backend**: PHP 7.4+ / 8.2+
- **Database**: MySQL 5.7+
- **Frontend**: TailwindCSS, Alpine.js
- **Autoloading**: PSR-4 Autoloader
- **Architecture**: MVC Pattern

## Project Structure

```
production-request-management/
├── app/
│   ├── Controllers/        # Application controllers
│   ├── Models/            # Database models
│   ├── Views/             # View templates
│   │   ├── layouts/       # Layout templates
│   │   ├── auth/          # Authentication views
│   │   └── dashboard/     # Dashboard views
│   ├── Middleware/        # Middleware classes
│   ├── Controller.php     # Base controller class
│   ├── Model.php          # Base model class
│   ├── Database.php       # Database connection manager
│   ├── Router.php         # Router class
│   ├── Route.php          # Route class
│   ├── Session.php        # Session manager
│   └── Security.php       # Security utilities
├── config/
│   ├── app.php           # Application configuration
│   └── database.php      # Database configuration
├── public/
│   ├── index.php         # Application entry point
│   ├── .htaccess         # Clean URL configuration
│   ├── css/
│   │   └── style.css     # Custom styles
│   ├── js/
│   │   └── app.js        # Application JavaScript
│   └── assets/           # Static assets
├── routes/
│   └── web.php           # Route definitions
├── helpers/
│   └── functions.php     # Global helper functions
├── database/
│   └── migrations/       # Database migration files
├── Autoloader.php        # PSR-4 Autoloader
├── composer.json         # Composer configuration
├── .env.example          # Environment variables example
├── .htaccess             # Root .htaccess
└── README.md             # This file
```

## Installation

### Prerequisites

- PHP 7.4 or higher (8.0+ recommended)
- MySQL 5.7 or higher
- Apache with `mod_rewrite` enabled
- Composer (optional, for future dependencies)

### Setup Steps

1. **Clone/Download the project**
   ```bash
   cd c:\laragon\www\production-request-management
   ```

2. **Create environment file**
   ```bash
   copy .env.example .env
   ```

3. **Edit .env file with your database credentials**
   ```
   DB_HOST=localhost
   DB_PORT=3306
   DB_NAME=production_request_db
   DB_USER=root
   DB_PASSWORD=
   ```

4. **Create the database**
   ```bash
   # Using MySQL command line
   mysql -u root
   > CREATE DATABASE production_request_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   > EXIT;
   ```

5. **Import the initial schema**

   **Windows - Command Prompt:**
   ```cmd
   mysql -u root production_request_db < database/migrations/001_initial_schema.sql
   ```

   **Windows - PowerShell:**
   ```powershell
   Get-Content database/migrations/001_initial_schema.sql | mysql -u root production_request_db
   ```

   Or use the helper script:
   ```powershell
   .\import-schema.ps1
   ```

   **Windows - Batch File:**
   ```cmd
   import-schema.bat
   ```

   **Mac/Linux:**
   ```bash
   mysql -u root production_request_db < database/migrations/001_initial_schema.sql
   ```

6. **Configure Virtual Host (if using local server)**
   - Set document root to `production-request-management/public`
   - Or access via http://localhost/production-request-management/public

7. **Start your local server** (Laragon, XAMPP, etc.)

## Usage

### Accessing the Application

- **Home**: http://localhost/production-request-management/public/
- **Login**: http://localhost/production-request-management/public/login
- **Dashboard**: http://localhost/production-request-management/public/dashboard

### Demo Credentials

```
Email: admin@example.com
Password: admin123

Email: manager@example.com
Password: manager123

Email: user@example.com
Password: user123
```

## Architecture

### Controllers

All controllers extend the `App\Controller` base class which provides:
- View rendering
- Input validation
- Redirects
- JSON responses
- CSRF protection

```php
namespace App\Controllers;

use App\Controller;

class Dashboard extends Controller {
    public function index(): void {
        $this->setTitle('Dashboard');
        $this->view('dashboard/index');
    }
}
```

### Models

All models extend the `App\Model` base class which provides:
- CRUD operations
- Database queries
- Pagination
- Relationships

```php
namespace App\Models;

use App\Model;

class User extends Model {
    protected string $table = 'users';
    protected array $fillable = ['name', 'email', 'password'];
}
```

### Routes

Routes are defined in `routes/web.php`:

```php
$router->get('/dashboard', 'Dashboard@index');
$router->post('/requests', 'Request@store');
$router->get('/requests/{id}', 'Request@show');
$router->delete('/requests/{id}', 'Request@delete');
```

### Autoloading

The PSR-4 autoloader automatically loads classes based on namespaces:

```php
// File: app/Controllers/Home.php
namespace App\Controllers;

class Home {
    // Automatically loaded from app/Controllers/Home.php
}
```

## Security Features

### 1. CSRF Protection
- Tokens generated via `Session::generateToken()`
- Verified via `Session::verifyToken($token)`
- Available in views via `csrfToken()` helper

### 2. XSS Prevention
- Output escaping via `e()` helper or `Security::escape()`
- Input sanitization via `Security::sanitize()`

### 3. SQL Injection Prevention
- PDO prepared statements used throughout
- No raw SQL queries with user input

### 4. Password Security
- Bcrypt hashing via `Security::hashPassword()`
- Verification via `Security::verifyPassword()`
- Cost factor: 12

### 5. Session Management
- Timeout-based session expiration (1 hour default)
- Session regeneration on login
- Secure cookie settings

## Database Schema

### Users Table
```sql
CREATE TABLE users (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    role ENUM('user', 'manager', 'admin'),
    is_active TINYINT(1),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Production Requests Table
```sql
CREATE TABLE production_requests (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT,
    title VARCHAR(255),
    description LONGTEXT,
    status ENUM('pending', 'in_progress', 'completed'),
    priority ENUM('low', 'medium', 'high', 'urgent'),
    assigned_to BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

## Helper Functions

### Global Functions

- `env($key, $default)` - Get environment variable
- `url($path)` - Generate URL
- `e($value)` - Escape HTML
- `csrfToken()` - Get CSRF token
- `session($key)` - Get session data
- `config($key)` - Get config value
- `hashPassword($password)` - Hash password
- `verifyPassword($password, $hash)` - Verify password
- `dd($var)` - Dump and die
- `dump($var)` - Dump variable
- `formatDate($date)` - Format date

## API Endpoints

### Example API Routes

```php
GET  /api/requests           # Get all requests
POST /api/requests           # Create request
GET  /api/requests/{id}      # Get single request
PUT  /api/requests/{id}      # Update request
DELETE /api/requests/{id}    # Delete request
```

## Development

### Adding a New Controller

1. Create file: `app/Controllers/MyController.php`
2. Extend `App\Controller` class
3. Add route in `routes/web.php`

### Adding a New Model

1. Create file: `app/Models/MyModel.php`
2. Extend `App\Model` class
3. Set `$table` property
4. Define `$fillable` array

### Adding a New View

1. Create file: `app/Views/path/view.php`
2. Use `$this->view('path/view')` in controller
3. Access data via `$variable`

## PHP Version Compatibility

This application is fully compatible with:
- PHP 7.4
- PHP 8.0
- PHP 8.1
- PHP 8.2+

Key compatibility features:
- No typed properties (works with 7.4)
- No union types (works with 7.4)
- No match expressions (works with 7.4)
- Property promotion (PHP 8.0+) - not used

## Best Practices

1. **Always validate input** - Use `$this->validate()` in controllers
2. **Escape output** - Use `e()` helper in views
3. **Use prepared statements** - Handled by Model class
4. **Check user authentication** - Use `Session::has('user_id')`
5. **Use CSRF tokens** - Required for POST/PUT/DELETE
6. **Hash passwords** - Use `Security::hashPassword()`
7. **Follow naming conventions** - Controllers/Models in PascalCase
8. **Use meaningful route names** - RESTful patterns

## Troubleshooting

### 404 Errors
- Check `.htaccess` is enabled (mod_rewrite)
- Verify route definitions in `routes/web.php`
- Check controller and method names match routes

### Database Connection Failed
- Verify MySQL is running
- Check `.env` database credentials
- Ensure database exists

### CSRF Token Errors
- Session must be started: `Session::start()`
- Token must be in form: `<input name="_csrf_token" value="<?php echo csrfToken(); ?>">`

### Views Not Found
- Check view file path matches view name
- Ensure file has `.php` extension
- Verify view file is in `app/Views/` directory

## License

MIT License - See LICENSE file for details

## Support

For issues and feature requests, please create an issue in the repository.

## Future Enhancements

- [ ] Email notifications
- [ ] File upload handling
- [ ] API authentication (JWT)
- [ ] Advanced search and filtering
- [ ] Audit logging
- [ ] Multi-language support
- [ ] Dark mode
- [ ] Mobile app
- [ ] Caching layer
- [ ] Queue system
