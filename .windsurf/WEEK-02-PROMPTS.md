# 📅 WEEK 2: Shopping Cart & Payment (Days 6-10)

## Standard Context (Copy before each day's prompt)
```
PROJECT: GoRefill E-Commerce | PHP 8.x MVC | MySQL 8.x | TailwindCSS | Leaflet.js | Midtrans
RULES: Strict MVC, PDO prepared statements, Session auth, password_hash(), htmlspecialchars()
DEPENDENCIES: Week 1 complete (auth system, product CRUD ready)
```

---

## 📅 DAY 6: Product Catalog Pages

**Task:** Display product listing and detail pages for users

**Dependencies:** Week 1 complete (Product model ready)

**Steps:**
1. Create `/app/Controllers/ProductController.php`:
   - index() - list with filters (category, price, eco-badge)
   - detail($id) - single product with reviews
   - search() - keyword search
   - Pagination: 12 products per page
2. Create `/app/Views/products/index.php`:
   - Grid layout (3-4 columns, responsive)
   - Product cards: image, name, price, eco-badge
   - Filter sidebar: category, price range, eco-badge checkbox
   - Search bar
   - "Add to Cart" button
3. Create `/app/Views/products/detail.php`:
   - Large image, name, price, description, stock
   - Quantity selector
   - "Add to Cart" & "Add to Favorites" buttons
   - Reviews section (display only)
4. Implement filters via query params (?category=air&min=10000&max=50000&eco=1)
5. Style with TailwindCSS (modern e-commerce design)

**Deliverables:** ✅ ProductController.php ✅ index.php grid ✅ detail.php ✅ Filters working ✅ TailwindCSS design

**Use Context7:** TailwindCSS grid layouts, e-commerce UI

---

## 📅 DAY 7: Shopping Cart System

**Task:** Implement session-based shopping cart with AJAX

**Dependencies:** Day 6 complete (product pages ready)

**Steps:**
1. Create `/app/Controllers/CartController.php`:
   - add() POST - add to $_SESSION['cart']
   - update() POST - update quantity
   - remove() POST - remove item
   - get() GET - return cart as JSON
   - count() GET - total items
   - All return JSON for AJAX
2. Cart session structure:
   ```php
   $_SESSION['cart'] = [
     'product_id' => ['qty' => 2, 'price' => 15000],
   ];
   ```
3. Create `/app/Views/cart/index.php`:
   - Table: image, name, price, qty controls, subtotal
   - +/- buttons for quantity
   - Remove button (SweetAlert confirm)
   - Cart summary: subtotal, total
   - "Proceed to Checkout" button
4. Create `/public/assets/js/cart.js`:
   - addToCart(productId, qty) - Fetch API
   - updateCart(productId, qty)
   - removeFromCart(productId)
   - getCartCount() - update navbar badge
5. Add cart count badge on navbar

**Deliverables:** ✅ CartController.php ✅ cart/index.php ✅ cart.js ✅ AJAX working ✅ Cart badge ✅ SweetAlert confirms

**Use Context7:** JavaScript Fetch API, PHP session management

---

## 📅 DAY 8: Checkout Flow (Address & Voucher)

**Task:** Build checkout page with address and voucher validation

**Dependencies:** Day 7 complete (cart system ready)

**Steps:**
1. Create `/app/Models/Address.php`:
   - getByUserId($userId)
   - create($userId, $data) - with lat/lng
   - delete($id)
2. Create `/app/Models/Voucher.php`:
   - getByCode($code)
   - validate($code, $totalAmount) - check expiry, min_purchase, max_usage
   - use($voucherId) - increment usage_count
3. Create `/app/Controllers/CheckoutController.php`:
   - index() - show checkout form
   - selectAddress() POST
   - applyVoucher() POST - validate and return discount
   - create() POST - will complete tomorrow with payment
4. Create `/app/Views/checkout/index.php`:
   - Section 1: Cart summary (read-only)
   - Section 2: Address selection (radio buttons, "Add new" modal)
   - Section 3: Voucher input + "Apply" button
   - Section 4: Payment method (placeholder)
   - Order summary: Subtotal, Discount, Total
5. Implement voucher validation:
   - Check code exists & not expired
   - Check min_purchase met
   - Check max_usage not exceeded
   - Calculate discount (percentage/fixed)
   - Store voucher_id in session

**Deliverables:** ✅ Address.php ✅ Voucher.php ✅ CheckoutController.php ✅ checkout/index.php ✅ Voucher validation ✅ Address selection

