# 🚀 MyHomeBP API Deployment Guide

## 📋 Prerequisites

1. **GitHub Account** - Your code needs to be on GitHub
2. **Server Access** - EC2, VPS, or similar hosting environment
3. **Domain** (optional) - For custom domain

## 🎯 Deployment Options

### Option 1: EC2 Deployment (Recommended)

See the detailed [EC2_DEPLOYMENT.md](EC2_DEPLOYMENT.md) guide for step-by-step EC2 deployment instructions.

### Option 2: Generic VPS Deployment

#### Step 1: Server Setup
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y nginx php8.2-fpm php8.2-cli php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip composer git

# Install MySQL
sudo apt install -y mysql-server
```

#### Step 2: Deploy Application
```bash
# Clone repository
git clone https://github.com/yourusername/MyHomeBP.git
cd MyHomeBP

# Install dependencies
composer install --no-dev --optimize-autoloader

# Configure environment
cp .env.example .env
nano .env
```

#### Step 3: Database Setup
```bash
# Create database and user
sudo mysql -u root -p
CREATE DATABASE myhomebp;
CREATE USER 'myhomebp_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON myhomebp.* TO 'myhomebp_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Run migrations and seed
php artisan migrate --seed
```

#### Step 4: Configure Web Server
Set up Nginx or Apache to serve your Laravel application.

#### Step 5: Run Deployment Script
```bash
# Make executable and run
chmod +x deploy.sh
./deploy.sh
```

## 🌐 Alternative Hosting Options

### Render.com
1. Connect GitHub repository
2. Select "Web Service"
3. Build Command: `composer install --no-dev --optimize-autoloader`
4. Start Command: `php artisan serve --host=0.0.0.0 --port=$PORT`

### Heroku
1. Install Heroku CLI
2. Create Heroku app: `heroku create myhomebp-api`
3. Add MySQL addon: `heroku addons:create cleardb:ignite`
4. Deploy: `git push heroku main`

### DigitalOcean App Platform
1. Connect GitHub repository
2. Select PHP as runtime
3. Configure build and run commands
4. Add MySQL database service

## 📱 Mobile App Configuration

Once deployed, your Android developer can use:

**Base URL**: `https://your-domain.com/api`

**Swagger Documentation**: `https://your-domain.com/docs`

**Example API Calls**:
```javascript
// Registration
POST https://your-domain.com/api/auth/register

// Login
POST https://your-domain.com/api/auth/login

// Get Blood Pressure Readings
GET https://your-domain.com/api/blood-pressure/readings
Authorization: Bearer YOUR_TOKEN
```

## 🔧 Environment Variables

### Required Variables:
- `APP_KEY` - Laravel application key
- `DB_HOST` - Database host
- `DB_DATABASE` - Database name
- `DB_USERNAME` - Database username
- `DB_PASSWORD` - Database password

### Optional Variables:
- `APP_URL` - Your app URL
- `CORS_ALLOWED_ORIGINS` - CORS origins (use `*` for development)

## 🚨 Important Notes

1. **Database**: Ensure MySQL/PostgreSQL is properly configured
2. **SSL**: Use HTTPS in production
3. **CORS**: Configured for mobile app access
4. **Swagger**: Available at `/docs` endpoint
5. **Health Check**: Available at `/api/health`

## 📊 Monitoring

Your hosting provider should provide:
- Real-time logs
- Performance metrics
- Health monitoring
- SSL certificates

## 🔄 Updates

To update your API:
1. Push changes to GitHub
2. Pull changes on server
3. Run deployment script
4. Database migrations run automatically

## 🆘 Troubleshooting

### Common Issues:
1. **Database Connection**: Check environment variables
2. **CORS Errors**: Verify CORS configuration
3. **Swagger Not Loading**: Check if `l5-swagger:generate` ran
4. **File Permissions**: Ensure proper permissions on storage and cache directories

### Logs:
Check your hosting provider's log viewer or server logs directly.

## 📞 Support

- Laravel Documentation: [laravel.com/docs](https://laravel.com/docs)
- MyHomeBP API Issues: Create GitHub issue

---

**🎉 Your API will be live at: `https://your-domain.com`**