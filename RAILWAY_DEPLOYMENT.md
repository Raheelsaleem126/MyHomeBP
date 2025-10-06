# 🚀 Railway Deployment Guide - MyHomeBP API

## ✅ **Project Structure Fixed!**

Your Laravel project is now properly structured in the root directory for Railway deployment.

## 🎯 **Deploy to Railway (5 minutes):**

### Step 1: Go to Railway
1. Visit [railway.app](https://railway.app)
2. Sign up/Login with GitHub
3. Click "New Project"

### Step 2: Deploy from GitHub
1. Select "Deploy from GitHub repo"
2. Choose your `MyHomeBP` repository
3. Railway will automatically detect PHP/Laravel

### Step 3: Add MySQL Database
1. In your project dashboard, click "New"
2. Select "Database" → "MySQL"
3. Railway will automatically connect it to your app

### Step 4: Set Environment Variables
In Railway dashboard → Variables tab, add:

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

### Step 5: Deploy!
Railway will automatically:
- ✅ Install PHP dependencies
- ✅ Run database migrations
- ✅ Seed test data
- ✅ Generate Swagger docs
- ✅ Start the server

## 📱 **For Your Android Developer:**

Once deployed, they'll get:

**🌐 Live API Base URL:** `https://your-app.railway.app/api`  
**📖 Swagger Documentation:** `https://your-app.railway.app/docs`  
**❤️ Health Check:** `https://your-app.railway.app/api/health`  

## 🧪 **Test Data Available:**

**Login Credentials:**
- Email: `alice.johnson@example.com`
- Password: `password123`

**API Endpoints:**
```bash
# Login
POST https://your-app.railway.app/api/auth/login
{
  "email": "alice.johnson@example.com",
  "password": "password123"
}

# Get Blood Pressure Readings
GET https://your-app.railway.app/api/blood-pressure/readings
Authorization: Bearer YOUR_TOKEN

# Get Dashboard Data
GET https://your-app.railway.app/api/patient/dashboard
Authorization: Bearer YOUR_TOKEN
```

## 🔧 **What Railway Will Do:**

1. **Detect PHP/Laravel** automatically
2. **Install Composer dependencies**
3. **Run the start.sh script** which:
   - Generates app key
   - Runs migrations
   - Seeds database
   - Clears caches
   - Generates Swagger docs
   - Starts the server

## 📊 **Monitoring:**

Railway provides:
- ✅ Real-time logs
- ✅ Performance metrics
- ✅ Automatic scaling
- ✅ Health monitoring
- ✅ Automatic HTTPS

## 🚨 **Troubleshooting:**

### If deployment fails:
1. Check Railway logs in dashboard
2. Verify environment variables are set
3. Ensure database is connected

### Common issues:
- **Database connection**: Check DB_* variables
- **App key**: Will be generated automatically
- **CORS errors**: CORS is configured for mobile apps

## 🎉 **Success!**

Once deployed, your API will be live at:
`https://your-app.railway.app`

Your Android developer can immediately start using:
- Swagger documentation for API reference
- Test data for development
- All endpoints working with authentication

---

**🚀 Deploy now and get your API live in minutes!**
