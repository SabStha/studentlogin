# AIWA FARM - Student and School Management System

A Laravel-based web application for managing students and schools with Japanese UI.

## Features

1. **Login System** - User authentication with username/password
2. **Dashboard** - Student search and display with filters
3. **Student Registration** - Comprehensive student registration form
4. **School Search** - Search and view schools
5. **School Registration** - Register new schools

## Installation

1. Install dependencies:
```bash
composer install
npm install
```

2. Copy `.env.example` to `.env`:
```bash
cp .env.example .env
```

3. Generate application key:
```bash
php artisan key:generate
```

4. Configure database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aiwa_farm
DB_USERNAME=root
DB_PASSWORD=
```

5. Run migrations and seeders:
```bash
php artisan migrate
php artisan db:seed
```

6. Start the development server:
```bash
php artisan serve
```

7. Build assets (in another terminal):
```bash
npm run dev
```

## Default Login Credentials

- Username: `miura`
- Password: `password`

## Access

- Login: http://localhost:8000/login
- Dashboard: http://localhost:8000/dashboard

## Database Structure

### Users
- id, name, username, email, password, remember_token, timestamps

### Schools
- id, name, postal_code, address, contact_person, position, phone, fax, email, website, memo, timestamps

### Students
- id, name_english, name_kana, nationality, gender, age, email, jlpt_level, school_id, student_number, home_country_education, referrer, oc_attendance, oc_reservation_date, online, enrollment_year, status, applied, participated, timestamps

## Technologies Used

- Laravel 10
- PHP 8.1+
- MySQL
- Vite
- Blade Templates

