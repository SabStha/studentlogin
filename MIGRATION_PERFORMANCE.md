# Migration Performance Optimization

## Why Migrations Are Slow

The `migrate:fresh` command can be slow because:

1. **Foreign Key Constraints**: MySQL checks foreign keys when dropping tables, which can be slow
2. **Large Amounts of Data**: If tables have many rows, dropping them takes time
3. **MySQL Configuration**: Default MySQL settings may not be optimized for fast DDL operations

## Solutions

### Solution 1: Disable Foreign Key Checks (Recommended)

When running `migrate:fresh`, you can temporarily disable foreign key checks:

```bash
php artisan migrate:fresh --force
```

Or manually in MySQL:
```sql
SET FOREIGN_KEY_CHECKS=0;
-- Run your migrations
SET FOREIGN_KEY_CHECKS=1;
```

### Solution 2: Use migrate:refresh Instead

If you just want to reset and re-run migrations (without dropping all tables first):

```bash
php artisan migrate:refresh
```

### Solution 3: Optimize MySQL Configuration

Add these settings to your MySQL configuration (my.ini or my.cnf):

```ini
[mysqld]
foreign_key_checks=0
innodb_flush_log_at_trx_commit=2
sync_binlog=0
```

**Note:** Only use these settings for development, not production!

### Solution 4: Drop Tables Manually (Fastest)

If you need to frequently reset the database:

1. Create a script to drop all tables:
```sql
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS schools;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS migrations;
SET FOREIGN_KEY_CHECKS=1;
```

2. Then run:
```bash
php artisan migrate
```

### Solution 5: Use SQLite for Development

For faster development, you can use SQLite instead of MySQL:

In `.env`:
```env
DB_CONNECTION=sqlite
DB_DATABASE=C:\Users\user\Stuent\database\database.sqlite
```

Then create the SQLite file:
```bash
touch database/database.sqlite
php artisan migrate:fresh
```

## Quick Commands

### Fast Reset (Recommended)
```bash
php artisan migrate:fresh --seed
```

### Reset Without Seeding
```bash
php artisan migrate:fresh
```

### Just Re-run Migrations
```bash
php artisan migrate:refresh
```

## Performance Tips

1. **For Development**: Use SQLite for faster migrations
2. **For Production**: Always use MySQL with proper foreign key constraints
3. **Large Databases**: Consider using `migrate:refresh` instead of `migrate:fresh`
4. **Frequent Resets**: Create a custom artisan command that drops tables with foreign key checks disabled

## Current Migration Status

To check which migrations have run:
```bash
php artisan migrate:status
```

To see migration progress in real-time:
```bash
php artisan migrate --verbose
```

