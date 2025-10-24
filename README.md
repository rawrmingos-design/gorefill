# GoRefill (PHP Native Project)

Website layanan isi ulang air, LPG, dan produk rumah tangga dengan sistem e-commerce sederhana.  
Dibuat menggunakan **PHP Native + MySQL**, mendukung multi-role (Admin, Kurir, User), tracking kurir realtime, voucher, dan sistem pembayaran.

---

## ⚙️ TECH STACK
- **Backend:** PHP 8.x (Native, OOP structured)
- **Database:** MySQL 8.x
- **Frontend:** HTML5, TailwindCSS, Vanilla JavaScript
- **Map Integration:** Leaflet.js + OpenStreetMap
- **Payment Gateway:** Midtrans API (Snap.js & Server-side REST)
- **Additional Tools:** SweetAlert2, Fetch API (AJAX), PHPMailer (optional)

---

## 🚀 Instalasi & Cara Menjalankan

### Prasyarat
- PHP 8.x dan Composer
- MySQL 8.x (atau MariaDB kompatibel)
- Ekstensi PHP `pdo_mysql`, `curl`, dan `mbstring`
- Node.js (opsional, hanya jika ingin build ulang asset Tailwind)

### Langkah Instalasi
1. **Clone repository**
   ```bash
   git clone https://github.com/username/gorefill.git
   cd gorefill
   ```
2. **Install dependency PHP**
   ```bash
   composer install
   ```
3. **Konfigurasi Midtrans**
   ```bash
   cp config/midtrans.example.php config/midtrans.php
   # Edit file dan masukkan SERVER_KEY & CLIENT_KEY Anda
   ```
4. **Import database**
   ```bash
   # Buat database baru mis. gorefill
   mysql -u root -p gorefill < migrations/egymarke_gorefill.sql
   ```
5. **Set credential database** di `config/config.php`:
   ```php
   return [
       'db_host' => '127.0.0.1',
       'db_name' => 'gorefill',
       'db_user' => 'root',
       'db_pass' => '',
   ];
   ```
6. **Pastikan folder upload writable**
   ```bash
   chmod -R 775 uploads/
   ```

### Menjalankan Project (Localhost)
1. Jalankan built-in PHP server:
   ```bash
   php -S localhost:8000 -t public
   ```
   atau gunakan XAMPP/Laragon. Letakkan folder gorefill di `wwww` (Laragon) atau `htdocs` (XAMPP).
2. Buka browser ke `http://localhost:8000`.
3. **Akun Admin Default** (tersimpan di file migrasi):
   - Email: `admin@gorefill.test`
   - Password: `admin123`

---

## 🧱 PROJECT STRUCTURE
```
/gorefill
├── /app
│ ├── /Controllers
│ ├── /Models
│ ├── /Views
│ ├── bootstrap.php
│
├── /config
│ ├── config.php
│ └── midtrans.php
│
├── /public
│ ├── index.php ← Front Controller (Routing)
│ ├── /assets
│
├── /uploads
├── /migrations
│ └── egymarke_gorefill.sql
│
├── README.md
└── .windsurf/mcp.yaml
```


---

## 🧭 BUSINESS LOGIC FLOW

### 🧍 USER FLOW
1. Register / Login → Session Auth
2. Browse Products → Filter by category / price / eco-badge
3. Add to Cart → AJAX update total realtime
4. Apply Voucher → Server-side validation
5. Checkout:
   - Choose Address (manual atau klik titik di peta Leaflet)
   - Select Payment Method → Midtrans Snap popup
6. Payment Success → Callback Midtrans → Update `orders.payment_status = 'paid'`
7. Receive Order → Status “Delivered”
8. Review Product → Rating 1–5
9. Add to Favorites (Wishlist)

---

### 🧑‍💼 ADMIN FLOW
1. Dashboard → Overview (Total Orders, Sales, Top Products)
2. CRUD:
   - Products
   - Users (Assign Kurir)
   - Vouchers
3. Manage Orders → Change Status (packing/shipped/delivered)
4. Assign Courier to Orders
5. View Reports (Sales, Most Ordered Products)

---

### 🚚 COURIER FLOW
1. Login as “kurir”
2. View Assigned Orders
3. Start Delivery → Browser akan mengirim lokasi otomatis via JS `navigator.geolocation.watchPosition`
4. Data dikirim ke backend (`courier.update_location`) dan disimpan di `courier_locations`
5. User & Admin dapat melihat posisi kurir di **peta Leaflet.js** (marker diperbarui realtime)
6. Complete Delivery → Status order jadi “delivered”

---

## 🗺️ MAPS INTEGRATION (Leaflet.js)
Digunakan di dua bagian:
1. **Alamat Pengguna (Checkout Form)**  
   - Peta Leaflet terbuka → user klik titik di peta → hasil `lat,lng` disimpan di `addresses` table.
2. **Tracking Kurir (Admin & User Dashboard)**  
   - Menampilkan marker kurir berdasarkan tabel `courier_locations`.
   - Data diperbarui via AJAX setiap beberapa detik.

