# 📅 WEEK 4: Enhancement & Polish (Days 16-20)

## Standard Context (Copy before each day's prompt)
```
PROJECT: GoRefill E-Commerce | PHP 8.x MVC | MySQL 8.x | TailwindCSS | Leaflet.js | Midtrans
RULES: Strict MVC, PDO prepared statements, Session auth, SweetAlert notifications
DEPENDENCIES: Week 3 complete (maps, tracking, favorites ready)
```

---

## 📅 DAY 16: Product Reviews & Ratings

**Task:** Allow users to review and rate products after purchase

**Dependencies:** Week 3 complete

**Steps:**
1. Create `/app/Models/ProductReview.php`:
   - create($productId, $userId, $rating, $review)
   - getByProductId($productId, $limit, $offset) - pagination
   - getAverageRating($productId) - calculate avg rating
   - hasUserReviewed($productId, $userId) - check if already reviewed
   - canUserReview($productId, $userId) - check if user purchased product
2. Update `/app/Controllers/ProductController.php`:
   - addReview($productId) POST - validate and save review
     * Check user has purchased product
     * Check user hasn't already reviewed
     * Validate rating (1-5) and review text
3. Update `/app/Views/products/detail.php`:
   - Display average rating (stars) at top
   - Reviews section:
     * Show existing reviews (user name, rating, review, date)
     * Pagination for reviews
   - Review form (if user purchased and hasn't reviewed):
     * Star rating selector (1-5 stars)
     * Review textarea
     * Submit button
4. Update `/app/Views/products/index.php`:
   - Show average rating stars on product cards
5. Create `/public/assets/js/reviews.js`:
   - Star rating selector UI
   - AJAX review submission
   - SweetAlert success/error
6. Add verified purchase badge on reviews
7. Test: user purchases → receives order → can review → review appears

**Deliverables:** ✅ ProductReview.php ✅ Review methods in controller ✅ Review form ✅ Star rating UI ✅ Average rating display ✅ Purchase verification

**Use Context7:** Star rating components, review system patterns

---

## 📅 DAY 17: Advanced Voucher Management

**Task:** Complete voucher system with admin management and user history

**Dependencies:** Day 16 complete

**Steps:**
1. Update `/app/Models/Voucher.php`:
   - getAll() - list all vouchers
   - create($data) - create new voucher
   - update($id, $data) - edit voucher
   - delete($id) - delete voucher
   - getUsageHistory($voucherId) - who used it
   - getUserVoucherHistory($userId) - vouchers user used
2. Update `/app/Controllers/AdminController.php`:
   - vouchers() GET - list all vouchers
   - createVoucher() GET/POST - form and handler
   - editVoucher($id) GET/POST - edit form and handler
   - deleteVoucher($id) POST - delete voucher
   - voucherUsage($id) GET - show usage stats
3. Create `/app/Views/admin/vouchers/index.php`:
   - Table: code, discount, type, min_purchase, usage, valid_until, actions
   - "Create Voucher" button
   - Edit/Delete actions
4. Create `/app/Views/admin/vouchers/form.php`:
   - Fields: code, discount_type (percentage/fixed), discount_value
   - min_purchase, max_usage, valid_until
   - Validation: code unique, discount > 0
5. Add voucher features:
   - User-specific vouchers (add user_id column, nullable)
   - Category-specific vouchers (add category column, nullable)
   - First-time user vouchers
   - Bulk voucher generation
6. Create `/app/Views/user/vouchers.php`:
   - Show available vouchers for user
   - Show used vouchers history
   - "Copy Code" button
7. Add voucher notification system:
   - Show available vouchers on checkout page
   - Highlight if user is eligible
8. Test: admin creates voucher → user sees it → applies → discount correct

**Deliverables:** ✅ Voucher CRUD ✅ Admin voucher management ✅ Voucher types ✅ User voucher history ✅ Advanced validation ✅ Usage tracking

**Use Context7:** Voucher/discount logic patterns, promotional systems

---

## 📅 DAY 18: Admin Dashboard Analytics

**Task:** Build analytics dashboard with charts and reports

**Dependencies:** Day 17 complete

**Steps:**
1. Create `/app/Models/Analytics.php`:
   - getDailySales($startDate, $endDate) - sales by date
   - getTopProducts($limit) - best selling products
   - getCategoryStats() - sales by category
   - getUserStats() - new users by date
   - getOrderStats() - orders by status
   - getRevenueStats() - total revenue, avg order value
2. Update `/app/Controllers/AdminController.php`:
   - dashboard() - main analytics view
     * Total sales (today, this week, this month)
     * Total orders (pending, packing, shipped, delivered)
     * Total users, new users today
     * Top 5 products
     * Recent orders
   - reports() GET - detailed reports page
     * Date range selector
     * Sales chart (daily/weekly/monthly)
     * Category performance
     * Courier performance
3. Create `/app/Views/admin/dashboard.php`:
   - Stats cards: Sales, Orders, Users, Products
   - Charts:
     * Sales trend (line chart) - use Chart.js
     * Category distribution (pie chart)
     * Order status distribution (bar chart)
   - Top products table
   - Recent orders table
4. Add Chart.js CDN:
   ```html
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   ```
5. Create `/public/assets/js/admin-charts.js`:
   - Initialize Chart.js with data from backend
   - Line chart for sales trend
   - Pie chart for categories
   - Bar chart for order status
6. Create export functionality:
   - Export sales report to CSV
   - Export product report to CSV
   - Date range filter
7. Add date range picker for reports
8. Test: dashboard shows correct stats → charts display → CSV export works

**Deliverables:** ✅ Analytics.php model ✅ Dashboard with stats ✅ Chart.js integration ✅ Sales reports ✅ CSV export ✅ Date range filter

**Use Context7:** Chart.js documentation, dashboard design patterns

---

## 📅 DAY 19: Email Notifications (PHPMailer)

**Task:** Implement email notifications for important events

**Dependencies:** Day 18 complete

**Steps:**
1. Install PHPMailer: `composer require phpmailer/phpmailer`
2. Create `/config/mail.php`:
   ```php
   return [
     'smtp_host' => 'smtp.gmail.com',
     'smtp_port' => 587,
     'smtp_user' => 'your-email@gmail.com',
     'smtp_pass' => 'your-app-password',
     'from_email' => 'noreply@gorefill.com',
     'from_name' => 'GoRefill',
   ];
   ```
3. Create `/app/Services/MailService.php`:
   - send($to, $subject, $body, $isHtml) - send email
   - Template methods:
     * sendWelcomeEmail($user)
     * sendOrderConfirmation($order)
     * sendPaymentSuccess($order)
     * sendShippingNotification($order, $courier)
     * sendDeliveryConfirmation($order)
     * sendPasswordReset($user, $token)
4. Create email templates in `/app/Views/emails/`:
   - welcome.php - welcome new users
   - order-confirmation.php - order details
   - payment-success.php - payment received
   - shipping.php - order shipped, tracking link
   - delivered.php - order delivered, ask for review
   - password-reset.php - reset password link
5. Update controllers to trigger emails:
   - AuthController::register() → send welcome email
   - CheckoutController::create() → send order confirmation
   - PaymentController::callback() → send payment success
   - CourierController::startDelivery() → send shipping notification
   - CourierController::completeDelivery() → send delivered + review request
6. Create email queue system (optional):
   - Table: email_queue (id, to, subject, body, status, created_at)
   - Process queue with cron job or async
7. Add email preferences to user settings:
   - User can enable/disable notification types
8. Test: register → welcome email → order → confirmation email → payment → success email

**Deliverables:** ✅ PHPMailer installed ✅ mail.php config ✅ MailService.php ✅ Email templates ✅ Notifications triggered ✅ Email preferences

**Use Context7:** PHPMailer documentation, email template design

---

## 📅 DAY 20: Phase 2 Testing & Refinement

**Task:** Complete testing of all Phase 2 features and polish

**Dependencies:** Day 19 complete (all features implemented)

**Testing Checklist:**

**Maps & Tracking:**
- [ ] Leaflet map loads correctly
- [ ] Click to place marker works
- [ ] Current location button works
- [ ] Address with lat/lng saved
- [ ] Courier location updates in real-time
- [ ] Tracking page shows moving marker
- [ ] Route polyline displays
- [ ] Auto GPS sending works

**Reviews & Ratings:**
- [ ] User can review purchased products
- [ ] Cannot review without purchase
- [ ] Cannot review twice
- [ ] Star rating selector works
- [ ] Average rating calculates correctly
- [ ] Reviews display with pagination

**Vouchers:**
- [ ] Admin can CRUD vouchers
- [ ] Voucher validation works
- [ ] Different voucher types work
- [ ] User sees available vouchers
- [ ] Usage tracking correct
- [ ] Expiry dates enforced

**Admin Analytics:**
- [ ] Dashboard shows correct stats
- [ ] Charts display properly
- [ ] Date range filter works
- [ ] CSV export generates correctly
- [ ] Real-time data updates

**Email Notifications:**
- [ ] Welcome email sends
- [ ] Order confirmation sends
- [ ] Payment success sends
- [ ] Shipping notification sends
- [ ] Delivery confirmation sends
- [ ] Email preferences work

**Bug Fixes & Polish:**
- Fix any remaining bugs
- Improve mobile responsiveness
- Optimize database queries
- Add loading states
- Improve error messages
- Code cleanup and comments
- Update README.md

**Performance Optimization:**
- Add database indexes for commonly queried columns
- Optimize image loading (lazy load)
- Minify CSS/JS
- Enable browser caching
- Optimize SQL queries (EXPLAIN ANALYZE)

**Security Audit:**
- SQL injection prevention (prepared statements)
- XSS prevention (htmlspecialchars)
- CSRF protection
- File upload validation
- Password strength enforcement
- Session security (httponly, secure)

**Deliverables:** ✅ All tests pass ✅ No bugs ✅ Performance optimized ✅ Security hardened ✅ Documentation updated ✅ **PHASE 2 COMPLETE** 🎉

**Use Context7:** PHP performance optimization, web security best practices

---

## 🎯 WEEK 4 COMPLETION CHECKLIST
- [ ] Product reviews and ratings working
- [ ] Advanced voucher system complete
- [ ] Admin analytics dashboard with charts
- [ ] Email notifications for all events
- [ ] All Phase 2 features tested
- [ ] Performance optimized
- [ ] Security audit passed
- [ ] Documentation complete

**PHASE 2 COMPLETE!** 🎉
**Next Week:** Final optimization, deployment preparation
