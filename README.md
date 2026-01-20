# CourseDetailsExtMEA - MEA Engineering Accountability System

## 📋 Project Overview

**CourseDetailsExtMEA** is a Laravel-based web application that serves as the target system for the MEA (Management of Engineering Accountability) solution. This system receives course details and personnel information from SAP LSO (via integration middleware) and manages engineering accountability and technical governance competency requirements for Generation Engineering within the MEA tool library.

### 🎯 Key Objectives
- Receive and process personnel data from SAP LSO through JMS topics
- Maintain engineering accountability records in MySQL database
- Manage technical governance competency requirements
- Provide REST API for integration with OSB CourseDetailsMEAProvABCSREST
- Serve as a complete MEA system with full CRUD operations
- Audit and error logging for all operations
- API Key authentication for secure integration

### 🔗 Integration Flow
SAP LSO → SAP PO → Integration (OSB) → JMS Topics → CourseDetailsMEAProvABCSREST → CourseDetailsExtMEA (this system)

## 🏗️ System Architecture

### Technology Stack
- Backend: Laravel 10.x (PHP 8.1+)
- Database: MySQL 5.7+ (31 tables with relationships)
- API: RESTful JSON API with API Key authentication
- Frontend: Simple web interface for testing and management
- Deployment: Ready for production (Apache/Nginx)

### Components
1. **Laravel Web Application** - Main MEA system interface
2. **REST API Endpoints** - For receiving data from integration layer
3. **MySQL Database** - Stores personnel, course, and competency data
4. **Web Interface** - For administrators to view and manage data

### Data Flow
1. SAP LSO sends course details to SAP PO
2. SAP PO transforms and sends to Oracle Service Bus (OSB)
3. OSB publishes to JMS topics (`topicCourseDetailsAdHoc` or `topicCourseDetailsTakeOn`) through `CourseDetailsEBS`
4. `CourseDetailsMEAProvABCSREST` consumes from JMS and transforms to ABM
5. This system receives the ABM data via REST API
6. Data is stored in the MySQL database
7. Administrators can view and manage data through web interface

## 📊 Database Schema

The system uses a comprehensive database structure based on XML schemas from the integration:

### Main Entities
- **Personnel** - Employee information and details
- **Organisations** - Company and organizational data
- **Locations** - Geographic and address information
- **VehicleAssetInformation** - Asset and vehicle data
- **Message Structures** - Integration message metadata

### Key Features
- Hierarchical data structure reflecting XML schema relationships
- Foreign key relationships for data integrity
- Audit trails for data changes
- Support for multiple message types (AdHoc and TakeOn)

## 🚀 Getting Started

### Prerequisites
- Prerequisites
- PHP 8.1 or higher
- Composer 2.x
- MySQL 5.7 or higher
- Apache/Nginx web server
- Git

### Installation Steps

#### 1. Clone the repository
```bash
git clone https://github.com/kravhuravhu/CourseDetailsExtMEA
cd CourseDetailsExtMEA
```

#### 2. Instal Dependencies
```bash 
composer install
```
#### 3. Configure Environment
```bash 
cp .env.example .env
```
#### 4. Generate Application Key
```bash
php artisan key:generate
```

#### 5. Set permissions
```bash
chmod -R 775 storage bootstrap/cache
```
#### 6. Access Application
```bash
php artisan serve
# Web Interface: http://localhost:8000
# API Documentation: http://localhost:8000/api/docs
# Test Interface: http://localhost:8000/api-auth-test
```

#### 7. Using API Key(for Integration testing)
```bash
# Include the API key in your requests
# Example: curl command
curl -X GET http://localhost:8000/api/personnel \
-H "Accept: application/json" \
-H "X-API-Key: test-api-key-123"
```

#### 8. Manual Testing Scripts
```bash
# Test API authentication
php scripts/test-auth-simple.php
# Test integration endpoints
php scripts/test-integration.php
# Verify database and models
php public/scripts/verify-models.php
# Test full API
php public/scripts/test-api.php
```

## 🔧 Development Setup

### API Configurations
##### 📡 Core Endpoints

```bash
1. Personnel Management
text
GET    /api/personnel              # List all personnel
POST   /api/personnel              # Create new personnel
GET    /api/personnel/{id}         # Get personnel details
PUT    /api/personnel/{id}         # Update personnel
DELETE /api/personnel/{id}         # Delete personnel
GET    /api/personnel/mrid/{mrid}  # Get by MRID
2. Organisations
text
GET    /api/organisations          # List organisations
POST   /api/organisations          # Create organisation
GET    /api/organisations/{id}     # Get organisation details
PUT    /api/organisations/{id}     # Update organisation
DELETE /api/organisations/{id}     # Delete organisation
3. Locations
text
GET    /api/locations              # List locations
POST   /api/locations              # Create location
GET    /api/locations/{id}         # Get location details
PUT    /api/locations/{id}         # Update location
DELETE /api/locations/{id}         # Delete location
4. Vehicles
text
GET    /api/vehicles               # List vehicles
POST   /api/vehicles               # Create vehicle
GET    /api/vehicles/{id}          # Get vehicle details
PUT    /api/vehicles/{id}          # Update vehicle
DELETE /api/vehicles/{id}          # Delete vehicle
5. Audit Logs
text
GET    /api/audit/logs             # View audit logs
GET    /api/audit/errors           # View error logs
GET    /api/audit/summary          # Get audit summary
6. API Key Management (Public)
text
POST   /api/generate-key           # Generate new API key
GET    /api/validate-key           # Validate API key
GET    /api/list-keys              # List all API keys
```

##### 📈 Monitoring Endpoints
```bash 
GET  /api/integration/health      # Health check
GET  /api/integration/test        # Connectivity test
GET  /api/integration/stats       # Integration statistics
```

##### 🛡️ Security Considerations (future)
- API authentication for integration endpoints

- CSRF protection for web

- Input validation and sanitization

- SQL injection prevention

- XSS protection

- Secure session management

##### 🎯 Application Status
```bash 
Status: ✅ PRODUCTION READY
```
This CourseDetailsExtMEA system is now fully implemented and ready to receive data from the OSB CourseDetailsMEAProvABCSREST project. All integration endpoints are tested and working. The system includes comprehensive audit logging, error handling, and monitoring capabilities for production use.
