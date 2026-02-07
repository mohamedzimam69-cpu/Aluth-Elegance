# Troubleshooting Guide

## Admin Login - Internal Server Error

If you see "500 Internal Server Error" when trying to login:

### Solution 1: Run Setup First
1. Go to: `http://localhost/aluth-elegance/setup.php`
2. This will create all necessary database tables
3. Then try login again

### Solution 2: Test Login
1. Go to: `http://localhost/aluth-elegance/admin/test-login.php`
2. This will show you what's wrong
3. Follow the instructions shown

### Solution 3: Check Database
1. Open phpMyAdmin
2. Check if database `aluth_elegance` exists
3. Check if table `admin_users` exists
4. If not, run setup.php

### Solution 4: Manual Database Setup
If setup.php doesn't work, manually run this SQL:

```sql
CREATE DATABASE IF NOT EXISTS aluth_elegance;
USE aluth_elegance;

CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Password is: admin123
INSERT INTO admin_users (username, password, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'aluthelegance@gmail.com');
```

### Solution 5: Check PHP Errors
1. Open `config/database.php`
2. Make sure credentials are correct:
   - DB_HOST: localhost
   - DB_USER: root
   - DB_PASS: (empty or your password)
   - DB_NAME: aluth_elegance

## Common Issues

### "Database connection failed"
- Make sure MySQL is running in XAMPP
- Check database credentials in `config/database.php`

### "Admin user not found"
- Run setup.php to create admin user
- Or manually insert admin user using SQL above

### "Too many failed attempts"
- Wait 15 minutes
- Or clear browser cookies
- Or restart browser

### "Session expired"
- This is normal after 30 minutes of inactivity
- Just login again

## Quick Fix Steps

1. **Stop Apache** in XAMPP
2. **Start Apache** again
3. **Start MySQL** if not running
4. Go to: `http://localhost/aluth-elegance/setup.php`
5. Then go to: `http://localhost/aluth-elegance/admin/login.php`
6. Login with: admin / admin123

## Still Not Working?

1. Check Apache error logs in XAMPP
2. Enable PHP error display:
   - Open `php.ini`
   - Set: `display_errors = On`
   - Restart Apache

3. Check browser console for JavaScript errors

## Contact

If none of these work, the issue might be:
- PHP version (need PHP 7.0+)
- MySQL not running
- File permissions
- XAMPP configuration

Make sure you're using:
- PHP 7.0 or higher
- MySQL 5.6 or higher
- Apache 2.4 or higher
