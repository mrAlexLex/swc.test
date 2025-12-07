# Task Management API

Production-ready REST API for task management built with Laravel, featuring token-based authentication, CRUD operations, file attachments, and email notifications.

## Features

- 🔐 **Token Authentication** - Secure API authentication using Laravel Sanctum
- 📋 **Task Management** - Full CRUD operations for tasks with filtering and pagination
- 📎 **File Attachments** - Upload and manage task attachments using Spatie Media Library
- 📧 **Email Notifications** - Automatic email notifications when tasks are created
- 🐳 **Docker Ready** - Complete Docker environment for development
- 📚 **API Documentation** - Swagger/OpenAPI documentation
- ✅ **Tested** - Comprehensive feature tests

## Tech Stack

- **PHP 8.4**
- **Laravel 12**
- **PostgreSQL 17**
- **Redis 7.2** (for queues and caching)
- **Nginx**
- **Laravel Sanctum** (authentication)
- **Spatie Media Library** (file management)
- **L5-Swagger** (API documentation)

## Requirements

- Docker & Docker Compose
- Git

## Installation

### 1. Clone the repository

```bash
git clone <repository-url>
cd swc.test
```

### 2. Create environment file

```bash
cp backend/.env.example backend/.env
```

Edit `backend/.env` and configure your settings:

```env
APP_NAME="Task Management API"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8080

DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=task_test_db
DB_USERNAME=postgres
DB_PASSWORD=174471

REDIS_HOST=redis
REDIS_PORT=6379

QUEUE_CONNECTION=redis

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
```

### 3. Start Docker containers

```bash
docker-compose up -d
```

### 4. Install dependencies and setup

```bash
# Enter the app container
docker exec -it task_test_app bash

# Inside the container:
cd backend
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link

# Publish vendor assets
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"
php artisan migrate

# Generate Swagger documentation
php artisan l5-swagger:generate
```

### 5. Start the queue worker (for email notifications)

```bash
docker exec -it task_test_app php /var/www/backend/artisan queue:work
```

## API Endpoints

### Authentication

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/register` | Register a new user |
| POST | `/api/auth/login` | Login and get token |
| POST | `/api/auth/logout` | Logout (requires auth) |
| GET | `/api/auth/me` | Get current user (requires auth) |

### Tasks (All require authentication)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/tasks` | List tasks with filtering |
| POST | `/api/tasks` | Create a new task |
| GET | `/api/tasks/{id}` | Get task details |
| PUT | `/api/tasks/{id}` | Update a task |
| DELETE | `/api/tasks/{id}` | Delete a task |

### Task Filters

- `status` - Filter by status (planned, in_progress, done)
- `user_id` - Filter by user ID
- `completion_date` - Filter by exact completion date
- `completion_date_from` - Filter from date
- `completion_date_to` - Filter to date
- `per_page` - Items per page (default: 15, max: 100)
- `sort_by` - Sort field (id, title, status, completion_date, created_at, updated_at)
- `sort_order` - Sort direction (asc, desc)

## API Usage Examples

### Register

```bash
curl -X POST http://localhost:8080/api/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "Password123",
    "password_confirmation": "Password123"
  }'
```

### Login

```bash
curl -X POST http://localhost:8080/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "Password123"
  }'
```

### Create Task

```bash
curl -X POST http://localhost:8080/api/tasks \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json" \
  -F "title=Complete documentation" \
  -F "description=Write comprehensive API documentation" \
  -F "status=planned" \
  -F "completion_date=2024-12-31" \
  -F "attachment=@/path/to/file.pdf"
```

### List Tasks with Filters

```bash
curl -X GET "http://localhost:8080/api/tasks?status=planned&per_page=10&sort_by=created_at&sort_order=desc" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

### Update Task

```bash
curl -X PUT http://localhost:8080/api/tasks/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "title": "Updated title",
    "status": "in_progress"
  }'
```

### Delete Task

```bash
curl -X DELETE http://localhost:8080/api/tasks/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

## API Response Format

All API responses follow a consistent format:

### Success Response

```json
{
  "success": true,
  "message": "Operation successful.",
  "data": { ... }
}
```

### Error Response

```json
{
  "success": false,
  "message": "Error description.",
  "errors": {
    "field": ["Error message"]
  }
}
```

## API Documentation

Swagger documentation is available at:
```
http://localhost:8080/api/documentation
```

## Running Tests

```bash
# Inside the app container
docker exec -it task_test_app bash
cd backend
php artisan test
```

Or run specific test files:

```bash
php artisan test tests/Feature/AuthenticationTest.php
php artisan test tests/Feature/TaskTest.php
php artisan test tests/Feature/NotificationTest.php
```

## Test User

After seeding, you can use these credentials:

- **Email:** test@example.com
- **Password:** password

## Project Structure

```
backend/
├── app/
│   ├── Enums/
│   │   └── TaskStatus.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── AuthController.php
│   │   │       └── TaskController.php
│   │   ├── Requests/
│   │   │   ├── Auth/
│   │   │   │   ├── LoginRequest.php
│   │   │   │   └── RegisterRequest.php
│   │   │   └── Task/
│   │   │       ├── IndexTaskRequest.php
│   │   │       ├── StoreTaskRequest.php
│   │   │       └── UpdateTaskRequest.php
│   │   └── Resources/
│   │       ├── TaskCollection.php
│   │       ├── TaskResource.php
│   │       └── UserResource.php
│   ├── Models/
│   │   ├── Task.php
│   │   └── User.php
│   ├── Notifications/
│   │   └── TaskCreatedNotification.php
│   └── Policies/
│       └── TaskPolicy.php
├── database/
│   ├── factories/
│   │   ├── TaskFactory.php
│   │   └── UserFactory.php
│   ├── migrations/
│   └── seeders/
│       └── DatabaseSeeder.php
├── routes/
│   └── api.php
└── tests/
    └── Feature/
        ├── AuthenticationTest.php
        ├── NotificationTest.php
        └── TaskTest.php
```

## Docker Services

| Service | Container Name | Port |
|---------|---------------|------|
| Nginx | task_test_nginx | 8080 |
| PHP-FPM | task_test_app | - |
| PostgreSQL | task_test_db | 5432 |
| Redis | task_test_redis | 6379 |

## Makefile Commands

```bash
make up        # Start containers
make down      # Stop containers
make restart   # Restart containers
make logs      # View logs
make shell     # Enter app container
make test      # Run tests
make migrate   # Run migrations
make seed      # Run seeders
make fresh     # Fresh migration with seed
```

## License

MIT License
