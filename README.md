# 🩸 Blood Donation Management System

## 🛠️ အသုံးပြုထားသော Technology များ

| Category       | Technology      |
| -------------- | --------------- |
| Framework      | Laravel 12      |
| Language       | PHP 8.2+        |
| Database       | Postgres        |
| Authentication | Laravel Sanctum |
| API Format     | RESTful         |

---

## 📂 Folder Structure

```
app/
 |--Http/
 |  |--Controllers/
 |  |--Helpers/
 |  |--Middleware/
 |  |--Resources/
 |  |--Requests/
 |--Models/
 database/
 |--factories/
 |--migrations/
 |--seeders/
 routes/
 |--api.php

```

---

## 📦 Installation Guide

```
git clone https://github.com/one-project-one-month/BDMS_Laravel
```

```
cd BDMS_Laravel
```

```
composer install
```

```
cp .env.example .env
```

```
php artisan key:generate
```

```
php artisan migrate --seed
```

```
php artisan serve
```

---
