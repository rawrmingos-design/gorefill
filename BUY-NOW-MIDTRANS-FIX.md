# 🔧 Buy Now - Midtrans Error Fix

**Date:** October 24, 2025  
**Error:** `gross_amount harus sama atau lebih besar dari 0.01`

---

## 🐛 Bug Description

**Error Message:**
```
Midtrans API is returning API error. HTTP status code: 400
API response: {
    "error_messages": [
        "transaction_details.gross_amount harus sama atau lebih besar dari 0.01"
    ]
}
```

**When:** Clicking "Lanjutkan ke Pembayaran" after Buy Now

---

## 🔍 Root Cause Analysis

### Problem Flow:
```
1. User clicks "Buy Now"
   → $_SESSION['buy_now'] created ✅
   
2. Redirected to checkout page
   → URL: checkout&buy_now=1
   → $_GET['buy_now'] = 1 ✅
   
3. Checkout page loads
   → getCartItems() checks $_GET['buy_now'] ✅
   → Returns buy_now item ✅
   
4. User clicks "Lanjutkan ke Pembayaran"
   → AJAX POST to checkout.create
   → ❌ $_GET['buy_now'] NOT AVAILABLE! (AJAX doesn't pass URL params)
   → getCartItems() returns empty array []
   → $subtotal = 0
   → $total = 0
   → Midtrans error: gross_amount = 0 ❌
```

**Root Cause:**
```php
// OLD CODE - BROKEN for AJAX
if (isset($_GET['buy_now']) && $_GET['buy_now'] == 1 && !empty($_SESSION['buy_now'])) {
    // This condition FAILS in AJAX calls!
    // Because $_GET params are not available in POST requests
}
```

---

## ✅ Solution Applied

### Fix: Prioritize Session Check

**Before (Broken):**
```php
private function getCartItems() {
    $cartItems = [];
    
    // ❌ Checks $_GET first - fails in AJAX
    if (isset($_GET['buy_now']) && $_GET['buy_now'] == 1 && !empty($_SESSION['buy_now'])) {
        // buy now logic
    } else {
        // cart logic
    }
    
    return $cartItems;
}
```

**After (Fixed):**
```php
private function getCartItems() {
    $cartItems = [];
    
    // ✅ Check session first - works in both GET and AJAX POST
    if (!empty($_SESSION['buy_now'])) {
        $buyNowItem = $_SESSION['buy_now'];
        $cartItems[] = [
            'id' => $buyNowItem['product_id'],
            'name' => $buyNowItem['name'],
            'price' => $buyNowItem['price'],
            'qty' => $buyNowItem['quantity'],
            'image' => $buyNowItem['image'],
            'subtotal' => $buyNowItem['price'] * $buyNowItem['quantity']
        ];
    } elseif (!empty($_SESSION['cart'])) {
        // Regular cart checkout
        foreach ($_SESSION['cart'] as $productId => $item) {
            // ... cart logic
        }
    }
    
    return $cartItems;
}
```

**Key Changes:**
1. ✅ Check `$_SESSION['buy_now']` first (not `$_GET`)
2. ✅ Use `elseif` for cart (prioritize buy_now)
3. ✅ Works in both GET (page load) and POST (AJAX)

---

## 🛡️ Additional Validations Added

### 1. Validate Cart Items Not Empty
```php
if (empty($cartItems)) {
    echo json_encode(['success' => false, 'message' => 'Tidak ada produk untuk checkout']);
    exit;
}
```

### 2. Validate Subtotal > 0
```php
if ($subtotal <= 0) {
    echo json_encode(['success' => false, 'message' => 'Total pembelian tidak valid']);
    exit;
}
```

**Why:** Prevent sending invalid data to Midtrans API

---

## 🔄 Complete Flow (Fixed)

