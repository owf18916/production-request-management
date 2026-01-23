# Panduan Deployment Project di Server Windows

Panduan lengkap untuk deployment Production Request Management System di Windows server dengan asumsi PHP, MySQL, Apache, dan Git sudah terinstall.

---

## 📋 Prerequisites

Pastikan sudah terinstall:
- ✅ **Git** - untuk cloning project
- ✅ **PHP** (7.4 atau lebih tinggi, direkomendasikan 8.2+)
- ✅ **MySQL** (5.7 atau lebih tinggi)
- ✅ **Apache** - dengan `mod_rewrite` enabled
- ✅ **Composer** (opsional, untuk dependency management)

---

## 🚀 TAHAP 1: Cloning Project dari Git

### Step 1.1: Buka Command Prompt atau PowerShell

Tekan `Win + R`, ketik `cmd` (untuk Command Prompt) atau `powershell` (untuk PowerShell), kemudian tekan Enter.

### Step 1.2: Navigasi ke Document Root Apache

```cmd
cd C:\Apache24\htdocs
```

**Catatan:** Path dapat berbeda tergantung instalasi Apache Anda:
- XAMPP: `C:\xampp\htdocs`
- Laragon: `C:\laragon\www`
- Instalasi default Apache: `C:\Apache24\htdocs`

### Step 1.3: Clone Repository

```cmd
git clone <your-repository-url> production-request-management
```

Ganti `<your-repository-url>` dengan URL repository GitHub/GitLab Anda.

Contoh:
```cmd
git clone https://github.com/your-username/production-request-management.git production-request-management
```

### Step 1.4: Masuk ke Direktori Project

```cmd
cd production-request-management
```

### Step 1.5: Verifikasi File Penting Ada

Pastikan file/folder berikut ada:
```
✓ composer.json          - Dependency management
✓ app/                   - Source code
✓ config/                - Configuration files
✓ database/              - Migration files
✓ public/                - Web root (index.php)
✓ routes/                - Routing configuration
```

---

## 🗄️ TAHAP 2: Setup Database MySQL

### Step 2.1: Buat Database

Buka MySQL command line:

```cmd
mysql -u root -p
```

Jika MySQL tidak menggunakan password, cukup:
```cmd
mysql -u root
```

### Step 2.2: Jalankan Perintah SQL

Di prompt MySQL, jalankan:

```sql
CREATE DATABASE production_request_db 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

EXIT;
```

**Hasil yang diharapkan:**
```
Query OK, 1 row affected (0.02 sec)
```

### Step 2.3: Verifikasi Database Terbuat

```cmd
mysql -u root -p
```

```sql
SHOW DATABASES;
```

Anda seharusnya melihat `production_request_db` dalam list.

---

## 📊 TAHAP 3: Import Database Schema

### Step 3.1: Import Schema Lengkap

**Opsi A: Menggunakan Helper Script (PALING MUDAH) 🎯**

Cukup double-click file `import-database.bat` dari folder project:

```
import-database.bat
```

Script ini akan:
- Minta password MySQL
- Import all 20 tables
- Load sample data
- Tampilkan pesan sukses

---

**Opsi B: Manual Command Prompt**

Dari direktori project, jalankan file `000_complete_schema.sql`:

```cmd
mysql -u root production_request_db < database/migrations/000_complete_schema.sql
```

---

**Opsi C: Manual PowerShell**

```powershell
Get-Content database/migrations/000_complete_schema.sql | mysql -u root production_request_db
```

---

**⚠️ PENTING:** 
- Gunakan file **`000_complete_schema.sql`** - ini adalah file terlengkap
- File ini mencakup SEMUA 20 table dalam satu file
- Jangan gunakan `001_initial_schema.sql` karena tidak lengkap (hanya 9 table)
- File lain (003-013) adalah file terpisah, tidak perlu dijalankan

**Hasil yang diharapkan:**
- Tidak ada pesan error
- Semua 20 table berhasil dibuat
- Sample data ter-import (akun demo sudah siap)

### Step 3.2: Verifikasi Import Berhasil

```cmd
mysql -u root production_request_db
```

```sql
SHOW TABLES;
```

