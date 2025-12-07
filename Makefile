help:
	@echo "Task Management API - Available Commands:"
	@echo ""
	@echo "  make build     - Build Docker containers"
	@echo "  make up        - Start all containers"
	@echo "  make down      - Stop all containers"
	@echo "  make restart   - Restart all containers"
	@echo "  make logs      - View container logs"
	@echo "  make shell     - Access PHP container shell"
	@echo "  make install   - Install dependencies"
	@echo "  make migrate   - Run migrations"
	@echo "  make seed      - Run database seeders"
	@echo "  make fresh     - Fresh migration with seeds"
	@echo "  make test      - Run tests"
	@echo "  make queue     - Start queue worker"
	@echo "  make swagger   - Generate API documentation"
	@echo "  make clear     - Clear all caches"

build:
	docker-compose up -d --build

up:
	docker-compose up -d

down:
	docker-compose down

restart:
	docker-compose restart

logs:
	docker-compose logs -f

shell:
	docker-compose exec app bash

install:
	docker-compose exec -w /var/www/backend app composer install
	docker-compose exec -w /var/www/backend app php artisan key:generate
	docker-compose exec -w /var/www/backend app php artisan storage:link

migrate:
	docker-compose exec -w /var/www/backend app php artisan migrate

seed:
	docker-compose exec -w /var/www/backend app php artisan db:seed

fresh:
	docker-compose exec -w /var/www/backend app php artisan migrate:fresh --seed

test:
	docker-compose exec -w /var/www/backend app php artisan test

queue:
	docker-compose exec -w /var/www/backend app php artisan queue:work

clear:
	docker-compose exec -w /var/www/backend app php artisan cache:clear
	docker-compose exec -w /var/www/backend app php artisan config:clear
	docker-compose exec -w /var/www/backend app php artisan route:clear
	docker-compose exec -w /var/www/backend app php artisan view:clear

swagger:
	docker-compose exec -w /var/www/backend app php artisan l5-swagger:generate

publish:
	docker-compose exec -w /var/www/backend app php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
	docker-compose exec -w /var/www/backend app php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"

setup: build install publish migrate seed swagger
	@echo ""
	@echo "============================================"
	@echo "Setup complete!"
	@echo "============================================"
	@echo "API is available at: http://localhost:8080"
	@echo "API Documentation: http://localhost:8080/api/documentation"
	@echo "============================================"
