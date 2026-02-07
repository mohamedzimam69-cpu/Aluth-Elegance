# Aluth Elegance - E-Commerce Website

A complete e-commerce website for selling luxury tile basins and home accents.

## Features

### Frontend
- Modern, elegant design with gold accents
- Responsive layout for all devices
- Product catalog with categories
- Shopping cart functionality
- Contact form

### Backend (PHP + MySQL)
- Product management
- Order processing
- Category management
- Admin authentication

### Admin Panel
- Easy-to-use dashboard
- Product CRUD operations
- Order management with status updates
- Statistics overview

## Installation

### 1. Database Setup
```sql
-- Import the database schema
mysql -u root -p < database/schema.sql
```

Or manually:
1. Open phpMyAdmin
2. Create database `aluth_elegance`
3. Import `database/schema.sql`

### 2. Configuration
Edit `config/database.php` if needed:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'aluth_elegance');
```

### 3. Web Server Setup

#### Using XAMPP/WAMP:
1. Copy project to `htdocs` folder
2. Start Apache and MySQL
3. Access: `http://localhost/aluth-elegance`

#### Using PHP Built-in Server:
```bash
php -S localhost:8000
```

## Admin Access

**URL:** `http://localhost/aluth-elegance/admin/login.php`

**Default Credentials:**
- Username: `admin`
- Password: `admin123`

## Project Structure

```
aluth-elegance/
├── admin/              # Admin panel
│   ├── index.php       # Dashboard
│   ├── products.php    # Product management
│   ├── orders.php      # Order management
│   ├── login.php       # Admin login
│   └── logout.php      # Logout
├── api/                # API endpoints
│   ├── products.php    # Products API
│   └── orders.php      # Orders API
├── config/             # Configuration
│   └── database.php    # Database connection
├── css/                # Stylesheets
│   ├── style.css       # Main styles
│   └── admin.css       # Admin styles
├── database/           # Database files
│   └── schema.sql      # Database schema
├── images/             # Product images
├── js/                 # JavaScript
│   └── main.js         # Main JS file
├── index.html          # Homepage
├── shop.html           # Shop page
├── contact.html        # Contact page
└── README.md           # This file
```

## Usage

### Adding Products
1. Login to admin panel
2. Go to Products
3. Click "Add New Product"
4. Fill in details and submit

### Managing Orders
1. Go to Orders in admin panel
2. View all orders
3. Update order status using dropdown

### Frontend Shopping
1. Browse products on homepage
2. Click "Add to Cart"
3. View cart (icon in header)
4. Proceed to checkout

## Customization

### Colors
Edit `css/style.css`:
```css
:root {
    --primary-color: #D4AF37;  /* Gold */
    --secondary-color: #2C3E50; /* Dark blue */
}
```

### Logo
Replace text in header:
```html
<h1>Aluth<span>Elegance</span></h1>
```

### Images
Add product images to `images/` folder and reference in database.

## Security Notes

1. Change default admin password immediately
2. Use strong passwords
3. Enable HTTPS in production
4. Validate all user inputs
5. Keep PHP and MySQL updated

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## License

© 2026 Aluth Elegance. All rights reserved.
