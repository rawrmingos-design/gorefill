# 📅 WEEK 1: Foundation & Authentication (Days 1-5)

## Standard Context (Copy before each day's prompt)
```
PROJECT: GoRefill E-Commerce | PHP 8.x MVC | MySQL 8.x | TailwindCSS | Leaflet.js | Midtrans
RULES: Strict MVC, PDO prepared statements, Session auth, password_hash(), htmlspecialchars()
STRUCTURE: /app (Controllers/Models/Views), /config, /public, /migrations
```

---

## 📅 DAY 1: Project Setup & Database

**Task:** Create project structure and complete database schema

**Steps:**
1. Create folder structure: app/{Controllers,Models,Views}, config, public, uploads, migrations
2. Create `/config/config.php` with PDO database config
3. Create `/migrations/gorefill.sql` with tables:
   - users (id, name, email, password, role, created_at)
   - products (id, name, description, price, category, stock, image_url, eco_badge, created_at)
   - addresses (id, user_id, label, address, latitude, longitude, created_at)
   - orders (id, user_id, address_id, voucher_id, total, payment_status, status, created_at)
   - order_items (id, order_id, product_id, qty, price)
   - vouchers (id, code, discount_type, discount_value, min_purchase, max_usage, usage_count, valid_until)
   - favorites (id, user_id, product_id, created_at)
   - product_reviews (id, product_id, user_id, rating, review, created_at)
   - courier_locations (id, courier_id, latitude, longitude, updated_at)
4. Add foreign keys with ON DELETE CASCADE
5. Create `/app/bootstrap.php` with PDO connection and session_start()

**Deliverables:** ✅ Folder structure ✅ config.php ✅ gorefill.sql ✅ bootstrap.php ✅ DB connection test

**Use Context7:** PHP PDO best practices, MySQL foreign keys

---

## 📅 DAY 2: Routing & Base Controllers

**Task:** Implement front controller routing and base structure

**Dependencies:** Day 1 complete

**Steps:**
1. Create `/public/index.php` as front controller with routing:
   - Parse $_GET['route'] with switch/case
   - Map routes to controller methods
2. Create `/app/Controllers/BaseController.php`:
   - render($view, $data) - load views
   - redirect($route) - redirect to routes
   - json($data, $status) - JSON responses
   - requireAuth($role) - check authentication
3. Add routes: home, auth.login, auth.register, auth.logout
4. Create 404 error handler

**Deliverables:** ✅ index.php router ✅ BaseController.php ✅ Route mapping ✅ 404 page

**Use Context7:** PHP front controller pattern

---

## 📅 DAY 3: Authentication Backend

**Task:** Build user authentication system (register/login/logout)

**Dependencies:** Day 2 complete

**Steps:**
1. Create `/app/Models/User.php`:
   - register($name, $email, $password, $role) with password_hash()
   - login($email, $password) with password_verify()
   - findByEmail($email), findById($id)
   - All use PDO prepared statements
2. Create `/app/Controllers/AuthController.php`:
   - register() POST - validate, call User::register()
   - login() POST - validate, set $_SESSION['user_id', 'role']
   - logout() - destroy session
   - showRegisterForm(), showLoginForm()
3. Implement session authentication
4. Add input validation (email format, password min 8 chars, duplicate check)

**Deliverables:** ✅ User.php model ✅ AuthController.php ✅ Session management ✅ Password hashing ✅ Validation

**Use Context7:** PHP password hashing, session security

---

## 📅 DAY 4: Authentication UI

**Task:** Create auth views with TailwindCSS and test complete flow

**Dependencies:** Day 3 complete

**Steps:**
1. Create `/app/Views/auth/register.php`:
   - Form: name, email, password, confirm_password
   - TailwindCSS styling
   - JavaScript validation
   - SweetAlert for notifications
2. Create `/app/Views/auth/login.php`:
   - Form: email, password
   - TailwindCSS styling
3. Add SweetAlert2 CDN
4. Implement AJAX form submission with Fetch API
5. Test: register → login → logout

**Deliverables:** ✅ register.php ✅ login.php ✅ SweetAlert integration ✅ AJAX submission ✅ Full auth flow working

**Use Context7:** TailwindCSS forms, SweetAlert2

---

## 📅 DAY 5: Product CRUD (Admin)

**Task:** Build product management backend for admin

**Dependencies:** Day 4 complete

**Steps:**
1. Create `/app/Models/Product.php`:
   - getAll($category, $limit, $offset)
   - getById($id)
   - create($data), update($id, $data), delete($id)
   - search($keyword), getByCategory($category)
   - updateStock($id, $qty)
2. Create `/app/Controllers/AdminController.php`:
   - dashboard() - stats overview
   - products() - list with pagination
   - createProduct() POST, editProduct($id) GET/POST, deleteProduct($id)
   - All methods check requireAuth('admin')
3. Implement file upload for product images (save to /uploads/products/)
4. Add admin role check in BaseController

**Deliverables:** ✅ Product.php model ✅ AdminController.php ✅ Image upload ✅ Admin protection ✅ Prepared statements

**Use Context7:** PHP file upload security, PDO transactions

---

## 🎯 WEEK 1 COMPLETION CHECKLIST
- [ ] Database schema complete with all tables
- [ ] Routing system working
- [ ] User registration/login/logout functional
- [ ] Auth UI with TailwindCSS
- [ ] Admin can CRUD products
- [ ] Session-based authentication working
- [ ] All queries use prepared statements
- [ ] Input validation and sanitization in place

**Next Week:** Product catalog pages, shopping cart, checkout, Midtrans payment
