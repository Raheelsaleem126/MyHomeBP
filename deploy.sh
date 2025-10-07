#!/bin/bash

# MyHomeBP API Deployment Script
echo "🚀 Deploying MyHomeBP API..."

# Install dependencies
echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader

# Generate application key
echo "🔑 Generating application key..."
php artisan key:generate --no-interaction

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

# Set permissions
echo "🔐 Setting permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

echo "✅ Deployment completed successfully!"
echo "🌐 Your API is ready!"
echo "📖 Swagger UI: http://3.250.60.71/api/documentation"
echo "📚 API Docs: http://3.250.60.71/docs"
