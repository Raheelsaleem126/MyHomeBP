# 🚀 MyHomeBP API – Full Deployment Guide (EC2)

A complete step-by-step guide to deploy the **MyHomeBP Laravel API** with **Swagger documentation** on your EC2 server.

---

## 🧩 1. Connect to EC2 Instance

```bash
ssh -i /path/to/your-key.pem ubuntu@<your-ec2-ip>
Example:

bash
Copy code
ssh -i ~/.ssh/myhomebp.pem ubuntu@3.250.60.71
📦 2. Navigate to Project Directory
bash
Copy code
cd /var/www/myhomebp
🔁 3. Pull the Latest Code from Git
bash
Copy code
git fetch origin
git reset --hard origin/main
💡 Replace main with your active branch name if different (e.g., master, develop).

⚙️ 4. Install Dependencies
bash
Copy code
composer install --no-dev --optimize-autoloader
🔑 5. Set Environment Variables
If .env doesn’t exist yet:

bash
Copy code
cp .env.example .env
nano .env
Update all required values (DB credentials, APP_URL, etc.).

🔐 6. Generate Application Key
bash
Copy code
php artisan key:generate --no-interaction
🗄️ 7. Run Database Migrations
bash
Copy code
php artisan migrate --force
🌱 8. Seed the Database
bash
Copy code
php artisan db:seed --force
🧹 9. Clear & Rebuild Cache
bash
Copy code
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
📚 10. Setup Swagger (L5 Swagger)
10.1 Publish Swagger UI Assets
bash
Copy code
php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider" --tag=assets --force
10.2 Fix Swagger Permissions
bash
Copy code
sudo chown -R ubuntu:www-data /var/www/myhomebp/storage/api-docs
sudo chown -R ubuntu:www-data /var/www/myhomebp/public/vendor/swagger-api
sudo chmod -R 775 /var/www/myhomebp/storage/api-docs
sudo chmod -R 775 /var/www/myhomebp/public/vendor/swagger-api
10.3 Generate Swagger Docs
bash
Copy code
php artisan l5-swagger:generate --force
🧰 11. Set Laravel Folder Permissions
bash
Copy code
sudo chown -R ubuntu:www-data /var/www/myhomebp
sudo chmod -R 755 /var/www/myhomebp/storage
sudo chmod -R 755 /var/www/myhomebp/bootstrap/cache
🔁 12. Restart PHP & Nginx Services (if required)
bash
Copy code
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
⚠️ Adjust PHP version (php8.2-fpm) according to your setup.

🌐 13. Verify Deployment
Check	URL
✅ App	http://3.250.60.71
📖 Swagger UI	http://3.250.60.71/api/documentation

⚡ 14. Optional: One-Command Auto Deployment Script
You can automate all above steps using a deploy script.

Create the Script
bash
Copy code
nano /var/www/myhomebp/deploy.sh
Paste This:
bash
Copy code
#!/bin/bash
set -e

echo "🚀 Deploying MyHomeBP API..."

cd /var/www/myhomebp

echo "🔁 Pulling latest code..."
git fetch origin
git reset --hard origin/main

echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader

echo "🔑 Generating key..."
php artisan key:generate --no-interaction

echo "🗄️ Running migrations..."
php artisan migrate --force

echo "🌱 Seeding database..."
php artisan db:seed --force

echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "📦 Publishing Swagger UI assets..."
php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider" --tag=assets --force

echo "🔐 Fixing permissions..."
sudo chown -R ubuntu:www-data storage/api-docs public/vendor/swagger-api
sudo chmod -R 775 storage/api-docs public/vendor/swagger-api

echo "📚 Generating Swagger docs..."
php artisan l5-swagger:generate --force

echo "✅ Deployment completed successfully!"
echo "🌐 Visit: http://3.250.60.71/api/documentation"
Make It Executable
bash
Copy code
chmod +x /var/www/myhomebp/deploy.sh
Run Anytime
bash
Copy code
bash /var/www/myhomebp/deploy.sh
🧾 Summary
Step	Description
1️⃣	SSH into EC2
2️⃣	Pull latest Git code
3️⃣	Install Composer dependencies
4️⃣	Run migrations & seeds
5️⃣	Clear cache
6️⃣	Publish & generate Swagger docs
7️⃣	Fix permissions
8️⃣	Restart PHP & Nginx
✅	Done! Visit /api/documentation

Author: Deployment guide for MyHomeBP API
Maintainer: Raheel Saleem
Server: EC2 Ubuntu (Nginx + PHP-FPM)
Last Updated: October 2025