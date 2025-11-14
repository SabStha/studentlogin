# Install PhpSpreadsheet on Production Server

The PhpSpreadsheet package needs to be installed on your production server.

## Steps:

1. SSH into your production server:
```bash
ssh user@your-server
```

2. Navigate to your project directory:
```bash
cd /var/www/studentlogin
```

3. Install PhpSpreadsheet:
```bash
composer require phpoffice/phpspreadsheet
```

4. If composer is not available, you can also:
   - Copy the `vendor/phpoffice` directory from your local machine to the production server
   - Or run `composer install` on the production server to install all dependencies

5. Clear Laravel caches:
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

6. Set proper permissions:
```bash
sudo chown -R www-data:www-data vendor
sudo chmod -R 755 vendor
```

## Alternative: Quick Fix

If you can't install via composer on production, you can temporarily comment out the download functionality or use a simpler CSV export instead.