### Buy Now Flow:
```
1. Product Detail
   → Click "Buy Now"
   → POST to checkout.buyNow
   
2. CheckoutController::buyNow()
   → Create $_SESSION['buy_now'] ✅
   → Redirect to checkout&buy_now=1
   
3. Checkout Page Load (GET)
   → CheckoutController::index()
   → getCartItems() checks $_SESSION['buy_now'] ✅
   → Returns buy_now item ✅
   → Page displays correctly ✅
   
4. Click "Lanjutkan ke Pembayaran" (AJAX POST)
   → CheckoutController::create()
   → getCartItems() checks $_SESSION['buy_now'] ✅ (not $_GET!)
   → Returns buy_now item ✅
   → $subtotal calculated correctly ✅
   → $total > 0 ✅
   → Midtrans Snap Token generated ✅
   → Success! 🎉
```

### Regular Cart Flow:
```
1. Add to Cart
   → $_SESSION['cart'] created ✅
   
2. Checkout Page
   → getCartItems() checks $_SESSION['buy_now'] → empty
   → Falls back to $_SESSION['cart'] ✅
   → Returns cart items ✅
   
3. Click "Lanjutkan ke Pembayaran"
   → getCartItems() checks $_SESSION['buy_now'] → empty
   → Falls back to $_SESSION['cart'] ✅
   → Success! ✅
```

---

## 🧪 Testing

### Test 1: Buy Now with Payment
```
1. Go to product detail
2. Select quantity = 2
3. Click "Buy Now"
4. ✅ Redirected to checkout
5. ✅ Product shown with correct price
6. Select address
7. Click "Lanjutkan ke Pembayaran"
8. ✅ No error!
9. ✅ Midtrans Snap popup appears
10. ✅ gross_amount correct (price × qty)
11. Complete payment
12. ✅ Success!
```

### Test 2: Regular Cart with Payment
```
1. Add product to cart
2. Go to cart
3. Click "Checkout"
4. Select address
5. Click "Lanjutkan ke Pembayaran"
6. ✅ No error!
7. ✅ Midtrans Snap popup appears
8. ✅ Success!
```

### Test 3: Edge Cases
```
Test 3a: Empty cart
→ ✅ Error: "Tidak ada produk untuk checkout"

Test 3b: Zero price product (if exists)
→ ✅ Error: "Total pembelian tidak valid"

Test 3c: Buy Now then add to cart
→ ✅ Buy Now takes priority
→ ✅ Cart ignored during buy_now checkout
```

---

## 📊 Session Priority Logic

```
Priority Order:
1. $_SESSION['buy_now'] (highest priority)
2. $_SESSION['cart'] (fallback)
3. Empty array (if both empty)

Why this order?
- Buy Now is temporary, single-use
- Cart is persistent, multi-use
- Buy Now should override cart during its checkout
```

---

## 🔑 Key Learnings

### ❌ Don't Rely on $_GET in AJAX Handlers
```php
// BAD - Fails in AJAX POST
if (isset($_GET['buy_now'])) {
    // This won't work in AJAX calls!
}
```

### ✅ Use Session for State Management
```php
// GOOD - Works everywhere
if (!empty($_SESSION['buy_now'])) {
    // This works in GET, POST, AJAX, etc.
}
```

### ✅ Always Validate Before External API Calls
```php
// Validate data before sending to Midtrans
if ($subtotal <= 0) {
    // Catch error early, don't send to Midtrans
}
```

---

## 📝 Files Modified

1. ✅ `app/Controllers/CheckoutController.php`
   - Updated `getCartItems()` to prioritize session
   - Added validation for empty cart items
   - Added validation for zero subtotal

---

## ✅ Status

**FIXED & TESTED** ✅

**What Was Fixed:**
1. ✅ getCartItems() now checks session first
2. ✅ Works in both GET and AJAX POST
3. ✅ Added validations to prevent invalid Midtrans calls
4. ✅ Buy Now → Payment flow now works perfectly

**Impact:**
- ✅ Buy Now feature fully functional
- ✅ No more Midtrans gross_amount errors
- ✅ Better error messages for users
- ✅ More robust checkout system

---

## 🎉 Success!

**Before:** ❌ Buy Now → Error: "gross_amount harus sama atau lebih besar dari 0.01"

**After:** ✅ Buy Now → Checkout → Payment → Success!

Now both Buy Now and Cart checkout work perfectly! 🚀
