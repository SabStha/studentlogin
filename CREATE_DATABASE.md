# Create Database - Quick Guide

## Step 1: Create the Database

You need to create the database manually before running migrations. Choose one of the following methods:

### Method 1: Using MySQL Command Line
```bash
mysql -u root -p
CREATE DATABASE aiwa_farm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### Method 2: Using phpMyAdmin
1. Open phpMyAdmin (usually at http://localhost/phpmyadmin)
2. Click on "New" in the left sidebar
3. Enter database name: `aiwa_farm`
4. Select collation: `utf8mb4_unicode_ci`
5. Click "Create"

### Method 3: Using MySQL Workbench
1. Open MySQL Workbench
2. Connect to your MySQL server
3. Click on "Create a new schema" (database icon)
4. Enter schema name: `aiwa_farm`
5. Set default collation: `utf8mb4_unicode_ci`
6. Click "Apply"

## Step 2: Update .env File (if needed)

Make sure your `.env` file has the correct database configuration:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aiwa_farm
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

**Note:** If your MySQL root user has a password, update `DB_PASSWORD` in the `.env` file.

## Step 3: Run Migrations

After creating the database, run:

```bash
php artisan migrate
```

## Step 4: Seed the Database (Optional)

To populate the database with sample data:

```bash
php artisan db:seed
```

This will create:
- Default user (username: `miura`, password: `password`)
- Sample schools
- Sample students

## Troubleshooting

### Error: "Access denied for user 'root'@'localhost'"
- Make sure your MySQL root password is correct in the `.env` file
- If you don't have a password, leave `DB_PASSWORD=` empty

### Error: "Unknown database 'aiwa_farm'"
- Make sure you created the database first
- Check that the database name in `.env` matches the one you created

### Error: "Connection refused"
- Make sure MySQL is running
- Check that `DB_HOST` and `DB_PORT` in `.env` are correct

