# Student Management System

 # Student Management System

A Laravel-based student management system

## Requirements
- PHP >= 8.1
- Composer
- MySQL
- Laravel 11

## Installation Steps

### 1. Clone the project
```bash
git clone https://github.com/mhdalaashkaki/Student_system.git
cd Student_system
```

### 2. Install dependencies
```bash
composer install
```

### 3. Setup environment file
```bash
copy .env.example .env
```

### 4. Generate application key
```bash
php artisan key:generate
```

### 5. Create database
- Open phpMyAdmin
- Create a new database named: `student_mn`

### 6. Configure `.env` file
Open `.env` file and update database credentials:
```
DB_DATABASE=student_mn
DB_USERNAME=root
DB_PASSWORD=
```

### 7. Run migrations
```bash
php artisan migrate
```

### 8. Start the application
```bash
php artisan serve
```

Now open your browser at: http://localhost:8000

## Notes
- Make sure MySQL is running before starting the project
- If you encounter any issues, run: `php artisan config:clear`


