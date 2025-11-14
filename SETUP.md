# AIWA FARM - Setup Instructions

## Prerequisites

- PHP 8.1 or higher
- Composer
- Node.js and npm
- MySQL database
- Web server (Apache/Nginx) or PHP built-in server

## Installation Steps

### 1. Install PHP Dependencies

```bash
composer install
```

### 2. Install Node Dependencies

```bash
npm install
```

### 3. Environment Configuration

Copy the `.env.example` file to `.env`:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

### 4. Database Configuration

Edit the `.env` file and configure your database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aiwa_farm
DB_USERNAME=root
DB_PASSWORD=your_password
```

Create the database:

```sql
CREATE DATABASE aiwa_farm;
```

### 5. Run Migrations and Seeders

```bash
php artisan migrate
php artisan db:seed
```

This will create:
- A default user (username: `miura`, password: `password`)
- Sample schools
- Sample students

### 6. Build Assets

For development:

```bash
npm run dev
```

For production:

```bash
npm run build
```

### 7. Start the Development Server

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## Default Login Credentials

- **Username:** `miura`
- **Password:** `password`

## Features

### 1. Login System
- Username/password authentication
- Session-based authentication
- Secure password hashing

### 2. Dashboard
- Student search with keyword filter
- Enrollment year filter
- Status filter (2年合格, 1年合格, 不合格, 試験待ち)
- Statistics display (申込, 参加)
- Student cards with information

### 3. Student Registration
- Comprehensive student information form
- OC attendance tracking
- OC reservation date
- Online/Offline status
- JLPT level selection
- School association
- Home country education tracking
- Referrer information

### 4. School Search
- Keyword search
- School listing with contact information
- Edit and delete functionality

### 5. School Registration
- School name
- Postal code and address
- Contact person and position
- Phone and FAX numbers
- Email and website
- Memo field

## File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   └── LoginController.php
│   │   ├── DashboardController.php
│   │   ├── StudentController.php
│   │   └── SchoolController.php
│   └── Middleware/
├── Models/
│   ├── User.php
│   ├── School.php
│   └── Student.php
database/
├── migrations/
└── seeders/
resources/
├── views/
│   ├── auth/
│   │   └── login.blade.php
│   ├── layouts/
│   │   └── app.blade.php
│   ├── dashboard.blade.php
│   ├── students/
│   └── schools/
├── css/
│   └── app.css
└── js/
    └── app.js
routes/
└── web.php
```

## Troubleshooting

### Issue: "Class not found" errors

Solution: Run `composer dump-autoload`

### Issue: Database connection errors

Solution: Check your `.env` file database configuration

### Issue: 500 Internal Server Error

Solution: Check storage and bootstrap/cache directories have write permissions:
```bash
chmod -R 775 storage bootstrap/cache
```

### Issue: Assets not loading

Solution: Run `npm run dev` or `npm run build`

## Production Deployment

1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false` in `.env`
3. Run `php artisan config:cache`
4. Run `php artisan route:cache`
5. Run `php artisan view:cache`
6. Build assets: `npm run build`
7. Set proper file permissions

## Support

For issues or questions, please refer to the Laravel documentation or contact the development team.

