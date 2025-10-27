# 🚀 Quick Start: Payment Integration

## ⚡ Setup (5 Minutes)

### 1. Create Midtrans Config
```bash
copy config\midtrans.example.php config\midtrans.php
```

### 2. Get Midtrans Credentials
1. Go to: https://dashboard.sandbox.midtrans.com
2. Register/Login
3. Settings → Access Keys
4. Copy **Server Key** and **Client Key**

### 3. Update Config
Edit `config/midtrans.php`:
```php
'server_key' => 'SB-Mid-server-YOUR_SERVER_KEY_HERE',
'client_key' => 'SB-Mid-client-YOUR_CLIENT_KEY_HERE',
```

### 4. Run Migration
```sql
source migrations/create_orders_tables.sql
```

---

## 🧪 Test Payment (2 Minutes)

### Step 1: Add to Cart
- Go to Products page
- Add any product to cart

### Step 2: Checkout
- Click cart icon → "Checkout"
- Select/add shipping address
- (Optional) Apply voucher: `DISKON10`
- Click "Lanjutkan ke Pembayaran"

### Step 3: Pay with Test Card
Midtrans Snap popup will open. Use:

**Credit Card:**
```
Card: 4811 1111 1111 1114
Exp: 01/25
CVV: 123
OTP: 112233
```

### Step 4: Success!
You'll be redirected to success page with order details.

---

## 🎴 Test Cards (Sandbox)

| Scenario | Card Number | Result |
|----------|-------------|--------|
| Success | 4811 1111 1111 1114 | Payment approved |
| Denied | 4911 1111 1111 1113 | Payment denied |
| Challenge | 4411 1111 1111 1118 | Fraud challenge |

**OTP for all cards:** `112233`

---

## 🏦 Other Payment Methods

### GoPay
1. Select GoPay
2. Scan QR code (sandbox mode)
3. Use simulator in Midtrans dashboard

### Virtual Account
1. Select bank (BCA/BNI/BRI)
2. Get VA number
3. Use Midtrans simulator to mark as paid

---

## ✅ Verify Success

### Check Order in Database
```sql
SELECT order_number, payment_status, status, total 
FROM orders 
ORDER BY created_at DESC 
LIMIT 5;
```

### Check Order Items
```sql
SELECT oi.*, p.name 
FROM order_items oi
JOIN products p ON oi.product_id = p.id
WHERE oi.order_id = [YOUR_ORDER_ID];
```

---

## 🐛 Troubleshooting

**Snap popup doesn't open?**
- Check browser console for errors
- Verify `client_key` in config
- Check Snap.js loaded (view source)

**Payment not updating?**
- Check `server_key` in config
- Check error_log for callback errors
- Verify webhook URL in Midtrans dashboard

**Order not created?**
- Check cart has items
- Check address is selected
- Check browser console for API errors

---

## 📞 Support

**Midtrans Sandbox Dashboard:**  
https://dashboard.sandbox.midtrans.com

**Test Payment Simulator:**  
Dashboard → Transactions → [Your Transaction] → Actions → Simulator

**Documentation:**  
https://docs.midtrans.com

---

**Ready to test? Go to:** `index.php?route=products` 🚀
