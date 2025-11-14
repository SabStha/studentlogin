# AIWA FARM - Project Summary

## Overview

AIWA FARM is a comprehensive Laravel-based web application for managing students and schools with a Japanese user interface. The application provides features for user authentication, student management, school management, and dashboard analytics.

## Features Implemented

### 1. Authentication System
- ✅ Login page with username/password authentication
- ✅ Session-based authentication
- ✅ Secure password hashing
- ✅ Logout functionality
- ✅ Protected routes with authentication middleware

### 2. Dashboard
- ✅ Student search with keyword filtering
- ✅ Enrollment year filter (including 2026)
- ✅ Status filter (2年合格, 1年合格, 不合格, 試験待ち)
- ✅ Statistics display (申込, 参加)
- ✅ Student cards with information display
- ✅ Status badges with color coding
- ✅ Pagination support

### 3. Student Management
- ✅ Student registration form with all required fields:
  - OC attendance (OC出席)
  - OC reservation date (OC予約日時)
  - Online status (オンライン)
  - Name in English (名前 英語)
  - Name in Kana (名前 カナ)
  - Nationality (国籍)
  - Gender (性別)
  - Age (年齢)
  - Email (Eメール)
  - JLPT level
  - Current school (今の学校)
  - Student number (学籍番号)
  - Home country education (母国の最終学歴)
  - Referrer (紹介者)
  - Enrollment year (入学年度)
  - Status (ステータス)
- ✅ Student edit functionality
- ✅ Student listing with search
- ✅ Student deletion

### 4. School Management
- ✅ School registration form with all required fields:
  - School name (学校名)
  - Postal code (郵便番号)
  - Address (住所)
  - Contact person (担当者)
  - Position (役職)
  - Phone number (電話番号)
  - FAX number (FAX番号)
  - Email (Eメール)
  - Website (Webサイト)
  - Memo (メモ)
- ✅ School search functionality
- ✅ School listing with contact information
- ✅ School edit functionality
- ✅ School deletion

## Database Structure

### Users Table
- id, name, username, email, password, remember_token, timestamps

### Schools Table
- id, name, postal_code, address, contact_person, position, phone, fax, email, website, memo, timestamps

### Students Table
- id, name_english, name_kana, nationality, gender, age, email, jlpt_level, school_id, student_number, home_country_education, referrer, oc_attendance, oc_reservation_date, online, enrollment_year, status, applied, participated, timestamps

## File Structure

```
app/
├── Console/Kernel.php
├── Exceptions/Handler.php
├── Http/
│   ├── Controllers/
│   │   ├── Auth/LoginController.php
│   │   ├── Controller.php
│   │   ├── DashboardController.php
│   │   ├── SchoolController.php
│   │   └── StudentController.php
│   ├── Kernel.php
│   └── Middleware/
│       ├── Authenticate.php
│       ├── EncryptCookies.php
│       ├── PreventRequestsDuringMaintenance.php
│       ├── RedirectIfAuthenticated.php
│       ├── TrimStrings.php
│       ├── TrustProxies.php
│       ├── ValidateSignature.php
│       └── VerifyCsrfToken.php
├── Models/
│   ├── School.php
│   ├── Student.php
│   └── User.php
└── Providers/
    ├── AppServiceProvider.php
    └── RouteServiceProvider.php

database/
├── migrations/
│   ├── 2014_10_12_000000_create_users_table.php
│   ├── 2024_01_01_000001_create_schools_table.php
│   └── 2024_01_01_000002_create_students_table.php
└── seeders/
    └── DatabaseSeeder.php

resources/
├── css/app.css
├── js/
│   ├── app.js
│   └── bootstrap.js
└── views/
    ├── auth/login.blade.php
    ├── dashboard.blade.php
    ├── layouts/app.blade.php
    ├── schools/
    │   ├── create.blade.php
    │   ├── edit.blade.php
    │   └── index.blade.php
    └── students/
        ├── create.blade.php
        ├── edit.blade.php
        └── index.blade.php

routes/
├── api.php
├── console.php
└── web.php

config/
├── app.php
├── auth.php
├── database.php
├── filesystems.php
└── session.php
```

## Routes

### Authentication Routes
- GET `/login` - Show login form
- POST `/login` - Process login
- POST `/logout` - Process logout

### Protected Routes (Require Authentication)
- GET `/` - Dashboard (redirects to /dashboard)
- GET `/dashboard` - Dashboard
- GET `/students` - Student listing
- GET `/students/create` - Student registration form
- POST `/students` - Store student
- GET `/students/{id}/edit` - Student edit form
- PUT `/students/{id}` - Update student
- DELETE `/students/{id}` - Delete student
- GET `/schools` - School listing
- GET `/schools/create` - School registration form
- POST `/schools` - Store school
- GET `/schools/{id}/edit` - School edit form
- PUT `/schools/{id}` - Update school
- DELETE `/schools/{id}` - Delete school

## Default Data

### Default User
- Username: `miura`
- Password: `password`

### Sample Schools
1. さくら国際言語学院
2. 専門学校 さくら国際言語教育学院
3. 折尾愛真短期大学
4. 北九州YMCA学院

### Sample Students
1. リン (ミャンマー) - N3 - 試験待ち
2. アルビン (イラン|イラン・イスラム共和国) - N2 - 試験待ち

## UI Features

- Clean, modern Japanese UI
- Responsive design
- Color-coded status badges
- Search and filter functionality
- Pagination support
- Form validation
- Error handling
- Success messages
- Navigation menu
- Logout functionality

## Technology Stack

- **Framework:** Laravel 10
- **PHP Version:** 8.1+
- **Database:** MySQL
- **Frontend:** Blade Templates, CSS, JavaScript
- **Build Tool:** Vite
- **Authentication:** Session-based

## Next Steps

1. Install dependencies: `composer install && npm install`
2. Configure environment: Copy `.env.example` to `.env` and configure database
3. Generate application key: `php artisan key:generate`
4. Run migrations: `php artisan migrate`
5. Seed database: `php artisan db:seed`
6. Build assets: `npm run dev`
7. Start server: `php artisan serve`
8. Access application: http://localhost:8000

## Notes

- The application uses username-based authentication instead of email
- All text and UI elements are in Japanese
- The application includes sample data for testing
- All forms include validation
- The application is ready for deployment after configuration

