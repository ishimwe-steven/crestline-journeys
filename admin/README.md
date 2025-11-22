# Admin Panel Setup Instructions

## Initial Setup

1. **Run the database setup script:**
   - Navigate to: `http://your-domain/admin/setup.php`
   - This will create the necessary database tables:
     - `admin_users` - Stores admin login credentials
     - `site_images` - Stores uploaded images
   - A default admin account will be created:
     - **Username:** admin
     - **Password:** admin123
     - ⚠️ **IMPORTANT:** Change this password immediately after first login!

2. **Access the admin panel:**
   - URL: `http://your-domain/admin/login.php`
   - Login with the default credentials above

## Features

### Image Management
- Upload images with title, description, and category
- View all uploaded images in a grid layout
- Delete images
- Categories: General, Destination, Safari, Wildlife, Hotel, Culture

### QR Code Generator
- Generate QR codes with embedded logo
- Store contact information (website link, email, Instagram, phone number)
- Download QR codes for printing/sharing
- Logo automatically embedded from `assets/images/Pacific.png`
- QR codes stored in `assets/qrcodes/` directory

### Account Settings
- Change admin username
- Change admin password
- Requires current password verification
- Password must be at least 6 characters

### Security
- Session-based authentication
- Password hashing (bcrypt)
- Protected admin routes

## File Structure

```
admin/
├── setup.php         - Database setup script (run once)
├── login.php         - Admin login page
├── logout.php        - Logout handler
├── auth_check.php    - Authentication check (included in protected pages)
├── dashboard.php      - Main admin dashboard
├── generate_qr.php    - QR code generator with logo embedding
└── README.md         - This file
```

## Notes

- All uploaded images are stored in `assets/images/uploads/`
- QR codes are stored in `assets/qrcodes/`
- Images are organized by upload date
- The admin panel uses your site's color scheme (green #0c2d1a and gold)
- Make sure the `uploads` and `qrcodes` directories have write permissions (755 or 777)
- QR codes automatically include your site logo from `assets/images/Pacific.png`
- QR code generation uses online API if phpqrcode library is not installed (requires internet connection)

## Troubleshooting

**Can't login?**
- Make sure you've run `setup.php` first
- Check that the database connection is working
- Verify the username and password

**Can't upload images?**
- Check that `assets/images/uploads/` directory exists and has write permissions
- Verify file size limits in PHP (upload_max_filesize, post_max_size)
- Check allowed file types (JPEG, PNG, GIF, WebP)

**Can't generate QR codes?**
- Ensure you have internet connection (uses online API if phpqrcode library not installed)
- Check that `assets/qrcodes/` directory exists and has write permissions
- Verify that `assets/images/Pacific.png` exists (for logo embedding)
- Optional: Install phpqrcode library in `includes/phpqrcode/` for offline QR code generation