**Leaflet Integration Example:**
```html
<div id="map" style="height: 300px;"></div>
<script>
  const map = L.map('map').setView([-6.9667, 110.4167], 13); // default Semarang
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
  let marker;
  map.on('click', e => {
    if (marker) map.removeLayer(marker);
    marker = L.marker(e.latlng).addTo(map);
    document.querySelector('#lat').value = e.latlng.lat;
    document.querySelector('#lng').value = e.latlng.lng;
  });
</script>
```
---

## 💰 PAYMENT GATEWAY (Midtrans)

Gunakan Midtrans Snap API untuk transaksi otomatis.

🔑 Setup:

Buat akun di https://dashboard.midtrans.com

Ambil Server Key dan Client Key

Simpan di /config/midtrans.php seperti ini:
```
<?php
return [
  'is_production' => false,
  'server_key' => 'SB-Mid-server-xxxxxx',
  'client_key' => 'SB-Mid-client-xxxxxx',
];
```

🧩 Payment Flow

Saat user checkout → sistem hitung total, buat orders dengan payment_status = 'unpaid'.

Generate Snap Token via Midtrans API:
```
$payload = [
  'transaction_details' => [
    'order_id' => $orderId,
    'gross_amount' => $totalAmount,
  ],
  'customer_details' => [
    'first_name' => $user['name'],
    'email' => $user['email'],
  ],
];
$snapToken = \Midtrans\Snap::getSnapToken($payload);
```

Kirim token ke frontend → tampilkan popup Snap:
```
snap.pay(snapToken, {
  onSuccess: function(result){ window.location.href='/index.php?route=payment.success&id='+result.order_id; },
  onPending: function(result){ alert('Menunggu pembayaran...'); },
  onError: function(result){ alert('Pembayaran gagal'); },
});
```

Midtrans mengirim callback (webhook) ke:
```
/index.php?route=payment.callback
```

Backend akan memverifikasi signature dan mengupdate:
```
UPDATE orders SET payment_status='paid', status='packing' WHERE id=?
```
---

## 🧩 DATABASE SCHEMA

Lihat /migrations/egymarke_gorefill.sql
```
Tabel utama:

users — admin, user, kurir

products, product_reviews

orders, order_items

vouchers

addresses

favorites

courier_locations
```
---

## 🔄 CONTROLLERS & LOGIC
Controller	Fungsi Utama
```
AuthController          Register/Login/Logout
ProductController	    List, Filter, Detail, Review
CartController	        Add/Update/Delete (Session AJAX)
CheckoutController	    Voucher, Address, Midtrans Payment
PaymentController	    Handle Webhook Callback & Payment Status
AdminController	        Dashboard, CRUD, Reports
CourierController	    Update Location (Leaflet Tracking)
```
🌐 ROUTING CONVENTION

Example URLs
```
/index.php?route=home
/index.php?route=product.detail&id=2
/index.php?route=cart.add
/index.php?route=checkout
/index.php?route=payment.callback
```

Routing Dispatcher (index.php)
```
$route = $_GET['route'] ?? 'home';
$method = $_SERVER['REQUEST_METHOD'];

switch($route){
  case 'home': (new ProductController)->index(); break;
  case 'cart.add': (new CartController)->add(); break;
  case 'checkout': (new CheckoutController)->create(); break;
  case 'payment.callback': (new PaymentController)->callback(); break;
  default: http_response_code(404);
}
```
---

## 🧠 DEVELOPMENT RULES

Arsitektur MVC wajib konsisten.
```
Semua query pakai prepared statements (PDO).

Tidak ada SQL langsung di View.

Gunakan Leaflet untuk semua peta (bukan Google Maps).

Gunakan Midtrans untuk semua pembayaran online.

Gunakan SweetAlert untuk notifikasi UI.

Gunakan session untuk Auth dan Cart.

Sanitasi semua input (htmlspecialchars, filter_input).

Gunakan password_hash() & password_verify().
```
---

## 📊 REPORT & ANALYTICS

Top-selling products:
```
SELECT p.name, SUM(oi.qty) AS sold
FROM order_items oi
JOIN products p ON oi.product_id = p.id
GROUP BY oi.product_id
ORDER BY sold DESC
LIMIT 10;
```

Daily revenue:
```
SELECT DATE(created_at) AS date, SUM(total) AS revenue
FROM orders
WHERE payment_status = 'paid'
GROUP BY DATE(created_at);
```
---

## 📅 PHASED DEVELOPMENT PLAN
Phase 1 — MVP
```
Auth (Register/Login)

Product Listing

Cart (Session)

Checkout (Manual + Midtrans Sandbox)

Admin CRUD Products
```
Phase 2
```
Voucher System

Leaflet Address Picker

Wishlist

Order Tracking (Courier → User)
```
Phase 3
```
Midtrans Webhook Verification

Admin Dashboard Chart

Real-time Courier Map Update (AJAX polling)
```