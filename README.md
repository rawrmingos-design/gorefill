# 🚰 GoRefill - E-Commerce Platform (PHP Native)

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-orange)](https://www.mysql.com/)
[![License](https://img.shields.io/badge/License-MIT-green)](LICENSE)

**GoRefill** adalah platform e-commerce berbasis PHP Native untuk layanan isi ulang air galon, LPG, dan kebutuhan rumah tangga lainnya. Sistem ini mendukung multi-role (Admin, Kurir, User), real-time courier tracking, voucher management, analytics dashboard, email notifications, dan payment gateway terintegrasi.

---

## 🌟 Key Features

### 🛒 **Customer Features**
- ✅ Product browsing with category filters & search
- ✅ Shopping cart with real-time AJAX updates
- ✅ Voucher/discount code system
- ✅ Multiple shipping addresses with map picker (Leaflet.js)
- ✅ Secure payment via Midtrans (Credit Card, E-Wallet, Bank Transfer)
- ✅ Real-time order tracking with courier location
- ✅ Product reviews & ratings (1-5 stars)
- ✅ Wishlist/favorites
- ✅ Email notifications (order, payment, delivery)
- ✅ Order history & reorder functionality

### 👨‍💼 **Admin Features**
- ✅ Analytics dashboard with Chart.js visualizations
- ✅ Sales reports with date range filtering
- ✅ CSV export (sales & products)
- ✅ Complete CRUD operations (Products, Users, Categories, Vouchers)
- ✅ Order management & status updates
- ✅ Courier assignment
- ✅ Voucher management (percentage/fixed, expiry, usage limits)
- ✅ User management & role assignment
- ✅ Real-time statistics (revenue, orders, top products)

### 🚚 **Courier Features**
- ✅ Assigned orders dashboard
- ✅ Auto location tracking via browser GPS
- ✅ Order status updates (packing → shipping → delivered)
- ✅ Delivery history

### 📧 **Email Notifications**
- ✅ Welcome email on registration
- ✅ Order confirmation with details
- ✅ Payment success notification
- ✅ Shipping notification with courier info
- ✅ Delivery confirmation
- ✅ Password reset emails

---

## 💻 Tech Stack

### **Backend**
- **PHP 8.0+** (Native, OOP with MVC architecture)
- **MySQL 8.0+** (PDO with prepared statements)
- **Composer** (dependency management)
- **PHPMailer** (email notifications)

### **Frontend**
- **HTML5 & CSS3**
- **TailwindCSS** (utility-first styling)
- **JavaScript (Vanilla)** (no frameworks)
- **Chart.js** (analytics visualizations)
- **SweetAlert2** (beautiful alerts)
- **Leaflet.js** (interactive maps)

### **Third-Party APIs**
- **Midtrans API** (payment gateway)
  - Snap.js (frontend popup)
  - REST API (server-side verification)
- **OpenStreetMap** (map tiles via Leaflet)

---

## 🚀 Installation & Setup

### **Prerequisites**
- PHP 8.0 or higher
- MySQL 8.0 or higher (or MariaDB compatible)
- Composer
- PHP Extensions: `pdo_mysql`, `curl`, `mbstring`, `openssl`
- SMTP account (Gmail recommended) for email notifications

### **Installation Steps**

#### 1. **Clone Repository**
```bash
git clone https://github.com/yourusername/gorefill.git
cd gorefill
```

#### 2. **Install PHP Dependencies**
```bash
composer install
```

#### 3. **Database Setup**
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE gorefill CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import schema & sample data
mysql -u root -p gorefill < migrations/gorefill.sql
```

#### 4. **Configure Database**
Edit `config/config.php`:
```php
<?php
return [
    'db_host' => '127.0.0.1',
    'db_name' => 'gorefill',
    'db_user' => 'root',
    'db_pass' => 'your_password',
    'db_charset' => 'utf8mb4'
];
```

#### 5. **Configure Midtrans Payment Gateway**
Copy example file:
```bash
cp config/midtrans.example.php config/midtrans.php
```

Edit `config/midtrans.php` with your credentials from [Midtrans Dashboard](https://dashboard.midtrans.com):
```php
<?php
return [
    'is_production' => false, // Set true for production
    'server_key' => 'SB-Mid-server-xxxxxx', // Your Midtrans Server Key
    'client_key' => 'SB-Mid-client-xxxxxx', // Your Midtrans Client Key
    'enabled_payments' => ['credit_card', 'gopay', 'shopeepay', 'bca_va'],
];
```

#### 6. **Configure Email Notifications**
Edit `config/mail.php`:
```php
<?php
return [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_user' => 'your-email@gmail.com',
    'smtp_pass' => 'your-app-password', // Gmail App Password
    'from_email' => 'noreply@gorefill.com',
    'from_name' => 'GoRefill',
];
```

**For Gmail:**
1. Enable 2-Factor Authentication
2. Generate App Password: https://myaccount.google.com/apppasswords
3. Use App Password (not regular password)

#### 7. **Set File Permissions**
```bash
# For uploads directory
chmod -R 775 public/uploads/
chown -R www-data:www-data public/uploads/

# Or if using XAMPP/Laragon (Windows), ensure write permissions
```

### **Running the Application**

#### **Option 1: PHP Built-in Server** (Development)
```bash
php -S localhost:8000 -t public
```
Access: `http://localhost:8000`

#### **Option 2: XAMPP/Laragon** (Development)
1. Place `gorefill` folder in `htdocs` (XAMPP) or `www` (Laragon)
2. Start Apache and MySQL
3. Access: `http://localhost/gorefill/public`

#### **Option 3: Production Server**
- Configure Apache/Nginx to point document root to `/public`
- Enable `.htaccess` (Apache) or configure Nginx rewrite rules
- Set `is_production => true` in Midtrans config
- Use real Midtrans Production keys

---

## 👤 Default Accounts

After importing database, you can login with:

### **Admin Account**
- Email: `admin@gorefill.test`
- Password: `admin123`

### **Courier Account**
- Email: `kurir@gorefill.test`
- Password: `kurir123`

### **Customer Account**
- Email: `user@gorefill.test`
- Password: `user123`

**⚠️ Change default passwords immediately in production!**

---

## 📂 Project Structure

```
gorefill/
├── app/
│   ├── Controllers/          # Business logic handlers
│   │   ├── AuthController.php
│   │   ├── ProductController.php
│   │   ├── CartController.php
│   │   ├── CheckoutController.php
│   │   ├── PaymentController.php
│   │   ├── AdminController.php
│   │   ├── CourierController.php
│   │   └── UserController.php
│   ├── Models/               # Database interaction layer
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Order.php
│   │   ├── Voucher.php
│   │   ├── Analytics.php
│   │   └── Address.php
│   ├── Services/             # External services
│   │   └── MailService.php   # PHPMailer wrapper
│   └── Views/                # HTML templates
│       ├── layouts/          # Shared layouts
│       ├── auth/             # Login, register
│       ├── products/         # Product pages
│       ├── checkout/         # Checkout flow
│       ├── admin/            # Admin panel
│       ├── courier/          # Courier dashboard
│       ├── profile/          # User profile
│       └── emails/           # Email templates
├── config/
│   ├── config.php            # Database config
│   ├── midtrans.php          # Midtrans credentials
│   └── mail.php              # SMTP config
├── migrations/
│   └── gorefill.sql          # Database schema + sample data
├── public/                   # Web-accessible directory
│   ├── index.php             # Front controller (routing)
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   └── images/
│   └── uploads/              # User-uploaded files
├── vendor/                   # Composer dependencies
├── composer.json
├── .gitignore
└── README.md
```

---

## 🧭 Business Logic Flow

### **Customer Journey**

```
1. BROWSE PRODUCTS
   ↓
2. ADD TO CART (AJAX)
   ↓
3. APPLY VOUCHER (optional)
   ↓
4. CHECKOUT
   - Select/add shipping address (with map picker)
   - Confirm order details
   ↓
5. PAYMENT (Midtrans Snap)
   - Choose payment method
   - Complete payment
   ↓
6. CONFIRMATION
   - Receive order confirmation email
   - Receive payment success email
   ↓
7. ORDER PROCESSING
   - Admin assigns courier
   - Status: packing → shipping
   ↓
8. DELIVERY
   - Track courier in real-time (GPS)
   - Receive shipping notification email
   ↓
9. DELIVERED
   - Receive delivery confirmation email
   - Leave review & rating
```

### **Admin Workflow**

```
1. LOGIN to Admin Dashboard
   ↓
2. VIEW ANALYTICS
   - Today/Week/Month revenue
   - Sales trends (Chart.js)
   - Top products
   - Order statistics
   ↓
3. MANAGE CONTENT
   - CRUD Products
   - CRUD Categories
   - CRUD Vouchers
   - CRUD Users
   ↓
4. PROCESS ORDERS
   - View pending payments
   - Update order status
   - Assign couriers
   ↓
5. GENERATE REPORTS
   - Sales report (date range)
   - Export CSV
   - Product performance
```

### **Courier Workflow**

```
1. LOGIN to Courier Dashboard
   ↓
2. VIEW ASSIGNED ORDERS
   ↓
3. START DELIVERY
   - Browser auto-tracks GPS location
   - Location saved to database
   - Visible on customer's tracking page
   ↓
4. UPDATE STATUS
   - Change to "shipping"
   - Customer receives email notification
   ↓
5. COMPLETE DELIVERY
   - Mark as "delivered"
   - Customer receives confirmation email
```

---

## 🗺️ Map Integration (Leaflet.js)

### **Address Picker (Checkout)**
```javascript
// Customer clicks on map to set shipping address
const map = L.map('map').setView([-6.9667, 110.4167], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

map.on('click', function(e) {
    if (marker) map.removeLayer(marker);
    marker = L.marker(e.latlng).addTo(map);
    
    // Save coordinates
    document.getElementById('latitude').value = e.latlng.lat;
    document.getElementById('longitude').value = e.latlng.lng;
});
```

### **Courier Tracking (Real-time)**
```javascript
// Auto-update courier location every 10 seconds
setInterval(function() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            fetch('/index.php?route=courier.updateLocation', {
                method: 'POST',
                body: JSON.stringify({
                    order_id: orderId,
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude
                })
            });
        });
    }
}, 10000);
```

---

## 💳 Payment Integration (Midtrans)

### **Flow Overview**
1. **Checkout** → Generate Snap Token from Midtrans API
2. **Frontend** → Display Midtrans Snap popup
3. **Customer** → Select payment method & complete payment
4. **Midtrans** → Send webhook notification to callback URL
5. **Backend** → Verify signature & update order status
6. **System** → Send payment success email

### **Backend (Generate Token)**
```php
// CheckoutController.php
$params = [
    'transaction_details' => [
        'order_id' => $orderNumber,
        'gross_amount' => (int) $total,
    ],
    'customer_details' => [
        'first_name' => $_SESSION['name'],
        'email' => $_SESSION['email'],
    ],
    'item_details' => $cartItems,
];

$snapToken = \Midtrans\Snap::getSnapToken($params);
```

### **Frontend (Snap Popup)**
```javascript
// checkout.js
snap.pay(snapToken, {
    onSuccess: function(result) {
        window.location.href = '/index.php?route=payment.success&order_number=' + result.order_id;
    },
    onPending: function(result) {
        window.location.href = '/index.php?route=payment.pending&order_number=' + result.order_id;
    },
    onError: function(result) {
        Swal.fire('Error', 'Payment failed', 'error');
    }
});
```

### **Webhook Verification**
```php
// PaymentController.php
public function callback() {
    $json = file_get_contents('php://input');
    $notification = json_decode($json);
    
    // Verify signature
    $serverKey = $this->midtransConfig['server_key'];
    $hashed = hash('sha512', $notification->order_id . $notification->status_code . 
                   $notification->gross_amount . $serverKey);
    
    if ($hashed === $notification->signature_key) {
        // Update order status
        $this->orderModel->updatePaymentStatus($notification->order_id, 'paid');
        
        // Send email notification
        $mailService->sendPaymentSuccess($order);
    }
}
```

---

## 📊 Database Schema

### **Core Tables**

#### **users**
```sql
- id, name, email, password, role (admin/user/kurir), phone, created_at
```

#### **products**
```sql
- id, category_id, name, description, price, stock, image, eco_badge, created_at
```

#### **orders**
```sql
- id, user_id, order_number, address_id, voucher_id, subtotal, discount_amount, 
  total_price, payment_status, order_status, snap_token, courier_id, created_at
```

#### **order_items**
```sql
- id, order_id, product_id, quantity, price, subtotal
```

#### **vouchers**
```sql
- id, code, discount_type (percentage/fixed), discount_value, min_purchase, 
  usage_limit, times_used, expiry_date, is_active
```

#### **addresses**
```sql
- id, user_id, label, street, village, district, regency, province, postal_code, 
  latitude, longitude, phone, is_default
```

#### **courier_locations**
```sql
- id, courier_id, order_id, latitude, longitude, updated_at
```

#### **favorites**
```sql
- id, user_id, product_id, created_at
```

#### **product_reviews**
```sql
- id, product_id, user_id, rating (1-5), review, created_at
```

Full schema available in: `migrations/gorefill.sql`

---

## 🔀 Routing System

### **Route Format**
```
/index.php?route={controller}.{method}&{params}
```

### **Available Routes**

#### **Public Routes**
```
GET  /index.php?route=home                      # Homepage (product list)
GET  /index.php?route=product.detail&id={id}    # Product detail
POST /index.php?route=auth.login                # Login
POST /index.php?route=auth.register             # Register
GET  /index.php?route=auth.logout               # Logout
```

#### **Cart Routes (AJAX)**
```
POST /index.php?route=cart.add                  # Add to cart
POST /index.php?route=cart.update               # Update quantity
POST /index.php?route=cart.delete               # Remove item
GET  /index.php?route=cart                      # View cart
```

#### **Checkout & Payment**
```
GET  /index.php?route=checkout                  # Checkout page
POST /index.php?route=checkout.create           # Process checkout (get snap_token)
POST /index.php?route=payment.callback          # Midtrans webhook
GET  /index.php?route=payment.success           # Payment success page
GET  /index.php?route=payment.pending           # Payment pending page
GET  /index.php?route=payment.failed            # Payment failed page
```

#### **User Profile**
```
GET  /index.php?route=profile                   # User profile & orders
GET  /index.php?route=profile.orderDetail       # Order detail with tracking
GET  /index.php?route=user.vouchers             # Available vouchers
POST /index.php?route=profile.update            # Update profile
```

#### **Admin Routes**
```
GET  /index.php?route=admin.dashboard           # Analytics dashboard
GET  /index.php?route=admin.reports             # Sales reports
GET  /index.php?route=admin.exportReport        # Export CSV
GET  /index.php?route=admin.products            # Product management
GET  /index.php?route=admin.vouchers            # Voucher management
GET  /index.php?route=admin.orders              # Order management
GET  /index.php?route=admin.users               # User management
```

#### **Courier Routes**
```
GET  /index.php?route=courier.dashboard         # Courier dashboard
POST /index.php?route=courier.updateLocation    # Update GPS location
POST /index.php?route=courier.updateStatus      # Update order status
```

---

## 📧 Email Notifications

### **Automated Emails**

1. **Welcome Email** (On registration)
   - Subject: "Selamat Datang di GoRefill! 🎉"
   - Trigger: User successfully registers
   - Template: `app/Views/emails/welcome.php`

2. **Order Confirmation** (On checkout)
   - Subject: "Pesanan Berhasil Dibuat #{order_number}"
   - Trigger: Order created (before payment)
   - Template: `app/Views/emails/order-confirmation.php`

3. **Payment Success** (On payment)
   - Subject: "Pembayaran Berhasil! Pesanan #{order_number}"
   - Trigger: Midtrans callback with status "paid"
   - Template: `app/Views/emails/payment-success.php`

4. **Shipping Notification** (Future)
   - Subject: "Pesanan Sedang Dikirim! 🚚"
   - Trigger: Order status changed to "shipping"
   - Template: `app/Views/emails/shipping.php`

5. **Delivery Confirmation** (Future)
   - Subject: "Pesanan Telah Sampai! ✅"
   - Trigger: Order status changed to "delivered"
   - Template: `app/Views/emails/delivered.php`

### **Test Email**
```bash
php test-email.php your-email@example.com
```

---

## 📈 Analytics Dashboard

### **Key Metrics**
- ✅ Today's Revenue
- ✅ This Week Revenue
- ✅ This Month Revenue
- ✅ Average Order Value
- ✅ Total Products/Customers/Categories/Vouchers

### **Visualizations (Chart.js)**
- ✅ Sales Trend Line Chart (Last 7 days)
- ✅ Category Performance Pie Chart
- ✅ Order Status Bar Chart
- ✅ Top 5 Products Table

### **Reports**
- ✅ Date range filtering
- ✅ Top 20 products ranking
- ✅ Daily sales breakdown
- ✅ CSV export (sales & products)

---

## 🧪 Testing

### **Manual Testing Checklist**

#### **Customer Flow**
- [ ] Register new account → Receive welcome email
- [ ] Browse products → Filter by category
- [ ] Add to cart → Update quantity
- [ ] Apply voucher code → Discount applied
- [ ] Checkout → Select address on map
- [ ] Payment via Midtrans → Choose payment method
- [ ] Receive order confirmation email
- [ ] Complete payment → Receive payment success email
- [ ] Track order → See courier location on map
- [ ] Leave product review

#### **Admin Flow**
- [ ] Login as admin
- [ ] View analytics dashboard → Charts display correctly
- [ ] Create new product → Upload image
- [ ] Create voucher → Set expiry date
- [ ] View orders → Filter by status
- [ ] Assign courier to order
- [ ] Export sales report (CSV)

#### **Courier Flow**
- [ ] Login as courier
- [ ] View assigned orders
- [ ] Start delivery → GPS tracking starts
- [ ] Update order status
- [ ] Complete delivery

### **Email Testing**
```bash
# Test SMTP configuration
php test-email.php your-email@gmail.com

# Check inbox (and spam folder)
# Verify links work correctly
# Test on multiple email clients (Gmail, Outlook, Yahoo)
```

---

## 🔒 Security Considerations

### **Implemented**
- ✅ Password hashing with `password_hash()` & `password_verify()`
- ✅ PDO prepared statements (SQL injection protection)
- ✅ Input sanitization with `htmlspecialchars()` & `filter_input()`
- ✅ CSRF protection on forms (session tokens)
- ✅ Role-based access control (admin/user/courier)
- ✅ Session hijacking prevention (regenerate session ID)
- ✅ Midtrans signature verification
- ✅ Secure file uploads (type & size validation)

### **Recommended for Production**
- ⚠️ HTTPS/SSL certificate (Let's Encrypt)
- ⚠️ Rate limiting (login attempts, API calls)
- ⚠️ Environment variables for secrets (`.env` file)
- ⚠️ Database backups (automated)
- ⚠️ Error logging (don't expose errors to users)
- ⚠️ XSS protection headers
- ⚠️ Content Security Policy (CSP)

---

## 🚀 Deployment

### **Production Checklist**

#### **1. Environment**
- [ ] Set `is_production => true` in `config/midtrans.php`
- [ ] Use Production Midtrans keys (not Sandbox)
- [ ] Update `config/mail.php` with production SMTP
- [ ] Set secure database password
- [ ] Enable HTTPS/SSL

#### **2. Performance**
- [ ] Enable PHP OPcache
- [ ] Enable MySQL query cache
- [ ] Compress static assets (gzip)
- [ ] Optimize images
- [ ] Set proper cache headers

#### **3. Security**
- [ ] Change all default passwords
- [ ] Remove test accounts
- [ ] Disable PHP error display
- [ ] Set restrictive file permissions
- [ ] Configure firewall rules

#### **4. Monitoring**
- [ ] Set up error logging
- [ ] Configure email alerts for critical errors
- [ ] Monitor server resources
- [ ] Set up database backups (daily)

---

## 📝 Development Rules

### **Code Standards**
- ✅ Follow MVC architecture strictly
- ✅ Use PDO prepared statements for all queries
- ✅ Never write SQL queries in Views
- ✅ Use PascalCase for class names
- ✅ Use camelCase for variables & methods
- ✅ Add PHPDoc comments for public methods
- ✅ Sanitize all user input
- ✅ Use semantic HTML5
- ✅ Follow TailwindCSS utility-first approach

### **Git Workflow**
```bash
# Feature branch
git checkout -b feature/new-feature
git commit -m "feat: add new feature"
git push origin feature/new-feature

# Bug fix
git checkout -b fix/bug-description
git commit -m "fix: resolve bug description"
git push origin fix/bug-description
```

### **Commit Message Convention**
```
feat: add new feature
fix: bug fix
docs: documentation update
style: code formatting
refactor: code refactoring
test: add tests
chore: maintenance tasks
```

---

## 📚 Documentation

### **API Documentation**
- Midtrans API: https://docs.midtrans.com
- Leaflet.js: https://leafletjs.com/reference.html
- Chart.js: https://www.chartjs.org/docs/
- PHPMailer: https://github.com/PHPMailer/PHPMailer

### **Project Documentation**
- `WEEK4-DAY17-VOUCHER-MANAGEMENT.md` - Voucher system details
- `WEEK4-DAY18-ANALYTICS-DASHBOARD.md` - Analytics & reports
- `WEEK4-DAY19-EMAIL-NOTIFICATIONS.md` - Email setup & templates
- `NULL-COALESCING-FIX.md` - PHP 8 compatibility fixes

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'feat: add amazing feature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📜 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👨‍💻 Author

**GoRefill Development Team**

- Email: support@gorefill.com
- GitHub: [@yourusername](https://github.com/yourusername)

---

## 🙏 Acknowledgments

- [Midtrans](https://midtrans.com) - Payment gateway
- [Leaflet](https://leafletjs.com) - Interactive maps
- [TailwindCSS](https://tailwindcss.com) - CSS framework
- [Chart.js](https://www.chartjs.org) - Data visualization
- [PHPMailer](https://github.com/PHPMailer/PHPMailer) - Email sending
- [SweetAlert2](https://sweetalert2.github.io) - Beautiful alerts

---

## 📞 Support

If you encounter any issues or have questions:

1. Check existing [Issues](https://github.com/yourusername/gorefill/issues)
2. Create a new issue with detailed description
3. Email: support@gorefill.com

---

**⭐ If you find this project helpful, please give it a star!**

---

*Built with ❤️ using PHP Native* 🚀
