# Installation Guide

## System Requirements

- **PHP**: 7.4 or higher (8.2+ recommended)
- **MySQL**: 5.7 or higher
- **Apache**: with `mod_rewrite` enabled
- **Composer**: Optional (for dependency management)

## Step-by-Step Installation

### 1. Environment Setup

The project is located at: `C:\laragon\www\production-request-management`

If you're using Laragon, it should automatically be accessible.

### 2. Configure Environment Variables

Copy the example environment file:
```bash
copy .env.example .env
```

Edit `.env` with your database credentials:
```env
APP_NAME="Production Request Management System"
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost/production-request-management

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_NAME=production_request_db
DB_USER=root
DB_PASSWORD=
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
```

### 3. Create MySQL Database

Using MySQL CLI:
```bash
mysql -u root -p
```

In the MySQL prompt:
```sql
CREATE DATABASE production_request_db 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

EXIT;
```

Or using a MySQL GUI (phpMyAdmin, MySQL Workbench, etc.):
1. Create a new database named `production_request_db`
2. Set charset to `utf8mb4`
3. Set collation to `utf8mb4_unicode_ci`

### 4. Import Initial Schema

Run the migration file to create tables and insert sample data:

#### Option A: Command Prompt (Recommended for Windows)

Use Command Prompt (cmd.exe) instead of PowerShell:

```cmd
mysql -u root -p production_request_db < database/migrations/001_initial_schema.sql
```

#### Option B: PowerShell

If using PowerShell, use one of these alternatives:

**Method 1: Using Get-Content pipe**
```powershell
Get-Content database/migrations/001_initial_schema.sql | mysql -u root -p production_request_db
```

**Method 2: Using type command (with cd to project root)**
```powershell
cd C:\laragon\www\production-request-management
type database/migrations/001_initial_schema.sql | mysql -u root -p production_request_db
```

**Method 3: Using cmd.exe directly**
```powershell
cmd /c "mysql -u root -p production_request_db < database/migrations/001_initial_schema.sql"
```

#### Option C: Using phpMyAdmin or MySQL Workbench

