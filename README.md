# COS Key Register Log

A web-based system for managing community service key registration and borrowing logs with separate admin and borrower interfaces.

## Features

### Admin Panel
- **User Management**: Admin login and signup functionality
- **Data Manipulation**: Create, read, update, and delete key registration records
- **Dashboard**: Manage borrower accounts and key registry logs
- **Authentication**: Secure admin authentication system

### Borrower Interface
- **Key Borrowing**: Log system for borrowers to track key checkouts
- **Account Management**: Borrower login and profile access
- **Request Tracking**: Monitor active key borrow requests

## Project Structure

```
COSKeyRegisterLog/
├── index.html                          # Main entry point
├── index1.css                          # Home page styling
├── database.php                        # Database connection configuration
│
├── Admin Module/
│   ├── adminlogin.php                  # Admin login page
│   ├── adminsignup.php                 # Admin registration page
│   ├── adminlogin&signup1.css          # Admin auth styling
│   ├── adminmanipulation.php           # Admin dashboard & CRUD operations
│   ├── adminmanipulation.css           # Admin dashboard styling
│   └── adminmanipulation1.js           # Admin dashboard functionality
│
├── Borrower Module/
│   ├── borrowerlog.php                 # Borrower login & log interface
│   └── borrowerlog1.css                # Borrower page styling
│
└── assets/
    ├── images/                         # Application images
    └── logos/                          # Company/application logos
```

## Requirements

- **Server**: Apache (XAMPP)
- **PHP**: 5.6+ 
- **Database**: MySQL/MariaDB
- **Browser**: Modern browser with JavaScript enabled

## Installation

1. **Setup Database**
   - Create a new MySQL database
   - Update connection details in `database.php`

2. **Configure Database Connection**
   ```php
   // database.php
   $host = 'localhost';
   $username = 'root';
   $password = '';
   $database = 'cos_key_register';
   ```

3. **Place Files in Web Root**
   - Copy all files to `C:\xampp\htdocs\COSKeyRegisterLog\`

4. **Start Services**
   - Start Apache and MySQL from XAMPP Control Panel

5. **Access Application**
   - Open `http://localhost/COSKeyRegisterLog/` in your browser

## Usage

### For Admins
1. Navigate to admin signup page to create an account
2. Log in with admin credentials
3. Access the admin dashboard to:
   - View all key registration records
   - Add new keys to the system
   - Manage borrower accounts
   - Track borrowing history

### For Borrowers
1. Use borrower login to access the system
2. View available keys
3. Submit borrow requests
4. Track borrowed keys and return dates

## Database Schema

The application requires the following tables (configure in your database):
- `admins` - Admin user accounts
- `borrowers` - Borrower profiles
- `key_registry` - Registered keys in the system
- `borrow_logs` - Key borrowing transaction logs

## File Descriptions

| File | Purpose |
|------|---------|
| `database.php` | MySQL connection and database initialization |
| `adminlogin.php` | Admin authentication form and logic |
| `adminsignup.php` | Admin account registration |
| `adminmanipulation.php` | Admin CRUD interface for key management |
| `adminmanipulation1.js` | Client-side validation and interactivity |
| `borrowerlog.php` | Borrower interface for key logging |
| `index.html` | Application homepage with navigation |

## Features to Consider

- [ ] Email notifications for key requests
- [ ] QR code scanning for key identification
- [ ] Automated return date reminders
- [ ] PDF report generation for audit trails
- [ ] Mobile-responsive design enhancement

## Security Considerations

- Use parameterized queries to prevent SQL injection
- Implement password hashing (bcrypt/password_hash)
- Validate and sanitize all user inputs
- Use HTTPS in production
- Implement session management and timeouts
- Add CSRF token protection

## Support & Maintenance

For issues or questions, please refer to the project documentation or contact the development team.

---

*Last Updated: April 2026*
