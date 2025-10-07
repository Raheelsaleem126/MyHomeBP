# EC2 Admin Setup Guide

This guide explains how to set up admin users on your EC2 server after deployment.

## Option 1: Using Database Seeder (Recommended)

Run the database seeder to create admin users:

```bash
# Run all seeders (including admin users)
php artisan db:seed

# Or run only the admin seeder
php artisan db:seed --class=AdminSeeder
```

This will create 3 admin users:
- **Email**: `admin@myhomebp.com` | **Password**: `admin123`
- **Email**: `system@myhomebp.com` | **Password**: `admin123`
- **Email**: `manager@myhomebp.com` | **Password**: `admin123`

## Option 2: Using Artisan Command

Create a single admin user with custom credentials:

```bash
# Create admin with default credentials
php artisan admin:create

# Create admin with custom credentials
php artisan admin:create --name="Your Name" --email="your-email@domain.com" --password="your-password"
```

## Option 3: Manual Creation via Tinker

If you prefer to create admin users manually:

```bash
php artisan tinker
```

Then run:
```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Admin User',
    'email' => 'admin@myhomebp.com',
    'password' => Hash::make('admin123'),
    'role' => 'admin',
    'email_verified_at' => now(),
]);
```

## Testing Admin Login

After creating admin users, test the login:

```bash
curl -X POST "http://your-domain.com/api/admin/login" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin@myhomebp.com",
    "password": "admin123"
  }'
```

## Security Recommendations

1. **Change Default Passwords**: After first login, change the default passwords
2. **Use Strong Passwords**: Use complex passwords for production
3. **Limit Admin Access**: Only create admin users when necessary
4. **Regular Audits**: Regularly review admin user accounts

## Troubleshooting

### If you get "Class not found" errors:
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Regenerate autoload
composer dump-autoload
```

### If database connection fails:
```bash
# Check database configuration
php artisan config:show database

# Test database connection
php artisan db:show
```

### If migrations fail:
```bash
# Run migrations
php artisan migrate

# If you need to refresh the database
php artisan migrate:fresh --seed
```

## Admin Features Available

Once logged in as admin, you can:
- Create, update, and delete doctors
- Create, update, and delete specialities
- Manage clinic-doctor relationships
- Access all admin-protected endpoints
- Use Swagger UI with admin authentication

## API Endpoints for Admins

- `POST /api/admin/login` - Admin login
- `POST /api/admin/logout` - Admin logout
- `GET /api/admin/me` - Get admin profile
- `POST /api/admin/doctors` - Create doctor
- `PUT /api/admin/doctors/{id}` - Update doctor
- `DELETE /api/admin/doctors/{id}` - Delete doctor
- `POST /api/admin/specialities` - Create speciality
- `PUT /api/admin/specialities/{id}` - Update speciality
- `DELETE /api/admin/specialities/{id}` - Delete speciality