1. Open phpMyAdmin (usually at http://localhost/phpmyadmin)
2. Select database `production_request_db`
3. Click "Import" tab
4. Upload file: `database/migrations/001_initial_schema.sql`
5. Click "Go" to import

#### What Gets Created:

This will create:
- `users` table with sample admin/manager/user accounts
- `production_requests` table
- `request_comments` table
- `request_attachments` table
- `audit_logs` table
- `activity_logs` table
- `password_reset_tokens` table

### 5. Verify Apache Configuration

**For Laragon:**
1. Right-click on Laragon in system tray
2. Select "Web" → Check Apache is running
3. Make sure `mod_rewrite` is enabled

**For XAMPP/WAMP:**
1. Ensure Apache is running
2. Enable `mod_rewrite` in Apache configuration
3. Point virtual host to: `C:\laragon\www\production-request-management\public`

### 6. Access the Application

Start your local server and navigate to:

- **Home Page**: http://localhost/production-request-management/public/
- **Login Page**: http://localhost/production-request-management/public/login
- **Dashboard**: http://localhost/production-request-management/public/dashboard

### 7. Demo Credentials

Use these credentials to test the application:

**Admin Account**
- Email: `admin@example.com`
- Password: `admin123`
- Role: Administrator

**Manager Account**
- Email: `manager@example.com`
- Password: `manager123`
- Role: Manager

**User Account**
- Email: `user@example.com`
- Password: `user123`
- Role: User

## Windows-Specific Help

### PowerShell File Redirection Error

**Problem:**
```
The '<' operator is reserved for future use.
```

**Solution:**

Use one of these methods:

**1. Use Command Prompt (Easiest)**
- Press `Win + R`
- Type `cmd` and press Enter
- Navigate to project: `cd C:\laragon\www\production-request-management`
- Run: `mysql -u root production_request_db < database/migrations/001_initial_schema.sql`

**2. Use PowerShell Helper Script**
```powershell
# Run PowerShell as Administrator, then:
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
.\import-schema.ps1
```

**3. Use Batch File Helper**
- Double-click: `import-schema.bat`
- Enter MySQL password when prompted

**4. Use phpMyAdmin**
- Open: http://localhost/phpmyadmin
- Select database: `production_request_db`
- Click "Import" tab
- Upload: `database/migrations/001_initial_schema.sql`
- Click "Go"

**5. PowerShell Manual Command**
```powershell
Get-Content database/migrations/001_initial_schema.sql | mysql -u root production_request_db
```

---

## Troubleshooting

### Issue: 404 Not Found

**Solution:**
1. Verify mod_rewrite is enabled
2. Check the `.htaccess` file exists in both root and `public/` folders
3. Ensure virtual host document root points to `public/` folder
4. Restart Apache

### Issue: Database Connection Error

**Solution:**
1. Verify MySQL is running
2. Check `.env` credentials are correct
3. Ensure database `production_request_db` exists
4. Check MySQL user has correct permissions

```bash
mysql -u root -p
GRANT ALL PRIVILEGES ON production_request_db.* TO 'root'@'localhost';
FLUSH PRIVILEGES;
```

### Issue: Permission Denied (file/folder)

**Solution:**
1. Ensure PHP can read/write to these directories:
   - `/storage/` (if it exists)
   - `/public/uploads/` (if needed)
2. Set appropriate permissions:

```bash
chmod -R 755 public/
chmod -R 777 storage/
```

### Issue: Session Not Working

**Solution:**
1. Check PHP session settings in `php.ini`:
```ini
session.save_path = "/path/to/temp"
session.gc_maxlifetime = 3600
```
2. Verify `/tmp` folder exists and is writable
3. Session path might need to be configured in `.env`

### Issue: CSRF Token Mismatch

**Solution:**
1. Sessions must be enabled
2. Ensure forms include the CSRF token field:
```html
<input type="hidden" name="_csrf_token" value="<?php echo csrfToken(); ?>">
```
3. Clear browser cookies and try again

## Optional Configuration

### Configure Custom Domain

Edit your local `hosts` file:

**Windows**: `C:\Windows\System32\drivers\etc\hosts`
```
127.0.0.1  prm.local
```

**Apache Virtual Host** (`httpd-vhosts.conf`):
```apache
<VirtualHost *:80>
    ServerName prm.local
    DocumentRoot "C:\laragon\www\production-request-management\public"
    <Directory "C:\laragon\www\production-request-management\public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Access at: http://prm.local

### Enable Debug Mode

In `.env`:
```env
APP_DEBUG=true
APP_ENV=development
```

This will show detailed error messages. **Never enable in production!**

### Configure Email (Future)

When email functionality is added, configure in `.env`:
```env
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
```

## Security Configuration

### Production Deployment

Before deploying to production:

1. **Set APP_DEBUG to false**
```env
APP_DEBUG=false
APP_ENV=production
```

2. **Configure secure session settings** in `php.ini`:
```ini
session.use_strict_mode = 1
session.cookie_httponly = 1
session.cookie_secure = 1
session.cookie_samesite = Strict
```

3. **Set restrictive file permissions**
```bash
chmod 750 app/
chmod 750 config/
chmod 750 database/
chmod 755 public/
```

4. **Disable directory listing**
Already configured in `.htaccess`:
```apache
Options -Indexes
```

5. **Set strong database password**
Update in `.env` and MySQL

6. **Use HTTPS** with valid SSL certificate

7. **Configure CORS** if needed for API access

## Next Steps

1. Review the [README.md](README.md) for architecture overview
2. Check [CONTRIBUTING.md](CONTRIBUTING.md) for development guidelines
3. Explore example controllers in `app/Controllers/`
4. Check example models in `app/Models/`
5. Review views in `app/Views/`

## Support

If you encounter issues:

1. Check the [README.md](README.md) troubleshooting section
2. Review error messages in browser console and PHP logs
3. Check Apache error logs: `error_log`
4. Check MySQL error logs for database issues
5. Enable `APP_DEBUG=true` in `.env` for detailed error messages
