# Fix Storage Permissions on Production Server

## Error
```
The stream or file "/var/www/studentlogin/storage/logs/laravel.log" could not be opened in append mode: Failed to open stream: Permission denied
```

## Solution

SSH into your production server and run these commands:

```bash
cd /var/www/studentlogin

# Set ownership to web server user (usually www-data)
sudo chown -R www-data:www-data storage bootstrap/cache

# Set proper permissions
sudo chmod -R 775 storage bootstrap/cache

# Make sure all subdirectories are writable
sudo find storage -type d -exec chmod 775 {} \;
sudo find storage -type f -exec chmod 664 {} \;
sudo find bootstrap/cache -type d -exec chmod 775 {} \;
sudo find bootstrap/cache -type f -exec chmod 664 {} \;
```

## Alternative: If www-data doesn't work

Try these common web server users:

```bash
# For Apache
sudo chown -R www-data:www-data storage bootstrap/cache

# For Nginx with PHP-FPM
sudo chown -R www-data:www-data storage bootstrap/cache
# OR
sudo chown -R nginx:nginx storage bootstrap/cache

# For specific user setup
sudo chown -R $USER:www-data storage bootstrap/cache
```

## Verify Permissions

After setting permissions, verify:

```bash
ls -la storage/logs/
ls -la bootstrap/cache/
```

You should see:
- Directories: `drwxrwxr-x` (775)
- Files: `-rw-rw-r--` (664)
- Owner: `www-data` or your web server user

## Quick One-Liner Fix

```bash
cd /var/www/studentlogin && sudo chown -R www-data:www-data storage bootstrap/cache && sudo chmod -R 775 storage bootstrap/cache
```

## Also Check

Make sure the `storage/students` directory exists and is writable:

```bash
sudo mkdir -p storage/students
sudo chown -R www-data:www-data storage/students
sudo chmod -R 775 storage/students
```

## After Fixing

Clear caches:
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

Then try submitting the form again.

