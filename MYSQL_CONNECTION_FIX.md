# MySQL Connection Fix

## Error: "No connection could be made because the target machine actively refused it"

This error means MySQL server is **not running** or not accessible on `127.0.0.1:3306`.

## Quick Solutions

### Solution 1: Start MySQL Service (Windows)

#### Using Services (GUI)
1. Press `Win + R`
2. Type `services.msc` and press Enter
3. Find "MySQL" or "MySQL80" or "MariaDB" in the list
4. Right-click and select "Start"
5. If it's already running, try "Restart"

#### Using Command Line (PowerShell as Administrator)
```powershell
# Check MySQL service name
Get-Service -Name "*mysql*"

# Start MySQL (replace MySQL80 with your actual service name)
Start-Service MySQL80

# Or for MariaDB
Start-Service MariaDB
```

#### Using Command Line (Command Prompt as Administrator)
```cmd
# Start MySQL
net start MySQL80

# Or for MariaDB
net start MariaDB
```

### Solution 2: Check MySQL Installation

If MySQL is not installed, you need to install it:

1. **Download MySQL**: https://dev.mysql.com/downloads/installer/
2. **Or use XAMPP/WAMP**: 
   - XAMPP: https://www.apachefriends.org/
   - WAMP: https://www.wampserver.com/
3. **Or use Laragon** (Recommended for Windows): https://laragon.org/

### Solution 3: Check MySQL Port

MySQL might be running on a different port. Check your `.env` file:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
```

If MySQL is on a different port (like 3307), update `DB_PORT` in `.env`.

### Solution 4: Use XAMPP/WAMP MySQL

If you're using XAMPP or WAMP:

1. **XAMPP**: Start MySQL from XAMPP Control Panel
2. **WAMP**: Click the WAMP icon → MySQL → Service → Start/Resume Service

### Solution 5: Check MySQL Configuration

Verify MySQL is configured correctly:

1. Check if MySQL is installed:
   ```powershell
   where.exe mysql
   ```

2. Try connecting manually:
   ```powershell
   mysql -u root -p
   ```

3. If it works, check your `.env` file has correct credentials

### Solution 6: Use SQLite for Development (Fastest)

If you just want to develop quickly without MySQL:

1. Update `.env`:
   ```env
   DB_CONNECTION=sqlite
   DB_DATABASE=C:\Users\user\Stuent\database\database.sqlite
   ```

2. Create SQLite database:
   ```powershell
   New-Item -ItemType File -Path "database\database.sqlite" -Force
   ```

3. Run migrations:
   ```powershell
   php artisan migrate
   ```

## Common MySQL Service Names

- `MySQL80` - MySQL 8.0
- `MySQL` - Older MySQL versions
- `MariaDB` - MariaDB
- `MySQL57` - MySQL 5.7

## Verify MySQL is Running

After starting MySQL, verify it's working:

```powershell
# Test connection
Test-NetConnection -ComputerName 127.0.0.1 -Port 3306

# Or try connecting
mysql -u root -p
```

## Next Steps

Once MySQL is running:

1. Verify connection:
   ```powershell
   php artisan migrate:status
   ```

2. Run migrations:
   ```powershell
   php artisan migrate
   ```

3. Or use the fast command:
   ```powershell
   php artisan migrate:fast-fresh --seed
   ```

## Troubleshooting

### Error: "Access denied for user 'root'@'localhost'"
- Check your `.env` file `DB_PASSWORD`
- If no password, leave it empty: `DB_PASSWORD=`
- If you have a password, make sure it's correct

### Error: "Unknown database 'aiwa_farm'"
- Create the database first (see CREATE_DATABASE.md)
- Or use SQLite for development

### Error: "Can't connect to MySQL server"
- MySQL is not running - start the service
- Check firewall settings
- Verify MySQL is installed

