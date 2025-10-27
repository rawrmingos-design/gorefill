# 📊 Orders Table Structure - Complete

## ✅ Table Status: READY FOR MIDTRANS INTEGRATION

Total Columns: **49 columns**

---

## 📋 Column Groups

### 1. **Core Order Fields** (6 columns)
| Column | Type | Description |
|--------|------|-------------|
| `id` | INT | Primary key, auto increment |
| `order_number` | VARCHAR(50) | Unique order number: ORD-YYYYMMDD-XXXX |
| `user_id` | INT | Foreign key to users table |
| `address_id` | INT UNSIGNED | Foreign key to addresses table |
| `voucher_id` | INT | Foreign key to vouchers table (nullable) |
| `created_at` | TIMESTAMP | Order creation time |
| `updated_at` | TIMESTAMP | Last update time |

### 2. **Pricing Fields** (4 columns)
| Column | Type | Default | Description |
|--------|------|---------|-------------|
| `subtotal` | DECIMAL(12,2) | 0.00 | Subtotal sebelum diskon |
| `discount_amount` | DECIMAL(12,2) | 0.00 | Jumlah diskon dari voucher |
| `total` | DECIMAL(12,2) | - | Total yang harus dibayar |
| `gross_amount` | DECIMAL(12,2) | NULL | Total yang dikirim ke Midtrans |

### 3. **Payment Status** (3 columns)
| Column | Type | Values | Default |
|--------|------|--------|---------|
| `payment_status` | ENUM | pending, unpaid, paid, failed, expired, cancelled, refund | pending |
| `payment_method` | VARCHAR(50) | gopay, bca_va, credit_card, etc | NULL |
| `payment_type` | VARCHAR(50) | Tipe pembayaran dari Midtrans | NULL |

### 4. **Order Status** (1 column)
| Column | Type | Values | Default |
|--------|------|--------|---------|
| `status` | ENUM | pending, confirmed, packing, shipped, delivered, cancelled, expired | pending |

### 5. **Midtrans Core Fields** (8 columns)
| Column | Type | Description |
|--------|------|-------------|
| `snap_token` | VARCHAR(255) | Token untuk Snap.js popup |
| `transaction_id` | VARCHAR(100) | Transaction ID dari Midtrans |
| `transaction_status` | VARCHAR(50) | Status dari Midtrans: capture, settlement, pending, deny, cancel, expire |
| `midtrans_status` | VARCHAR(50) | Status umum dari Midtrans |
| `fraud_status` | VARCHAR(50) | Fraud detection: accept, challenge, deny |
| `signature_key` | VARCHAR(255) | Signature untuk validasi webhook |
| `callback_data` | JSON | Full callback data dari Midtrans |
| `paid_at` | TIMESTAMP | Waktu pembayaran berhasil |

### 6. **Midtrans Timestamps** (3 columns)
| Column | Type | Description |
|--------|------|-------------|
| `transaction_time` | TIMESTAMP | Waktu transaksi dibuat di Midtrans |
| `settlement_time` | TIMESTAMP | Waktu settlement |
| `expiry_time` | TIMESTAMP | Waktu kadaluarsa pembayaran |

### 7. **Bank Transfer / Virtual Account** (5 columns)
| Column | Type | Description |
|--------|------|-------------|
| `bank` | VARCHAR(50) | Nama bank: BCA, BNI, BRI, Mandiri, Permata |
| `va_number` | VARCHAR(50) | Nomor Virtual Account |
| `bill_key` | VARCHAR(50) | Bill key untuk Mandiri Bill |
| `biller_code` | VARCHAR(50) | Biller code untuk Mandiri Bill |
| `currency` | VARCHAR(3) | Currency code (default: IDR) |

### 8. **Convenience Store** (2 columns)
| Column | Type | Description |
|--------|------|-------------|
| `store` | VARCHAR(50) | Nama store: alfamart, indomaret |
| `payment_code` | VARCHAR(50) | Kode pembayaran untuk store |

### 9. **Additional Midtrans Data** (2 columns)
| Column | Type | Description |
|--------|------|-------------|
| `pdf_url` | VARCHAR(500) | URL PDF invoice dari Midtrans |
| `finish_redirect_url` | VARCHAR(500) | URL redirect setelah pembayaran |

