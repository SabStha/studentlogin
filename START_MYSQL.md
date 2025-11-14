# How to Start MySQL on Windows

## The Problem
MySQL server is not running. You need to start it before running migrations.

## Quick Solutions

### Option 1: Start MySQL Service (Recommended)

**Using PowerShell (Run as Administrator):**
```powershell
# Find MySQL service
Get-Service | Where-Object {$_.Name -like "*mysql*"}

# Start MySQL (replace MySQL80 with your service name)
Start-Service MySQL80
```

**Using Command Prompt (Run as Administrator):**
```cmd
net start MySQL80
```

**Using Services GUI:**
1. Press `Win + R`
2. Type `services.msc` and press Enter
3. Find "MySQL" or "MySQL80" in the list
4. Right-click → Start

### Option 2: If Using XAMPP
1. Open XAMPP Control Panel
2. Click "Start" next to MySQL

### Option 3: If Using WAMP
1. Click WAMP icon in system tray
2. MySQL → Service → Start/Resume Service

### Option 4: If Using Laragon
1. Open Laragon
2. Click "Start All" or just "MySQL"

### Option 5: Use SQLite Instead (No Setup Required!)

If you just want to develop quickly without setting up MySQL:

**Step 1: Update .env**
```env
DB_CONNECTION=sqlite
DB_DATABASE=C:\Users\user\Stuent\database\database.sqlite
```

**Step 2: Create SQLite file**
```powershell
New-Item -ItemType File -Path "database\database.sqlite" -Force
```

**Step 3: Run migrations**
```powershell
php artisan migrate
```

## Verify MySQL is Running

After starting MySQL, test the connection:
```powershell
Test-NetConnection -ComputerName 127.0.0.1 -Port 3306
```

If it shows `TcpTestSucceeded : True`, MySQL is running!

## Common MySQL Service Names

- `MySQL80` - MySQL 8.0
- `MySQL` - Older versions
- `MySQL57` - MySQL 5.7
- `MariaDB` - MariaDB

## Still Having Issues?

1. **Check if MySQL is installed:**
   ```powershell
   where.exe mysql
   ```

2. **Try connecting manually:**
   ```powershell
   mysql -u root -p
   ```

3. **Check MySQL configuration:**
   - Default port: 3306
   - Default host: 127.0.0.1 or localhost

4. **Check firewall:** Windows Firewall might be blocking MySQL

## Next Steps

Once MySQL is running:
```powershell
php artisan migrate:fast-fresh --seed
```