Anda seharusnya melihat **20 table** berikut:
```
✓ activity_logs
✓ audit_logs
✓ master_atk
✓ master_checksheet
✓ master_conveyor
✓ password_reset_tokens
✓ production_requests
✓ request_atk
✓ request_atk_history
✓ request_attachments
✓ request_checksheet
✓ request_checksheet_history
✓ request_comments
✓ request_id
✓ request_id_details
✓ request_id_history
✓ request_memo
✓ request_memo_history
✓ user_conveyor
✓ users
```

Verifikasi data sample sudah ter-import:
```sql
SELECT COUNT(*) as total_users FROM users;
SELECT COUNT(*) as total_requests FROM production_requests;
```

Keluar dari MySQL:
```sql
EXIT;
```

---

## ⚙️ TAHAG 4: Konfigurasi Aplikasi

### Step 4.1: Configure Environment File

Jika belum ada, copy file `.env.example` ke `.env`:

```cmd
copy .env.example .env
```

Atau jika file `.env.example` tidak ada, buat file baru `.env` di root project:

**Buka dengan text editor (Notepad, VS Code, dll) dan isi dengan format yang benar:**

```env
APP_NAME="Production Request Management System"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-domain.com/production-request-management/public
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_NAME=production_request_db
DB_USER=root
DB_PASSWORD=your_mysql_password
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
DB_PREFIX=
DB_STRICT=true
DB_ENGINE=InnoDB
```

**Penjelasan:**
- `APP_ENV`: Set ke `production` untuk server live
- `APP_DEBUG`: Set ke `false` untuk production (jangan expose error detail)
- `APP_URL`: Ganti dengan domain/IP server Anda
- `DB_HOST`: Host MySQL (biasanya `localhost`)
- `DB_NAME`: Nama database (`production_request_db`)
- `DB_USER`: Username MySQL (default `root`)
- `DB_PASSWORD`: Password MySQL (kosongkan jika tidak ada password)

**⚠️ Format `.env` - PENTING:**
- Boolean values: `true` atau `false` (TANPA tanda kutip)
- String values: Gunakan tanda kutip `"value"` atau `'value'`
- Tidak ada spasi di sekitar `=`
- Tidak ada space di value

**Format Benar:**
```env
APP_DEBUG=false          ✓ Boolean
APP_NAME="My App"        ✓ String
DB_PORT=3306             ✓ Integer
```

**Format Salah:**
```env
APP_DEBUG="false"        ✗ Boolean harus tanpa kutip
APP_DEBUG = false        ✗ Ada spasi di sekitar =
APP_NAME="My App         ✗ Missing closing quote
```

### Step 4.2: Verifikasi Apache Configuration

#### Enable mod_rewrite di Apache

Buka file konfigurasi Apache:
```
C:\Apache24\conf\httpd.conf
```

