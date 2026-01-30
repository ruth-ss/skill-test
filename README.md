# Laravel Skill Test – Post REST API

This project implements RESTful routes for a Post model using Laravel 12.

The API supports:
- Draft posts
- Scheduled publishing (without cron job)
- Published posts
- Authentication & author-based authorization

No views are implemented as per the requirements.
All responses are returned as JSON or simple strings.

---

## Requirements
- PHP 8.4
- Composer
- SQLite
- Node v22.15.0 (optional)

---

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate

php artisan migrate
php artisan db:seed

php artisan serve
