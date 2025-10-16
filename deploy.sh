#!/bin/bash

# MyHomeBP API Deployment Script
echo "🚀 Deploying MyHomeBP API..."

cd /var/www/myhomebp || { echo "❌ Project directory not found!"; exit 1; }

# Ensure proper ownership before anything else
echo "👤 Fixing ownership..."
sudo chown -R ubuntu:www-data /var/www/myhomebp

# Set permissions for writable directories early
echo "🔐 Setting initial permissions..."
sudo chmod -R 775 storage bootstrap/cache public/vendor

# Pull latest code (if applicable)
if [ -d .git ]; then
  echo "📥 Pulling latest code..."
  git pull origin main || echo "⚠️ Git pull failed (check permissions or branch name)"
fi

# Install dependencies
echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader

# Generate application key (only if not set)
if ! grep -q "APP_KEY=" .env || [ -z "$(grep 'APP_KEY=' .env | cut -d '=' -f2)" ]; then
  echo "🔑 Generating application key..."
  php artisan key:generate --no-interaction
else
  echo "🔑 Application key already exists, skipping..."
fi

# Run migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Seed database
echo "🌱 Seeding database..."
php artisan db:seed --force

# Clear caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Publish Swagger assets
echo "📦 Publishing Swagger UI assets..."
php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider" --tag=assets --force

# Generate Swagger documentation
echo "📚 Generating API documentation..."
php artisan l5-swagger:generate

# Fix final permissions
echo "🔐 Finalizing permissions..."
sudo chown -R ubuntu:www-data /var/www/myhomebp
sudo chmod -R 775 /var/www/myhomebp/storage /var/www/myhomebp/bootstrap/cache /var/www/myhomebp/public/vendor

echo "✅ Deployment completed successfully!"
echo "🌐 Your API is ready!"
echo "📖 Swagger UI: http://3.250.60.71/api/documentation"
echo "📚 API Docs: http://3.250.60.71/docs"
