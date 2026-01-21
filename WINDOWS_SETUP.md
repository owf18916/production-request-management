# Windows Setup Guide

Production Request Management System - Windows-specific installation guide.

## Quick Setup (Windows)

### Prerequisites

- PHP 7.4+ (included with Laragon)
- MySQL 5.7+ (included with Laragon)
- Laragon or XAMPP installed
- Command Prompt or PowerShell

### Step 1: Configure Environment

Edit `.env` file with your database credentials:

```env
DB_HOST=localhost
DB_NAME=production_request_db
DB_USER=root
DB_PASSWORD=
```

### Step 2: Create Database

**Option A: Using Command Prompt (Recommended)**

1. Open Command Prompt (Press `Win + R`, type `cmd`, Enter)
2. Go to project directory:
   ```cmd
   cd C:\laragon\www\production-request-management
   ```
3. Run:
   ```cmd
   mysql -u root
   ```
4. In MySQL prompt:
   ```sql
   CREATE DATABASE production_request_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   EXIT;
   ```

**Option B: Using Laragon GUI**

1. Right-click Laragon system tray icon
2. Select "MySQL" → "Open MySQL Console"
3. Run the CREATE DATABASE command above

**Option C: Using phpMyAdmin**

1. Open http://localhost/phpmyadmin
2. Click "Databases" tab
3. Create database: `production_request_db`
4. Charset: `utf8mb4`
5. Collation: `utf8mb4_unicode_ci`

### Step 3: Import Database Schema

**Option A: Using Helper Script (Easiest)**

Simply double-click:
```
import-schema.bat
```

This will:
- Prompt for MySQL password
- Import all tables
- Load sample data
- Show success message

**Option B: Using Command Prompt**

1. Open Command Prompt
2. Navigate to project:
   ```cmd
   cd C:\laragon\www\production-request-management
   ```
3. Run:
   ```cmd
   mysql -u root production_request_db < database/migrations/001_initial_schema.sql
   ```

**Option C: Using PowerShell Helper Script**

1. Open PowerShell as Administrator
2. Navigate to project:
   ```powershell
   cd C:\laragon\www\production-request-management
   ```
3. Allow script execution:
   ```powershell
   Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
   ```
4. Run script:
   ```powershell
   .\import-schema.ps1
   ```

**Option D: Using phpMyAdmin**

1. Open http://localhost/phpmyadmin
2. Select database: `production_request_db`
3. Click "Import" tab
4. Browse and select: `database/migrations/001_initial_schema.sql`
5. Click "Go"

### Step 4: Start Server

1. Open Laragon
2. Click "Start All" (or start Apache/MySQL individually)
3. Verify Apache and MySQL are running (green icons)

### Step 5: Access Application

Open browser and navigate to:

```
http://localhost/production-request-management/public/
```

### Step 6: Login

Use demo credentials:

```
Email: admin@example.com
Password: admin123
```

---

## Windows Tips

### Setting Virtual Host (Optional)

Edit: `C:\laragon\etc\apache2\sites-enabled\auto.conf`

Add:
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

Then edit: `C:\Windows\System32\drivers\etc\hosts`

Add:
```
127.0.0.1  prm.local
```

Restart Apache and access: `http://prm.local`

### Command Prompt vs PowerShell

**Use Command Prompt when:**
- Running MySQL commands with file redirection
- Importing database schema
- Running simple commands

**Use PowerShell when:**
- You prefer PowerShell interface
- Use: `Get-Content file | command` instead of `command < file`

### Enable mod_rewrite in Apache

1. Open: `C:\laragon\bin\apache\conf\httpd.conf`
2. Find and uncomment:
   ```
   LoadModule rewrite_module modules/mod_rewrite.so
   ```
3. Restart Apache

### Change MySQL Root Password

```cmd
mysql -u root
ALTER USER 'root'@'localhost' IDENTIFIED BY 'newpassword';
FLUSH PRIVILEGES;
EXIT;
```

Update in `.env`:
```env
DB_PASSWORD=newpassword
```

### Database Backups

**Backup:**
```cmd
mysqldump -u root production_request_db > backup.sql
```

**Restore:**
```cmd
mysql -u root production_request_db < backup.sql
```

### Enable HTTPS (Local Testing)

1. Use Laragon's built-in SSL support
2. Or use tools like mkcert for local SSL certificates

### Debug Mode

To see detailed errors in browser:

Edit `.env`:
```env
APP_DEBUG=true
APP_ENV=development
```

**Never enable in production!**

---

## Troubleshooting

### MySQL Connection Failed

**Problem:** `Error 2002 (HY000): Can't connect to local MySQL server`

**Solutions:**
1. Ensure MySQL service is running (Laragon → Start MySQL)
2. Check port 3306 is not blocked
3. Verify credentials in `.env`
4. Try: `mysql -u root -h 127.0.0.1`

### PowerShell Redirection Error

**Problem:** `The '<' operator is reserved for future use`

**Solutions:**
- Use Command Prompt instead
- Or use: `Get-Content file | mysql -u root production_request_db`
- Or use: `.\import-schema.ps1`

### Apache Mod_rewrite Not Working

**Problem:** 404 errors on clean URLs

**Solutions:**
1. Enable mod_rewrite in `httpd.conf`
2. Enable `.htaccess` override
3. Verify `.htaccess` file exists
4. Restart Apache (important!)

### Permission Denied Errors

**Problem:** `Access denied for user 'root'@'localhost'`

**Solutions:**
1. Verify MySQL password in `.env`
2. Reset MySQL root password
3. Check MySQL user permissions:
   ```sql
   GRANT ALL PRIVILEGES ON production_request_db.* TO 'root'@'localhost';
   FLUSH PRIVILEGES;
   ```

### .env File Not Loading

**Problem:** Environment variables not working

**Solutions:**
1. Copy `.env.example` to `.env`
2. Edit `.env` with correct values
3. Restart web server
4. Check file is readable

### Port Already in Use

**Problem:** `Address already in use`

**Solutions:**
1. Check what's using port 3306/80
2. Stop other applications
3. Or change port in Laragon settings
4. Or use different virtual host

---

## Performance Tips

1. **Keep Laragon updated** for latest PHP/MySQL versions
2. **Use SSD** for better disk performance
3. **Allocate enough RAM** to Laragon
4. **Enable database query cache** for production
5. **Use browser cache** headers properly

---

## Next Steps

1. Review [README.md](README.md) for full documentation
2. Read [DEVELOPMENT.md](DEVELOPMENT.md) for development guide
3. Check [QUICKSTART.md](QUICKSTART.md) for quick reference
4. See [INSTALLATION.md](INSTALLATION.md) for detailed setup

---

## Need Help?

1. **Setup issues?** → See Troubleshooting section above
2. **How to use?** → Read [README.md](README.md)
3. **How to develop?** → Read [DEVELOPMENT.md](DEVELOPMENT.md)
4. **Quick reference?** → Check [QUICKSTART.md](QUICKSTART.md)

---

**Happy coding on Windows!** 🚀
