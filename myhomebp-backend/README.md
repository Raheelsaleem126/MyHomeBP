# MyHomeBP Backend API

A comprehensive Laravel-based REST API for the MyHomeBP blood pressure management application, designed for NHS and Private GP Surgery use in the UK.

## Overview

The MyHomeBP App is designed to standardize and digitize home blood pressure monitoring in line with NICE NG136 guidelines and NHS England @Home initiatives. This backend API provides all the necessary endpoints for:

- Patient registration and authentication
- Blood pressure reading recording and validation
- Clinical data management
- Report generation and clinic communication
- Clinic search and management

## Features

### Phase 1 (Current Implementation)
- ✅ Patient registration and authentication (Sanctum)
- ✅ Blood pressure reading recording with NICE guidelines validation
- ✅ Clinical data management
- ✅ Clinic search and management
- ✅ Report generation (PDF)
- ✅ Comprehensive API documentation (Swagger/OpenAPI)
- ✅ Database migrations and seeding

### Key Compliance Features
- **NICE Guidelines (NG136) Compliance**: 
  - HBPM = 2 readings per session, 1 min apart, AM & PM, for 4–7 days
  - Discard Day 1 readings from averages
  - Hypertension diagnosis if average ≥135/85 mmHg at home
  - Hypertensive emergency detection (≥180/120 mmHg)
- **Automated Safety Alerts**: High reading detection with conditional third reading requirements
- **Data Validation**: Comprehensive validation rules for all medical data
- **Secure Authentication**: Laravel Sanctum for API token authentication

## API Endpoints

### Authentication
- `POST /api/auth/register` - Register new patient
- `POST /api/auth/login` - Patient login
- `POST /api/auth/logout` - Patient logout
- `GET /api/auth/me` - Get current patient profile

### Patient Management
- `GET /api/patient/profile` - Get patient profile
- `PUT /api/patient/profile` - Update patient profile
- `GET /api/patient/dashboard` - Get dashboard data
- `POST /api/patient/clinical-data` - Save clinical data
- `GET /api/patient/clinical-data` - Get clinical data

### Blood Pressure
- `POST /api/blood-pressure/record` - Record BP reading
- `GET /api/blood-pressure/readings` - Get BP readings
- `GET /api/blood-pressure/averages` - Get BP averages and trends

### Clinics
- `GET /api/clinics/search` - Search clinics
- `GET /api/clinics/nearby` - Find nearby clinics
- `GET /api/clinics` - Get all clinics
- `GET /api/clinics/{id}` - Get clinic details

### Reports
- `POST /api/reports/generate` - Generate BP report
- `GET /api/reports/summary` - Get report summary
- `GET /api/reports/history` - Get report history
- `GET /api/reports/download/{filename}` - Download report PDF

### System
- `GET /api/health` - Health check endpoint

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- SQLite (or MySQL/PostgreSQL)
- Laravel 12

### Setup

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd myhomebp-backend
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Start the development server**
   ```bash
   php artisan serve
   ```

The API will be available at `http://localhost:8000/api`

## API Documentation

### Swagger UI
Access the interactive API documentation at:
```
http://localhost:8000/api/documentation
```

### Authentication
The API uses Laravel Sanctum for authentication. Include the bearer token in the Authorization header:

```
Authorization: Bearer {your-token}
```

## Database Schema

### Core Tables
- **patients** - Patient information and authentication
- **clinics** - GP surgery and clinic information
- **clinical_data** - Optional clinical information (height, weight, BMI, etc.)
- **blood_pressure_readings** - BP readings with validation and categorization

### Key Features
- Automatic BMI calculation
- NICE guidelines compliance validation
- High reading detection and alerts
- 7-day rolling averages (excluding day 1)

## Testing the API

### 1. Health Check
```bash
curl -X GET "http://localhost:8000/api/health"
```

### 2. Search Clinics
```bash
curl -X GET "http://localhost:8000/api/clinics/search?postcode=SE1"
```

### 3. Register Patient
```bash
curl -X POST "http://localhost:8000/api/auth/register" \
  -H "Content-Type: application/json" \
  -d '{
    "first_name": "John",
    "surname": "Smith",
    "date_of_birth": "1980-03-15",
    "address": "123 Main Street, London",
    "mobile_phone": "07123456789",
    "email": "john.smith@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "clinic_id": 1,
    "terms_accepted": true,
    "data_sharing_consent": true
  }'
```

### 4. Record Blood Pressure
```bash
curl -X POST "http://localhost:8000/api/blood-pressure/record" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {your-token}" \
  -d '{
    "reading_date": "2024-01-15T09:30:00Z",
    "session_type": "am",
    "reading_1_systolic": 120,
    "reading_1_diastolic": 80,
    "reading_1_pulse": 72,
    "reading_2_systolic": 118,
    "reading_2_diastolic": 78,
    "reading_2_pulse": 70
  }'
```

## NICE Guidelines Implementation

### Blood Pressure Categories
- **Optimal**: <120/80 mmHg
- **Normal**: 120-129/80-84 mmHg
- **High Normal**: 130-134/85-89 mmHg
- **Stage 1 Hypertension**: 140-159/90-99 mmHg
- **Stage 2 Hypertension**: 160-179/100-109 mmHg
- **Hypertensive Crisis**: ≥180/110 mmHg

### Safety Features
- **High Reading Detection**: Automatically detects readings ≥180/110
- **Third Reading Requirement**: Mandatory third reading for high values
- **Urgent Advice Alerts**: Automatic alerts for persistently high readings
- **NHS 111 Integration**: Direct guidance for emergency situations

## Security Features

- **Laravel Sanctum**: Secure API token authentication
- **Input Validation**: Comprehensive validation for all medical data
- **Rate Limiting**: API rate limiting to prevent abuse
- **CORS Support**: Cross-origin resource sharing configuration
- **Data Encryption**: Sensitive data encryption at rest

## Development

### Code Structure
```
app/
├── Http/Controllers/Api/     # API Controllers
├── Models/                   # Eloquent Models
├── Services/                 # Business Logic Services
└── Providers/               # Service Providers

database/
├── migrations/              # Database Migrations
└── seeders/                # Database Seeders

routes/
└── api.php                 # API Routes
```

### Key Services
- **ReportService**: Handles PDF report generation and email delivery
- **BloodPressureService**: Manages BP calculations and NICE compliance
- **ValidationService**: Ensures data integrity and medical accuracy

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Submit a pull request

## License

This project is licensed under the MIT License.

## Support

For support and questions, please contact:
- Email: support@247meditech.com
- Documentation: [API Documentation](http://localhost:8000/api/documentation)

## Roadmap

### Phase 2 (Future)
- Desktop/clinic portal
- Secure clinician login
- Live dashboards
- Advanced analytics

### Phase 3 (Future)
- Integration with NHS GP systems (EMIS, SystmOne, Vision)
- NHS Login authentication
- FHIR/HL7-compliant data exchange
- Real-time monitoring and alerts