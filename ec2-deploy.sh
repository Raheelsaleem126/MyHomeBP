#!/bin/bash

# MyHomeBP API EC2 Deployment Script
echo "🚀 Deploying MyHomeBP API to EC2..."

# Navigate to project directory
cd /var/www/myhomebp

# Pull latest changes
echo "📥 Pulling latest changes from Git..."
git fetch origin
git reset --hard origin/main

# Install/Update dependencies
echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader

# Generate application key (if not exists)
echo "🔑 Ensuring application key exists..."
if [ -z "$(grep APP_KEY .env)" ] || [ "$(grep APP_KEY .env | cut -d '=' -f2)" = "" ]; then
    php artisan key:generate --no-interaction
fi

# Set production APP_URL if not already set
echo "🌐 Setting production APP_URL..."
if ! grep -q "APP_URL=http://3.250.60.71" .env; then
    sed -i 's|APP_URL=.*|APP_URL=http://3.250.60.71|' .env
fi

# Set L5_SWAGGER_CONST_HOST for production Swagger docs
echo "📚 Setting Swagger host URL..."
if ! grep -q "L5_SWAGGER_CONST_HOST=" .env; then
    echo "L5_SWAGGER_CONST_HOST=http://3.250.60.71/api" >> .env
else
    sed -i 's|L5_SWAGGER_CONST_HOST=.*|L5_SWAGGER_CONST_HOST=http://3.250.60.71/api|' .env
fi

# Run database migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Clear all caches
echo "🧹 Clearing all caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

# Publish Swagger assets (CRITICAL for EC2)
echo "📦 Publishing Swagger UI assets..."
mkdir -p public/vendor/swagger-api/swagger-ui/dist
php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider" --tag=assets --force

# Copy Swagger UI assets to public directory
echo "📋 Copying Swagger UI assets..."
cp -r vendor/swagger-api/swagger-ui/dist/* public/vendor/swagger-api/swagger-ui/dist/

# Generate Swagger documentation
echo "📚 Generating API documentation..."
php artisan l5-swagger:generate

# Set proper permissions
echo "🔐 Setting permissions..."
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
chown -R www-data:www-data public/vendor
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 755 public/vendor

# Optimize for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
echo "🔄 Restarting services..."
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm

echo "✅ EC2 Deployment completed successfully!"
echo "🌐 Your API is ready!"
echo "📖 Swagger UI: http://3.250.60.71/api/documentation"
echo "📚 API Docs: http://3.250.60.71/docs"
