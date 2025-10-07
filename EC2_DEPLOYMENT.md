# 🚀 MyHomeBP API - EC2 Deployment Guide

## ⚠️ Important: Swagger Assets Issue

**CRITICAL**: When you deploy to EC2 via `git fetch origin`, the Swagger UI assets will NOT work initially because:
- The `public/vendor/swagger-api/swagger-ui/dist/` directory is not in git (correctly ignored)
- These assets need to be generated during deployment

## 🛠️ EC2 Deployment Steps

### 1. Initial Server Setup
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y nginx php8.2-fpm php8.2-cli php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip composer git

# Create project directory
sudo mkdir -p /var/www/html
sudo chown -R $USER:$USER /var/www/html
cd /var/www/html

# Clone your repository
git clone https://github.com/yourusername/MyHomeBP.git
cd MyHomeBP
```

### 2. Configure Environment
```bash
# Copy environment file
cp .env.example .env

# Edit environment variables
nano .env
```

**Required .env settings for EC2:**
```env
APP_NAME="MyHomeBP API"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=myhomebp
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# CORS for mobile app
CORS_ALLOWED_ORIGINS=*
```

### 3. Database Setup
```bash
# Install MySQL
sudo apt install -y mysql-server

# Create database
sudo mysql -u root -p
CREATE DATABASE myhomebp;
CREATE USER 'myhomebp_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON myhomebp.* TO 'myhomebp_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 4. Run Deployment Script
```bash
# Make script executable
chmod +x ec2-deploy.sh

# Run deployment
./ec2-deploy.sh
```

### 5. Configure Nginx
Create `/etc/nginx/sites-available/myhomebp`:
```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/html/MyHomeBP/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/myhomebp /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### 6. SSL Certificate (Let's Encrypt)
```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Get SSL certificate
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

## 🔄 Updating Your Application

### Method 1: Using the Deployment Script (Recommended)
```bash
cd /var/www/html/MyHomeBP
git fetch origin
git reset --hard origin/main
./ec2-deploy.sh
```

### Method 2: Manual Update
```bash
cd /var/www/html/MyHomeBP

# Pull changes
git fetch origin
git reset --hard origin/main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Run migrations
php artisan migrate --force

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# CRITICAL: Publish Swagger assets
mkdir -p public/vendor/swagger-api/swagger-ui/dist
php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider" --tag=assets --force
cp -r vendor/swagger-api/swagger-ui/dist/* public/vendor/swagger-api/swagger-ui/dist/

# Generate Swagger documentation
php artisan l5-swagger:generate

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache public/vendor
sudo chmod -R 755 storage bootstrap/cache public/vendor

# Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
```

## 🧪 Testing Your Deployment

### 1. Test API Endpoints
```bash
# Health check
curl https://your-domain.com/api/health

# Swagger documentation
curl https://your-domain.com/docs

# Swagger UI
curl https://your-domain.com/api/documentation
```

### 2. Test Swagger Assets
```bash
# Test if assets are accessible
curl -I https://your-domain.com/docs/asset/swagger-ui.css
curl -I https://your-domain.com/docs/asset/swagger-ui-bundle.js
```

Both should return `HTTP 200 OK`.

## 🚨 Troubleshooting

### Swagger UI Not Loading
If you see `ERR_CONNECTION_CLOSED` or `SwaggerUIBundle is not defined`:

1. **Check if assets exist:**
   ```bash
   ls -la /var/www/html/MyHomeBP/public/vendor/swagger-api/swagger-ui/dist/
   ```

2. **Re-publish assets:**
   ```bash
   cd /var/www/html/MyHomeBP
   php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider" --tag=assets --force
   cp -r vendor/swagger-api/swagger-ui/dist/* public/vendor/swagger-api/swagger-ui/dist/
   ```

3. **Check permissions:**
   ```bash
   sudo chown -R www-data:www-data public/vendor
   sudo chmod -R 755 public/vendor
   ```

### Database Connection Issues
```bash
# Check MySQL status
sudo systemctl status mysql

# Test connection
mysql -u myhomebp_user -p myhomebp
```

### Nginx Issues
```bash
# Check Nginx configuration
sudo nginx -t

# Check Nginx status
sudo systemctl status nginx

# View error logs
sudo tail -f /var/log/nginx/error.log
```

## 📱 Mobile App Configuration

Once deployed, your Android developer can use:

**Base URL**: `https://your-domain.com/api`
**Swagger Documentation**: `https://your-domain.com/api/documentation`

## 🔒 Security Considerations

1. **Firewall**: Configure UFW to allow only necessary ports
2. **Database**: Use strong passwords and limit access
3. **SSL**: Always use HTTPS in production
4. **Updates**: Keep system and dependencies updated

## 📊 Monitoring

- **Logs**: Check `/var/log/nginx/access.log` and `/var/log/nginx/error.log`
- **Laravel Logs**: Check `storage/logs/laravel.log`
- **System Resources**: Use `htop` or `top` to monitor resource usage

---

**✅ Your API will be live at: `https://your-domain.com`**
**📖 Swagger UI: `https://your-domain.com/api/documentation`**
