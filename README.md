# Barangay Appointment System

A comprehensive PHP-based appointment management system for barangay offices.

## Features

- **User Management**

  - User registration and authentication
  - Role-based access control (Admin, Staff, Resident)
  - User profile management

- **Appointment Management**

  - Create, view, update, and cancel appointments
  - Time slot management
  - Appointment status tracking (Pending, Approved, Completed, Cancelled, Rejected)
  - Queue number generation

- **Service Management**

  - Manage barangay services
  - Service requirements and fees
  - Service categorization

- **Admin Dashboard**

  - Appointment statistics
  - User management
  - Service management
  - Reports and analytics

- **Notifications**

  - System notifications for users
  - Appointment status updates

- **Audit Logging**
  - Track all system activities
  - User action logging

## System Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- PDO PHP Extension

## Installation

1. **Clone or download the project**

   ```bash
   cd "c:\Sto Tomas"
   ```

2. **Create database**

   - Import the SQL schema from `database/schema.sql`
   - Or run the following commands:

   ```sql
   mysql -u root -p < database/schema.sql
   ```

3. **Configure database connection**

   - Edit `config/database.php`
   - Update database credentials:

   ```php
   private $host = "localhost";
   private $db_name = "barangay_appointment";
   private $username = "root";
   private $password = "";
   ```

4. **Configure site settings**

   - Edit `config/config.php`
   - Update SITE_URL to your local URL

5. **Set up uploads directory**
   ```bash
   mkdir uploads
   chmod 755 uploads
   ```

## Default Login Credentials

- **Admin Account**
  - Username: `admin`
  - Password: `admin123`

## Project Structure

```
c:\Sto Tomas\
├── assets/
│   ├── css/
│   │   └── style.css          # Main stylesheet
│   └── js/
│       └── script.js          # JavaScript functions
├── config/
│   ├── database.php           # Database connection
│   └── config.php             # Site configuration
├── controllers/
│   ├── AuthController.php     # Authentication logic
│   └── AppointmentController.php  # Appointment logic
├── database/
│   └── schema.sql             # Database schema
├── includes/
│   └── helpers.php            # Helper functions
├── models/
│   ├── User.php               # User model
│   ├── Service.php            # Service model
│   └── Appointment.php        # Appointment model
├── views/
│   ├── includes/
│   │   ├── admin_header.php
│   │   ├── admin_sidebar.php
│   │   └── user_header.php
│   ├── admin/
│   │   └── dashboard.php      # Admin dashboard
│   ├── user/
│   │   └── dashboard.php      # User dashboard
│   ├── login.php              # Login page
│   └── register.php           # Registration page
├── uploads/                   # File uploads directory
└── index.php                  # Main entry point
```

## Database Schema

### Main Tables

- **users** - User accounts and profiles
- **services** - Barangay services
- **appointments** - Appointment records
- **documents** - Uploaded documents
- **notifications** - System notifications
- **audit_log** - Activity logging
- **settings** - System settings

## Usage

### For Residents

1. Register an account
2. Login to the system
3. Browse available services
4. Create an appointment
5. Track appointment status

### For Admin/Staff

1. Login with admin credentials
2. View and manage appointments
3. Approve/reject appointments
4. Manage services
5. Generate reports

## Security Features

- Password hashing using bcrypt
- Session management
- SQL injection prevention (PDO prepared statements)
- XSS prevention
- CSRF protection (to be implemented)
- Role-based access control

## Future Enhancements

- Email notifications
- SMS notifications
- PDF certificate generation
- Payment integration
- Calendar view
- Advanced reporting
- Mobile app

## Development

To extend the system:

1. Add new models in `models/` directory
2. Create controllers in `controllers/` directory
3. Add views in `views/` directory
4. Update database schema as needed

## Copyright Notice

Copyright © 2026 Aldrian Loberiano.  
This project is intended strictly for personal use.  
Any unauthorized commercial use, distribution, or copying of the code or materials in this repository is prohibited and may constitute copyright infringement.

## Support

For issues and questions, please contact your system administrator.
