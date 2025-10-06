# 🚀 MyHomeBP API Deployment Guide

## 📋 Prerequisites

1. **GitHub Account** - Your code needs to be on GitHub
2. **Railway Account** - Sign up at [railway.app](https://railway.app)
3. **Domain** (optional) - For custom domain

## 🎯 Quick Deployment (Railway - Recommended)

### Step 1: Push to GitHub
```bash
# Initialize git if not already done
git init
git add .
git commit -m "Initial commit - MyHomeBP API"

# Create repository on GitHub and push
git remote add origin https://github.com/yourusername/myhomebp-api.git
git push -u origin main
```

### Step 2: Deploy on Railway
1. Go to [railway.app](https://railway.app)
2. Click "New Project"
3. Select "Deploy from GitHub repo"
4. Choose your `myhomebp-api` repository
5. Railway will automatically detect Laravel and deploy

### Step 3: Configure Environment Variables
In Railway dashboard, go to Variables tab and add:

```env
APP_NAME="MyHomeBP API"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app.railway.app

DB_CONNECTION=mysql
DB_HOST=containers-us-west-xxx.railway.app
DB_PORT=3306
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=your-db-password

CORS_ALLOWED_ORIGINS=*
```

### Step 4: Add MySQL Database
1. In Railway dashboard, click "New"
2. Select "Database" → "MySQL"
3. Railway will automatically connect it to your app

### Step 5: Deploy
Railway will automatically:
- Install dependencies
- Run migrations
- Seed database
- Generate Swagger docs

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

## 📱 Mobile App Configuration

Once deployed, your Android developer can use:

**Base URL**: `https://your-app.railway.app/api`

**Swagger Documentation**: `https://your-app.railway.app/docs`

**Example API Calls**:
```javascript
// Registration
POST https://your-app.railway.app/api/auth/register

// Login
POST https://your-app.railway.app/api/auth/login

// Get Blood Pressure Readings
GET https://your-app.railway.app/api/blood-pressure/readings
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

1. **Database**: Railway provides MySQL automatically
2. **SSL**: Railway provides HTTPS automatically
3. **CORS**: Configured for mobile app access
4. **Swagger**: Available at `/docs` endpoint
5. **Health Check**: Available at `/api/health`

## 📊 Monitoring

Railway provides:
- Real-time logs
- Performance metrics
- Automatic scaling
- Health monitoring

## 🔄 Updates

To update your API:
1. Push changes to GitHub
2. Railway automatically redeploys
3. Database migrations run automatically

## 🆘 Troubleshooting

### Common Issues:
1. **Database Connection**: Check environment variables
2. **CORS Errors**: Verify CORS configuration
3. **Swagger Not Loading**: Check if `l5-swagger:generate` ran

### Logs:
Check Railway dashboard → Deployments → View Logs

## 📞 Support

- Railway Documentation: [docs.railway.app](https://docs.railway.app)
- Laravel Documentation: [laravel.com/docs](https://laravel.com/docs)
- MyHomeBP API Issues: Create GitHub issue

---

**🎉 Your API will be live at: `https://your-app.railway.app`**
