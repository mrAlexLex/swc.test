## Installation

### 1. Clone the repository

```bash
git clone git@github.com:mrAlexLex/swc.test.git
cd project_name
```

### 2. Create environment file

```bash
cp backend/.env.example backend/.env
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

### Tasks (All require authentication)

| Method | Endpoint          | Description |
|--------|-------------------|-------------|
| GET | `/api/auth/tasks`     | List tasks with filtering |
| POST | `/api/auth/tasks`      | Create a new task |
| GET | `/api/auth/tasks/{id}` | Get task details |
| PUT | `/api/auth/tasks/{id}` | Update a task |
| DELETE | `/api/auth/tasks/{id}` | Delete a task |

### Task Filters

- `status` - Filter by status (planned, in_progress, done)
- `user_id` - Filter by user ID
- `completion_date` - Filter by exact completion date
- `completion_date_from` - Filter from date
- `completion_date_to` - Filter to date
- `per_page` - Items per page (default: 15, max: 100)
- `sort_by` - Sort field (id, title, status, completion_date, created_at, updated_at)
- `sort_order` - Sort direction (asc, desc)

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
