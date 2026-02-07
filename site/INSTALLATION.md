# Aluth Elegance - Installation Guide

## Quick Start (3 Steps)

### Step 1: Setup Web Server

**Option A - Using XAMPP (Recommended for Windows):**
1. Download and install XAMPP from https://www.apachefriends.org/
2. Copy the entire project folder to `C:\xampp\htdocs\`
3. Start Apache and MySQL from XAMPP Control Panel

**Option B - Using PHP Built-in Server:**
```bash
cd path/to/aluth-elegance
php -S localhost:8000
```

### Step 2: Create Database

Open your browser and go to:
```
http://localhost/aluth-elegance/setup.php
```

This will automatically:
- Create the database
- Create all tables
- Insert sample data
- Create admin user

**IMPORTANT:** Delete `setup.php` after successful setup!

### Step 3: Login to Admin

Go to: `http://localhost/aluth-elegance/admin/login.php`

**Default Credentials:**
- Username: `admin`
- Password: `admin123`

## What's Included

✅ Complete e-commerce website
✅ Admin panel with full CRUD operations
✅ Product management
✅ Order management
✅ Shopping cart
✅ Responsive design

## Admin Panel Features

### Dashboard
- View total products, orders, revenue
- See recent orders
- Quick statistics

### Products Management
- Add new products
- Edit existing products
- Update prices and stock
- Delete products
- Toggle featured status
- Manage categories

### Orders Management
- View all orders
- Update order status
- View customer details

### Settings
- Change admin password
- Secure authentication

## Troubleshooting

### Database Connection Error
1. Check MySQL is running
2. Verify credentials in `config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'aluth_elegance');
   ```

### Images Not Showing
1. Create `images` folder in root
2. Add your product images
3. Update image paths in admin panel

### Can't Login
1. Make sure you ran `setup.php` first
2. Default credentials: admin / admin123
3. Check database has admin_users table

## Adding Products

1. Login to admin panel
2. Go to "Products"
3. Click "Add New Product"
4. Fill in:
   - Product Name
   - Description
   - Price
   - Category (Tile Basins, Accessories, Home Accents)
   - Image path (e.g., `images/product.jpg`)
   - Stock quantity
   - Featured checkbox (to show on homepage)
5. Click "Save Product"

## Customization

### Change Colors
Edit `css/style.css`:
```css
:root {
    --primary-color: #D4AF37;  /* Gold */
    --secondary-color: #2C3E50; /* Dark blue */
}
```

### Change Logo
Edit header in HTML files:
```html
<h1>Aluth<span>Elegance</span></h1>
```

### Add More Categories
1. Go to admin panel
2. Add products with new category names
3. Categories will appear automatically

## Security Recommendations

1. **Change default password immediately**
2. **Delete setup.php after installation**
3. Use strong passwords
4. Enable HTTPS in production
5. Keep PHP and MySQL updated
6. Backup database regularly

## File Structure

```
aluth-elegance/
├── admin/              # Admin panel
│   ├── index.php       # Dashboard
│   ├── products.php    # Product management
│   ├── orders.php      # Order management
│   ├── settings.php    # Settings
│   ├── login.php       # Login page
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
│   └── main.js         # Main JS
├── index.html          # Homepage
├── shop.html           # Shop page
├── collections.html    # Collections page
├── about.html          # About page
├── contact.html        # Contact page
├── setup.php           # Database setup (delete after use)
└── README.md           # Documentation
```

## Support

For issues or questions:
1. Check this installation guide
2. Review README.md
3. Check database connection settings
4. Ensure all files are uploaded correctly

## Next Steps

1. Add your product images to `images/` folder
2. Add your products via admin panel
3. Customize colors and branding
4. Test the shopping cart
5. Configure email settings (if needed)
6. Deploy to production server

Enjoy your new e-commerce website! 🎉
