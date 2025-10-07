# 🚀 MyHomeBP API - Complete Development to Production Guide

## 📋 **Overview**
This guide covers the complete workflow from local development to production deployment on EC2, including all the fixes we discovered during our troubleshooting sessions.

---

## 🛠️ **LOCAL DEVELOPMENT WORKFLOW**

### **1. Make Your Changes**
```bash
# Work on your code locally
# Make changes to controllers, models, routes, etc.
```

### **2. Test Locally**
```bash
# Start Laravel development server
php artisan serve

# Test your API endpoints
curl http://localhost:8000/api/health

# Test Swagger documentation
# Visit: http://localhost:8000/api/documentation
```

### **3. Generate Swagger Documentation (Local)**
```bash
# Generate/update Swagger docs for local development
php artisan l5-swagger:generate

# Verify local Swagger works
# Visit: http://localhost:8000/api/documentation
```

### **4. Test Database Changes**
```bash
# Run migrations if you made database changes
php artisan migrate

# Seed data if needed
php artisan db:seed

# Test with fresh data
php artisan migrate:fresh --seed
```

### **5. Commit and Push to Git**
```bash
# Add all changes
git add .

# Commit with descriptive message
git commit -m "Add new feature: [describe your changes]"

# Push to GitHub
git push origin main
```

---

## 🌐 **PRODUCTION DEPLOYMENT WORKFLOW**

### **1. Connect to EC2 Server**
```bash
# SSH into your EC2 server
ssh -i your-key.pem ubuntu@3.250.60.71
```

### **2. Navigate to Project Directory**
```bash
cd /var/www/myhomebp
```

### **3. Run the Deployment Script**
```bash
# Make sure script is executable
chmod +x ec2-deploy.sh

# Run the complete deployment
./ec2-deploy.sh
```

### **4. Verify Deployment**
```bash
# Check if API is working
curl http://3.250.60.71/api/health

# Check if Swagger documentation is working
curl http://3.250.60.71/api/documentation

# Check if API docs JSON is accessible
curl http://3.250.60.71/docs
```

---

## 🔧 **WHAT THE DEPLOYMENT SCRIPT DOES**

The `ec2-deploy.sh` script automatically handles:

### **Git Operations:**
- ✅ Pulls latest changes from GitHub
- ✅ Resets to latest commit

### **Dependencies:**
- ✅ Installs/updates Composer packages
- ✅ Optimizes autoloader for production

### **Environment Configuration:**
- ✅ Generates application key (if missing)
- ✅ Sets `APP_URL=http://3.250.60.71`
- ✅ Sets `L5_SWAGGER_CONST_HOST=http://3.250.60.71/api`

### **Database:**
- ✅ Runs database migrations
- ✅ Seeds database with test data

### **Cache Management:**
- ✅ Clears all Laravel caches
- ✅ Optimizes for production

### **Swagger Documentation (CRITICAL FIXES):**
- ✅ Publishes Swagger UI assets to public directory
- ✅ Copies Swagger assets from vendor to public
- ✅ Generates API documentation with production URLs
- ✅ Creates `storage/api-docs/api-docs.json`

### **Permissions & Services:**
- ✅ Sets proper file permissions
- ✅ Restarts Nginx and PHP-FPM

---

## 🚨 **TROUBLESHOOTING COMMON ISSUES**

### **Issue 1: Swagger 404 Errors**
```bash
# On EC2 server, run:
php artisan l5-swagger:generate

# Or run full deployment:
./ec2-deploy.sh
```

### **Issue 2: Swagger Assets Not Loading**
```bash
# Publish Swagger assets:
php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider" --tag=assets --force

# Copy assets to public directory:
cp -r vendor/swagger-api/swagger-ui/dist/* public/vendor/swagger-api/swagger-ui/dist/
```

### **Issue 3: Wrong URLs in Swagger**
```bash
# Clear config cache and regenerate:
php artisan config:clear
php artisan l5-swagger:generate
```

### **Issue 4: Permission Errors**
```bash
# Fix permissions:
sudo chown -R www-data:www-data storage bootstrap/cache public/vendor
sudo chmod -R 755 storage bootstrap/cache public/vendor
```

---

## 📱 **FOR YOUR ANDROID DEVELOPER**

### **Development URLs:**
- **API Base**: `http://localhost:8000/api`
- **Swagger UI**: `http://localhost:8000/api/documentation`

### **Production URLs:**
- **API Base**: `http://3.250.60.71/api`
- **Swagger UI**: `http://3.250.60.71/api/documentation`

### **Test Credentials:**
```json
{
  "email": "john.smith@email.com",
  "password": "password123"
}
```

---

## 🔄 **QUICK UPDATE WORKFLOW**

For small updates without major changes:

### **Local:**
```bash
# Make changes
# Test locally
php artisan l5-swagger:generate
git add .
git commit -m "Quick fix: [description]"
git push origin main
```

### **Production:**
```bash
# SSH to server
ssh -i your-key.pem ubuntu@3.250.60.71
cd /var/www/myhomebp

# Quick update (if no database changes)
git pull origin main
php artisan config:clear
php artisan l5-swagger:generate
sudo systemctl restart nginx
```

---

## 📊 **VERIFICATION CHECKLIST**

After each deployment, verify:

### **✅ API Endpoints:**
- [ ] Health check: `http://3.250.60.71/api/health`
- [ ] Database test: `http://3.250.60.71/api/db-test`

### **✅ Swagger Documentation:**
- [ ] Swagger UI loads: `http://3.250.60.71/api/documentation`
- [ ] API docs JSON accessible: `http://3.250.60.71/docs`
- [ ] No 404 errors in browser console
- [ ] All API endpoints show correct production URLs

### **✅ Authentication:**
- [ ] Registration works: `POST /api/auth/register`
- [ ] Login works: `POST /api/auth/login`
- [ ] Protected routes require authentication

### **✅ Database:**
- [ ] Migrations ran successfully
- [ ] Test data is seeded
- [ ] All tables exist and have data

---

## 🎯 **PRODUCTION ENVIRONMENT VARIABLES**

Your production `.env` should have:
```env
APP_NAME="MyHomeBP API"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://3.250.60.71

L5_SWAGGER_CONST_HOST=http://3.250.60.71/api

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=myhomebp
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

CORS_ALLOWED_ORIGINS=*
```

---

## 📝 **IMPORTANT NOTES**

1. **Always test locally first** before deploying to production
2. **The deployment script handles Swagger automatically** - no manual steps needed
3. **Database migrations run automatically** during deployment
4. **Swagger documentation is regenerated** with correct production URLs
5. **File permissions are set automatically** by the deployment script
6. **Services are restarted automatically** after deployment

---

## 🎉 **SUCCESS INDICATORS**

Your deployment is successful when:
- ✅ API responds at `http://3.250.60.71/api/health`
- ✅ Swagger UI loads without errors at `http://3.250.60.71/api/documentation`
- ✅ No 404 errors in browser console
- ✅ All API endpoints use production URLs (`http://3.250.60.71/api`)
- ✅ Authentication works with test credentials
- ✅ Database has seeded data

---

**🚀 You're all set! This workflow will save you time and ensure consistent deployments.**