### 10. **Shipping Address Snapshot** (5 columns)
| Column | Type | Description |
|--------|------|-------------|
| `shipping_name` | VARCHAR(255) | Nama penerima |
| `shipping_phone` | VARCHAR(20) | Nomor telepon penerima |
| `shipping_address` | TEXT | Alamat lengkap |
| `shipping_city` | VARCHAR(100) | Kota |
| `shipping_postal_code` | VARCHAR(10) | Kode pos |

### 11. **Customer Info** (2 columns)
| Column | Type | Description |
|--------|------|-------------|
| `customer_email` | VARCHAR(255) | Email customer dari session |
| `customer_phone` | VARCHAR(20) | Phone customer dari session |

### 12. **Shipping Tracking** (2 columns)
| Column | Type | Description |
|--------|------|-------------|
| `courier` | VARCHAR(50) | Kurir: JNE, TIKI, POS, etc |
| `tracking_number` | VARCHAR(100) | Nomor resi |

### 13. **Refund** (3 columns)
| Column | Type | Default | Description |
|--------|------|---------|-------------|
| `refund_amount` | DECIMAL(12,2) | 0.00 | Jumlah yang di-refund |
| `refund_reason` | TEXT | NULL | Alasan refund |
| `refunded_at` | TIMESTAMP | NULL | Waktu refund |

### 14. **Notes** (1 column)
| Column | Type | Description |
|--------|------|-------------|
| `note` | TEXT | Catatan order (internal/customer) |

---

## 🔄 Payment Status Flow

```
pending → paid → (order.status = 'confirmed')
pending → failed → (user can retry)
pending → expired → (auto-cancel after 24h)
paid → refund → (refund processed)
```

## 🔄 Order Status Flow

```
pending → confirmed (after payment) → packing → shipped → delivered
                                                        ↓
                                                   cancelled
```

## 🔄 Transaction Status from Midtrans

| Status | Meaning | Action |
|--------|---------|--------|
| `capture` | Credit card captured | Check fraud_status |
| `settlement` | Payment settled | Update to 'paid' |
| `pending` | Waiting payment | Keep 'pending' |
| `deny` | Payment denied | Update to 'failed' |
| `cancel` | Payment cancelled | Update to 'cancelled' |
| `expire` | Payment expired | Update to 'expired' |
| `refund` | Payment refunded | Update to 'refund' |

---

## 📊 Indexes

```sql
PRIMARY KEY (id)
UNIQUE KEY (order_number)
INDEX idx_user_id (user_id)
INDEX idx_transaction_id (transaction_id)
INDEX idx_payment_status (payment_status)
INDEX idx_order_status (status)
INDEX idx_transaction_status (transaction_status)
INDEX idx_expiry_time (expiry_time)
```

---

## 🎯 Usage Examples

### Create Order
```php
$orderModel->create(
    $userId,
    $addressId,
    $voucherId,
    $subtotal,
    $discountAmount,
    $total,
    $cartItems,
    $shippingInfo
);
```

### Update Payment Status (from Midtrans callback)
```php
$orderModel->updatePaymentStatus(
    $orderNumber,
    'paid',
    $transactionId,
    $paymentMethod
);
```

### Get Order with Items
```php
$order = $orderModel->getOrderWithItems($orderNumber);
```

---

## ✅ Compliance Checklist

- ✅ Semua field Midtrans notification tersimpan
- ✅ Signature key untuk validasi webhook
- ✅ Fraud status tracking
- ✅ Multiple payment method support
- ✅ Virtual Account details
- ✅ Convenience store details
- ✅ Refund tracking
- ✅ Address snapshot (immutable)
- ✅ Customer info for analytics
- ✅ Expiry time for auto-cancel
- ✅ Transaction timestamps
- ✅ Proper indexes for performance

---

## 🔧 Maintenance Queries

### Check Expired Orders
```sql
SELECT order_number, expiry_time, payment_status
FROM orders
WHERE payment_status = 'pending'
AND expiry_time < NOW();
```

### Revenue Report
```sql
SELECT 
    DATE(paid_at) as date,
    COUNT(*) as orders,
    SUM(total) as revenue,
    AVG(total) as avg_order_value
FROM orders
WHERE payment_status = 'paid'
GROUP BY DATE(paid_at)
ORDER BY date DESC;
```

### Payment Method Distribution
```sql
SELECT 
    payment_type,
    COUNT(*) as count,
    SUM(total) as total_amount
FROM orders
WHERE payment_status = 'paid'
GROUP BY payment_type
ORDER BY count DESC;
```

---

**Status:** ✅ READY FOR PRODUCTION  
**Last Updated:** 2025-10-24  
**Total Columns:** 49
