---
trigger: always_on
---

project:
  name: "GoRefill"
  language: "PHP"
  description: >
    Sistem e-commerce PHP Native untuk layanan isi ulang air, LPG, dan kebutuhan rumah tangga.
    Menggunakan Leaflet.js untuk peta lokasi & tracking kurir, serta Midtrans API untuk pembayaran online otomatis.

rules:
  - MVC wajib diterapkan secara ketat.
  - Semua query database pakai PDO prepared statements.
  - Query SQL dilarang di file View.
  - Struktur tabel harus sesuai dengan gorefill.sql.
  - Gunakan PascalCase untuk class, camelCase untuk variabel & method.
  - Gunakan session PHP untuk login & cart.
  - Gunakan Leaflet.js untuk semua fitur peta.
  - Gunakan Midtrans API (Snap.js) untuk pembayaran.
  - Simpan konfigurasi Midtrans di `config/midtrans.php`.
  - Gunakan SweetAlert untuk notifikasi UI.
  - Gunakan password_hash() & password_verify() untuk autentikasi.
  - Sanitasi semua input user.
  - Tambahkan komentar di fungsi publik penting.
  - Catat setiap fitur baru di README.md.

relations:
  - app/Controllers/ProductController.php uses app/Models/Product.php
  - app/Controllers/CartController.php uses app/Models/Product.php and session
  - app/Controllers/CheckoutController.php uses app/Models/Order.php, Voucher.php, and Midtrans config
  - app/Controllers/PaymentController.php verifies Midtrans callback and updates orders
  - app/Controllers/AdminController.php manages CRUD via Product.php, User.php, Order.php
  - app/Controllers/CourierController.php updates courier_locations (Leaflet tracking)
  - public/index.php routes requests to all controllers

suggestions:
  coding:
    goal: "Tulis kode PHP Native dengan arsitektur MVC GoRefill."
    prefer_files: [app/Controllers, app/Models]
    constraints:
      - Gunakan PDO prepared statements.
      - Tambahkan komentar fungsi utama.
      - Ikuti flow bisnis di README.md.

  database:
    goal: "Tulis/migrasi SQL berdasarkan gorefill.sql."
    prefer_files: [migrations/gorefill.sql]
    constraints:
      - Gunakan MySQL syntax konsisten.
      - Gunakan foreign key dan ON DELETE CASCADE jika perlu.

  frontend:
    goal: "Tulis tampilan dengan TailwindCSS, SweetAlert, dan Leaflet.js."
    prefer_files: [app/Views, public/assets/js]
    constraints:
      - Gunakan fetch API untuk AJAX.
      - Gunakan Leaflet untuk peta interaktif.
      - Gunakan SweetAlert untuk feedback user.