Pastikan baris berikut uncommented (tidak ada # di depan):
```apache
LoadModule rewrite_module modules/mod_rewrite.so
```

#### Configure Virtual Host (Opsional)

Di file yang sama, tambahkan:

```apache
<VirtualHost *:80>
    ServerName production-request.local
    DocumentRoot "C:\Apache24\htdocs\production-request-management\public"
    
    <Directory "C:\Apache24\htdocs\production-request-management\public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**Simpan file dan restart Apache.**

---

## 🔍 TAHAP 5: Install Dependencies (Optional - Jika menggunakan Composer)

### Step 5.1: Install Composer

Jika Composer belum terinstall, download dari https://getcomposer.org/

### Step 5.2: Install Project Dependencies

Dari direktori project:

```cmd
composer install
```

Atau jika perlu update:

```cmd
composer update
```

---

## ✅ TAHAP 6: Test & Verification

### Step 6.1: Verify PHP Syntax

```cmd
php -l public/index.php
```

Hasil yang diharapkan:
```
No syntax errors detected in public/index.php
```

### Step 6.2: Verify Database Connection

Buat file test `test-db.php` di root project:

```php
<?php
require 'config/database.php';

try {
    $pdo = new PDO(
        'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';charset=' . $_ENV['DB_CHARSET'],
        $_ENV['DB_USER'],
        $_ENV['DB_PASSWORD']
    );
    echo "✅ Database connection successful!\n";
    
    $result = $pdo->query("SELECT COUNT(*) as count FROM users");
    $count = $result->fetch(PDO::FETCH_ASSOC);
    echo "Total users: " . $count['count'] . "\n";
    
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
}
?>
```

Jalankan dari command prompt:
```cmd
php test-db.php
```

### Step 6.3: Start Apache

**Option A: Dari Command Prompt**
```cmd
net start Apache2.4
```

**Option B: Dari Services**
1. Tekan `Win + R`
2. Ketik `services.msc` dan Enter
3. Cari Apache
4. Klik kanan → Start

**Option C: Dari Laragon**
- Right-click Laragon icon di system tray
- Klik "Start All"

### Step 6.4: Access Application

Buka browser dan navigasi ke:

```
http://localhost/production-request-management/public
```

Atau jika menggunakan virtual host:
```
http://production-request.local
```

### Step 6.5: Login

Gunakan akun demo:
- **Email**: admin@example.com
- **Password**: admin123

---

## 🔐 TAHAP 7: Production Checklist

Sebelum go-live, pastikan:

### Security

- [ ] Ubah password MySQL dari default
- [ ] Set `APP_DEBUG=false` di `.env`
- [ ] Set `APP_ENV=production` di `.env`
- [ ] Ganti akun demo dengan user production
- [ ] Atur file permissions dengan benar
- [ ] Backup database secara berkala

### Performance

- [ ] Enable caching jika diperlukan
- [ ] Optimize database indexes
- [ ] Setup database backup schedule
- [ ] Monitor disk space

### Monitoring

- [ ] Setup error logging
- [ ] Monitor Apache error logs
- [ ] Monitor MySQL performance
- [ ] Setup uptime monitoring

---

## 🐛 Troubleshooting

### Problem: "Connection refused" pada database

**Solution:**
1. Pastikan MySQL service running:
   ```cmd
   net start MySQL80
   ```
2. Cek file `.env` - pastikan credentials benar
3. Test connection manual:
   ```cmd
   mysql -u root -p -h localhost
   ```

### Problem: "mod_rewrite not enabled"

**Solution:**
1. Buka `C:\Apache24\conf\httpd.conf`
2. Cari baris: `#LoadModule rewrite_module modules/mod_rewrite.so`
3. Uncomment (hapus `#`) di depannya
4. Restart Apache

### Problem: 404 pada semua request

**Solution:**
1. Verifikasi `.htaccess` di folder `public/`
2. Pastikan DocumentRoot Apache menunjuk ke folder `public/`
3. Restart Apache setelah perubahan

### Problem: "Permission denied" pada file write

**Solution:**
1. Buka file explorer
2. Klik kanan folder `production-request-management`
3. Properties → Security
4. Edit → Full Control untuk user yang menjalankan Apache

### Problem: Database import error

**Solution:**
1. Pastikan MySQL user punya permission CREATE TABLE:
   ```sql
   GRANT ALL PRIVILEGES ON production_request_db.* TO 'root'@'localhost';
   ```
2. Pastikan charset dan collation di command sama dengan di database:
   ```sql
   ALTER DATABASE production_request_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

---

## 📞 Support

Jika mengalami masalah:
1. Cek file logs:
   - Apache errors: `C:\Apache24\logs\error.log`
   - MySQL errors: MySQL data folder
2. Verifikasi semua prerequisites terinstall
3. Pastikan file konfigurasi `.env` sudah benar
4. Review INSTALLATION.md atau WINDOWS_SETUP.md di project

---

## 📝 Quick Reference - Command Summary

```cmd
# Clone project
git clone <url> production-request-management
cd production-request-management

# Create database
mysql -u root
CREATE DATABASE production_request_db CHARACTER SET utf8mb4;
EXIT;

# Import schema (gunakan 000_complete_schema.sql - file lengkap!)
mysql -u root production_request_db < database/migrations/000_complete_schema.sql

# Verify PHP
php -l public/index.php

# Start Apache
net start Apache2.4

# Test database connection
php test-db.php

# Access application
# http://localhost/production-request-management/public
```

---

**Selesai! 🎉 Aplikasi sudah siap digunakan.**

Untuk dokumentasi lebih detail, lihat file:
- [INSTALLATION.md](INSTALLATION.md)
- [WINDOWS_SETUP.md](WINDOWS_SETUP.md)
- [QUICKSTART.md](QUICKSTART.md)
- [README.md](README.md)