**Use Context7:** PHP form validation, voucher logic

---

## 📅 DAY 9: Midtrans Payment Integration

**Task:** Complete payment gateway integration with Midtrans

**Dependencies:** Day 8 complete (checkout form ready)

**Steps:**
1. Install Midtrans: `composer require midtrans/midtrans-php`
2. Create `/config/midtrans.php`:
   ```php
   return [
     'is_production' => false,
     'server_key' => 'SB-Mid-server-YOUR_KEY',
     'client_key' => 'SB-Mid-client-YOUR_KEY',
   ];
   ```
3. Create `/app/Models/Order.php`:
   - create($userId, $addressId, $voucherId, $total, $items)
   - updatePaymentStatus($orderId, $status)
   - updateStatus($orderId, $status)
   - getById($orderId), getByUserId($userId)
4. Update `/app/Controllers/CheckoutController.php`:
   - In create(): validate cart, calculate total, create order, generate Snap Token:
     ```php
     \Midtrans\Config::$serverKey = $config['server_key'];
     $params = ['transaction_details' => ['order_id' => $orderId, 'gross_amount' => $total]];
     $snapToken = \Midtrans\Snap::getSnapToken($params);
     ```
   - Return snapToken to frontend
5. Create `/app/Controllers/PaymentController.php`:
   - callback() - verify Midtrans webhook, update payment_status to 'paid', set status to 'packing'
   - success($orderId), pending($orderId), failed($orderId) - result pages
6. Update checkout view with Midtrans Snap.js:
   ```html
   <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="CLIENT_KEY"></script>
   <script>
   snap.pay(snapToken, {
     onSuccess: (r) => window.location = '/index.php?route=payment.success&id=' + r.order_id,
     onPending: (r) => window.location = '/index.php?route=payment.pending&id=' + r.order_id,
     onError: (r) => window.location = '/index.php?route=payment.failed&id=' + r.order_id,
   });
   </script>
   ```
7. Create payment result views: success.php, pending.php, failed.php

**Deliverables:** ✅ Midtrans SDK ✅ midtrans.php config ✅ Order.php ✅ PaymentController.php ✅ Snap popup ✅ Callback handler ✅ Result pages

**Use Context7:** Midtrans PHP SDK, webhook security

---

## 📅 DAY 10: MVP Testing & Fixes

**Task:** End-to-end testing and bug fixes for Phase 1 MVP

**Dependencies:** Day 9 complete (payment working)

**Testing Checklist:**

**Authentication:**
- [ ] Register new user successfully
- [ ] Login with correct credentials
- [ ] Login fails with wrong password
- [ ] Logout destroys session
- [ ] Protected routes redirect to login

**Product Catalog:**
- [ ] Listing displays correctly
- [ ] Filters work (category, price, eco-badge)
- [ ] Search returns results
- [ ] Detail page shows info
- [ ] Pagination works

**Shopping Cart:**
- [ ] Add to cart updates badge
- [ ] Cart displays items
- [ ] Quantity update recalculates
- [ ] Remove works with confirm
- [ ] Cart persists across pages

**Checkout & Payment:**
- [ ] Checkout shows summary
- [ ] Address selection works
- [ ] Voucher validation correct
- [ ] Midtrans popup opens
- [ ] Payment success updates status
- [ ] Webhook callback works

**Admin Panel:**
- [ ] Admin login
- [ ] Product CRUD works
- [ ] Image upload works
- [ ] Only admin access

**Bug Fixes:**
- Fix SQL errors
- Fix JS console errors
- Improve error messages
- Add loading states
- Mobile responsiveness
- Error handling

**Optimization:**
- Review SQL queries
- Add database indexes
- Code comments
- Update README.md

**Deliverables:** ✅ All tests pass ✅ No errors ✅ Smooth UX ✅ Clean code ✅ README updated ✅ **MVP COMPLETE** 🎉

**Use Context7:** PHP debugging, MySQL optimization

---

## 🎯 WEEK 2 COMPLETION CHECKLIST
- [ ] Product catalog with filters
- [ ] Session-based shopping cart
- [ ] AJAX cart operations
- [ ] Checkout flow with vouchers
- [ ] Midtrans payment integration
- [ ] Order creation working
- [ ] Payment callback verified
- [ ] Complete MVP tested

**PHASE 1 MVP COMPLETE!** 🎉
**Next Week:** Leaflet maps integration, courier tracking, wishlist
