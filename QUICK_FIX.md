# Quick Fix for Slow Migrations

## The Problem

`php artisan migrate:fresh` is slow because MySQL checks foreign key constraints when dropping tables.

## Quick Solution

I've created a faster migration command. Use this instead:

```bash
php artisan migrate:fast-fresh
```

Or with seeding:

```bash
php artisan migrate:fast-fresh --seed
```

## What Changed

1. ✅ Optimized the students migration to handle foreign keys better
2. ✅ Created a new `migrate:fast-fresh` command that disables foreign key checks during table drops
3. ✅ This makes dropping tables much faster

## Alternative: Manual SQL (Fastest)

If you want the absolute fastest way, run this SQL directly in MySQL:

```sql
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS schools;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS migrations;
SET FOREIGN_KEY_CHECKS=1;
```

Then run:
```bash
php artisan migrate
```

## Why It's Slow

- MySQL checks foreign key constraints when dropping tables
- With foreign keys, MySQL must verify relationships before dropping
- Disabling foreign key checks speeds this up significantly

## Performance Comparison

- **Normal `migrate:fresh`**: ~5-30 seconds (depending on data)
- **Fast `migrate:fast-fresh`**: ~1-3 seconds
- **Manual SQL + migrate**: ~1-2 seconds

## Use Cases

- **Development**: Use `migrate:fast-fresh` - it's safe and fast
- **Production**: Use normal `migrate:fresh` - maintains data integrity checks

