# CourseDetailsExtMEA - MEA Engineering Accountability System

## 📋 Project Overview

**CourseDetailsExtMEA** is a Laravel-based web application that serves as the target system for the MEA (Management of Engineering Accountability) solution. This system receives course details and personnel information from SAP LSO (via integration middleware) and manages engineering accountability and technical governance competency requirements for Generation Engineering within the MEA tool library.

### 🎯 Key Objectives
- Receive and process course details from SAP LSO through JMS topics
- Maintain engineering accountability records
- Manage technical governance competency requirements
- Provide a web interface for viewing and managing competency data
- Serve as a mock MEA system for integration testing

### 🔗 Integration Flow
SAP LSO → SAP PO → Integration (OSB) → JMS Topics → CourseDetailsMEAProvABCSREST → CourseDetailsExtMEA (this system)

## 🏗️ System Architecture

### Components
1. **Laravel Web Application** - Main MEA system interface
2. **REST API Endpoints** - For receiving data from integration layer
3. **MySQL Database** - Stores personnel, course, and competency data
4. **Web Interface** - For administrators to view and manage data

### Data Flow
1. SAP LSO sends course details to SAP PO
2. SAP PO transforms and sends to Oracle Service Bus (OSB)
3. OSB publishes to JMS topics (`topicCourseDetailsAdHoc` or `topicCourseDetailsTakeOn`)
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
- PHP 8.0 or higher
- Composer
- MySQL 5.7 or higher
- Laravel 9.x or 10.x
- Web server (Apache/Nginx)

### Installation Steps
1. Clone the repository
2. Configure database connection
3. Run database migrations (when available)
4. Install dependencies with Composer
5. Configure environment variables
6. Start the application

## 📁 Project Structure (Planned)
app/
├── Console/
├── Exceptions/
├── Http/
│ ├── Controllers/
│ │ ├── Api/
│ │ │ ├── CourseController.php
│ │ │ ├── PersonnelController.php
│ │ │ └── IntegrationController.php
│ │ └── Web/
│ │ ├── DashboardController.php
│ │ ├── PersonnelController.php
│ │ └── ReportsController.php
│ ├── Middleware/
│ └── Requests/
├── Models/
│ ├── Personnel/
│ ├── Organisations/
│ ├── Locations/
│ └── Integration/
├── Services/
│ ├── Integration/
│ ├── Validation/
│ └── Transformation/
└── Listeners/

## 🔧 Development Setup

### Environment Configuration
```bash
📡 API Endpoints (Planned)
Integration Endpoints
POST /api/integration/course-details - Receive course details from integration

POST /api/integration/personnel-details - Receive personnel details

GET /api/integration/status - Check integration status

Web Application Endpoints
GET / - Dashboard

GET /personnel - Personnel listing

GET /personnel/{id} - Personnel details

GET /courses - Course listing

GET /reports - Reporting interface

🛡️ Security Considerations
API authentication for integration endpoints

CSRF protection for web forms

Input validation and sanitization

SQL injection prevention

XSS protection

Secure session management

🧪 Testing Strategy
Test Types
Unit Tests - Individual components and services

Integration Tests - API endpoints and database interactions

Feature Tests - User interface and workflows

Integration Flow Tests - End-to-end data flow testing

Test Data
Sample XML messages from SAP LSO

Mock JMS messages

Test database with sample records

📈 Future Enhancements
Phase 1 (MVP)
Basic data reception and storage

Simple web interface for data viewing

Basic reporting

Phase 2
Advanced search and filtering

Export functionality

Email notifications

Dashboard analytics

Phase 3
Advanced reporting and analytics

Integration with other systems

Mobile responsive design

Advanced security features

🤝 Contributing
Fork the repository

Create a feature branch

Make your changes

Write or update tests

Submit a pull request