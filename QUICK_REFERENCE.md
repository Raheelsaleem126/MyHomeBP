# 🚀 MyHomeBP API - Quick Reference

## 📱 **Development Commands**

```bash
# Start local server
php artisan serve

# Generate Swagger docs (local)
php artisan l5-swagger:generate

# Test API
curl http://localhost:8000/api/health

# Git workflow
git add .
git commit -m "Your message"
git push origin main
```

## 🌐 **Production Commands**

```bash
# SSH to server
ssh -i your-key.pem ubuntu@3.250.60.71

# Navigate to project
cd /var/www/myhomebp

# Full deployment
./ec2-deploy.sh

# Quick update (no DB changes)
git pull origin main
php artisan config:clear
php artisan l5-swagger:generate
sudo systemctl restart nginx
```

## 🔧 **Troubleshooting Commands**

```bash
# Fix Swagger 404
php artisan l5-swagger:generate

# Fix Swagger assets
php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider" --tag=assets --force

# Fix permissions
sudo chown -R www-data:www-data storage bootstrap/cache public/vendor
sudo chmod -R 755 storage bootstrap/cache public/vendor

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## 📊 **Verification URLs**

### Local Development:
- Health: `http://localhost:8000/api/health`
- Swagger: `http://localhost:8000/api/documentation`

### Production:
- Health: `http://3.250.60.71/api/health`
- Swagger: `http://3.250.60.71/api/documentation`
- API Docs: `http://3.250.60.71/docs`

## 🔑 **Test Credentials**
```json
{
  "email": "john.smith@email.com",
  "password": "password123"
}
```

## 🎯 **Key Files**
- `ec2-deploy.sh` - Production deployment script
- `config/l5-swagger.php` - Swagger configuration
- `app/Http/Controllers/Controller.php` - API documentation
- `storage/api-docs/api-docs.json` - Generated API docs

---
**💡 Tip: Bookmark this page for quick access to common commands!**
