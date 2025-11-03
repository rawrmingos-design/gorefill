# 🌿 GoRefill — Website Toko Refill Ramah Lingkungan

Warna dominan: **Biru Muda & Putih**  
Website: **GoRefill**

---

## 🛍️ Fitur Pengguna (Customer)

### A. Interaksi Produk & Belanja
- **Keranjang Belanja Dinamis**
  - Tambah, kurangi, atau hapus produk secara langsung.
  - Total harga otomatis terhitung *real-time* tanpa reload halaman.
  - Efek animasi ringan (ikon keranjang bergetar / produk meluncur ke cart).
  - Data disimpan sementara di `session` PHP.

- **Wishlist / Produk Favorit**
  - Pengguna dapat menandai produk (⭐).
  - Produk favorit tampil di halaman khusus.

- **Filter & Urutkan Produk**
  - Berdasarkan kategori (Sabun, Deterjen, Refill Dapur, dll).
  - Berdasarkan harga (termurah–termahal / termahal–termurah).
  - Berdasarkan rating tertinggi / produk terpopuler.
  - Menggunakan dropdown atau tombol interaktif.

- **Voucher / Kode Promo Otomatis**
  - Database: `kode_voucher`, `diskon_persen`, `batas_penggunaan`, `tanggal_expired`.
  - Sistem otomatis menghitung potongan harga di halaman checkout.

---

### B. Checkout & Pembayaran
- **Alamat Pengiriman Interaktif**
  - Input manual (jalan, kota, kode pos) **atau**
  - Pilih langsung dari **peta interaktif (Google Maps API)**:
    - Lokasi otomatis tersimpan sebagai koordinat (latitude, longitude).
    - Geolocation mendeteksi lokasi pengguna secara otomatis.

- **Metode Pembayaran**
  - E-Wallet (Dana, OVO, GoPay)
  - Transfer Bank
  - COD (*Cash on Delivery*)

- **Voucher Diskon**
  - Total harga otomatis diperbarui sesuai kode yang dimasukkan.

- **Notifikasi Pembelian Berhasil**
  - Animasi popup sukses (SweetAlert / CSS animation).

---

### C. Riwayat & Ulasan Produk
- **Riwayat Pesanan**
  - Status: Menunggu, Dikemas, Dikirim, Selesai.
- **Sistem Review & Rating**
  - Pengguna memberi rating (⭐1–5) dan komentar.
  - Review tampil di bawah produk & dikelola oleh admin.

---

### D. Edukasi & Pengalaman Pengguna
- **Badge Ramah Lingkungan (♻️)** pada produk refill.
- **Blog Edukasi** tentang refill, zero waste, & pengurangan sampah plastik.
- **Loading Page** dengan logo GoRefill (animasi 2 detik).
- **Desain Responsif** untuk semua perangkat (mobile & tablet).

---

## 🧑‍💼 Fitur Admin

### A. Manajemen Produk & Voucher
- **CRUD Produk**
  - Tambah, ubah, dan hapus produk beserta gambar.
- **Manajemen Voucher**
  - Admin dapat membuat & mengatur masa berlaku voucher.

### B. Manajemen Pesanan & Pengguna
- **Pesanan**
  - Lihat semua pesanan + status (Menunggu, Dikirim, Selesai).
- **Pengguna & Kurir**
  - Atur akun pelanggan & kurir.
  - Tetapkan pesanan ke kurir tertentu.
  - Posisi kurir bisa dipantau **real-time** (Google Maps).

### C. Laporan & Analitik
- Grafik statistik penjualan (harian, mingguan, bulanan).
- Produk paling laku, user aktif, & total pendapatan.

---

## 🚚 Fitur Kurir
- Login ke dashboard khusus kurir.
- Melihat daftar pesanan yang harus dikirim.
- Update lokasi **real-time** di Google Maps (tracking system).
- Konfirmasi pesanan diterima oleh pelanggan.
- Dapat menghubungi pelanggan via WhatsApp.

---

## 🔄 Alur Sistem & Interaksi

### A. Halaman Awal (Landing Page)
- Logo GoRefill dengan font dan warna gradasi biru muda.
- Latar bertema ramah lingkungan (dengan animasi kanan–kiri).
- **Menu:** Beranda | Produk | Promo | Tentang Kami | Blog | FAQ | Kontak
- Efek transisi & hover halus.

### B. Login & Registrasi
- Role: **User**, **Admin**, **Kurir**
- Setelah login → diarahkan ke dashboard masing-masing.

### C. Halaman Produk
- Menampilkan daftar produk dengan gambar, harga, deskripsi.
- Filter dan urutkan produk.
- Tombol **Tambah ke Keranjang** dan **Favorit**.
- Efek hover 3D dan transisi halus.

### D. Keranjang Belanja
- Daftar produk + jumlah & harga.
- Tombol (+) dan (–) untuk ubah jumlah produk.
- Total harga otomatis terupdate.
- Tombol **Checkout** menuju halaman pembayaran.

### E. Checkout & Pembayaran
1. Isi / pilih alamat (manual atau via Maps).  
2. Pilih metode pembayaran (E-Wallet / Transfer / COD).  
3. Masukkan kode voucher.  
4. Klik **Bayar Sekarang** → popup konfirmasi.  
5. Setelah sukses:
   - **User:** “Pembayaran berhasil!”  
   - **Admin:** “Pesanan baru menunggu konfirmasi.”

### F. Pengiriman (Kurir)
- Kurir login → lihat pesanan → klik “Mulai Kirim”.
- Posisi kurir muncul di **Google Maps (real-time)**.
- Setelah barang diterima → notifikasi otomatis ke user & admin.

### G. Dashboard Admin
- Grafik transaksi, produk terlaris, user aktif, pendapatan.
- CRUD: Produk, Pesanan, User, Voucher, Blog, FAQ.
- Notifikasi aktivitas sistem.

### H. Blog & FAQ
- Artikel edukatif seputar refill & gaya hidup hijau.
- FAQ berisi pertanyaan umum: pemesanan, pembayaran, pengiriman.

### I. Footer
- Info kontak, alamat, sosial media, dan newsletter.
- Warna biru muda dan putih, tampilan bersih & lembut.

---

## 🔁 Alur Pengguna Singkat
1. Registrasi / Login  
2. Pilih Produk → Tambah ke Keranjang  
3. Checkout → Isi / Pilih Alamat (Maps)  
4. Masukkan Voucher → Pilih Pembayaran  
5. Pembayaran Sukses → Notifikasi  
6. Kurir Mengantar → User Lacak Posisi  
7. User Terima Barang → Beri Ulasan  

---

© 2025 GoRefill — *Reduce Waste, Refill with Care*
