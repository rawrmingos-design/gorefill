```
Warna dominan website ini birumuda dan putih dengan nama website “ GoRefill ”
```
Fitur Pengguna / Customer

A. Interaksi Produk & Belanja

- Keranjang Belanja Dinamis
    o Pengguna bisa menambah, mengurangi, dan menghapus produk.
    o Total harga otomatis terhitung real-time tanpa reload halaman.
    o Efek animasi ringan: ikon keranjang bergetar atau produk meluncur ke cart.
    o Data sementara disimpan di session PHP.
- Wishlist / Produk Favorit
    o Pengguna dapat menandai produk (tombol ).
    o Produk favorit tampil di halaman khusus “Produk Favorit”.
- Filter & Urutkan Produk
    o Berdasarkan kategori: Sabun, Deterjen, Cairan Pel, Refill Dapur, dll.
    o Berdasarkan harga: termurah–termahal / termahal–termurah.
    o Berdasarkan rating tertinggi atau produk terpopuler.
    o Gunakan dropdown atau tombol interaktif.
- Voucher / Kode Promo Otomatis
    o Tabel database: kode_voucher, diskon_persen, batas_penggunaan,
       tanggal_expired.
    o Saat pengguna memasukkan kode voucher di checkout, sistem otomatis
       menghitung potongan harga.

B. Checkout & Pembayaran

- Alamat Pengiriman Interaktif
    o Pengguna dapat:
       1. Mengetik alamat secara manual (jalan, kota, kode pos).


2. Memilih langsung dari peta interaktif (Google Maps API)
    ➜ Lokasi otomatis tersimpan sebagai koordinat (latitude, longitude) ke
    database.
    ➜ Fitur geolocation dapat mendeteksi lokasi pengguna saat ini secara
    otomatis.
- Metode Pembayaran
o E-Wallet (Dana, OVO, Gopay)
o Transfer Bank
o COD (Bayar di Tempat)
- Voucher Diskon
o Total harga otomatis diperbarui sesuai kode voucher yang dimasukkan.
- Notifikasi Pembelian Berhasil
o Setelah pembayaran sukses, muncul popup animasi sukses (SweetAlert / CSS
animation).

C. Riwayat & Ulasan Produk

- Riwayat Pesanan
    o Menampilkan daftar transaksi sebelumnya dengan status:
       ▪ Menunggu, Dikemas, Dikirim, Selesai.
- Sistem Review & Rating
    o Setelah menerima produk, pengguna bisa memberi rating (⭐ 1 – 5) dan ulasan
       komentar.
    o Review tampil di bawah produk dan bisa dikelola admin.

D. Edukasi & Pengalaman Pengguna

- Badge Ramah Lingkungan ( ) pada produk refill.
- Halaman Blog Edukasi:


```
o Artikel singkat tentang refill, zero waste, dan pengurangan sampah plastik.
```
- Loading Page dengan Logo GoRefill
    o Animasi 2 detik sebelum masuk ke halaman utama.
- Desain Responsif
    o Tampilan menyesuaikan perangkat mobile & tablet menggunakan media query.
2. Fitur Admin / Pengelola

A. Manajemen Produk & Voucher

- CRUD Produk
    o Tambah, ubah, dan hapus produk serta gambar langsung di dashboard.
- Manajemen Voucher & Diskon
    o Admin dapat membuat dan mengatur masa berlaku voucher.

B. Manajemen Pesanan & Pengguna

- Manajemen Pesanan
    o Melihat semua pesanan masuk + status (Menunggu, Dikirim, Selesai).
- Manajemen Pengguna & Kurir
    o Melihat dan mengatur akun pelanggan dan kurir.
    o Admin dapat menetapkan pesanan ke kurir tertentu.
    o Posisi kurir bisa dipantau secara real-time melalui Google Maps.

C. Laporan & Analitik

- Tampilan grafik Statistik penjualan harian, mingguan, bulanan.
- Produk paling laku, jumlah user aktif, pendapatan total.


3. Fitur Kurir
    - Login ke dashboard kurir.
    - Melihat daftar pesanan yang harus dikirim.
    - Update lokasi real-time di Google Maps (Tracking System).
    - Konfirmasi pesanan sudah diterima pengguna.
    - Dapat menghubungi pelanggan via WhatsApp jika diperlukan.
4. Alur Sistem & Interaksi

A. Halaman Awal (Landing Page)

- Diatas logo pakai foto/logo GoRefill dengan font menarik serta warna gradiasi
- Logo bertema ramah lingkungan
- Background ramah lingkungan yang menampilkan animasi gerakan kanan kiri
- Menu: Beranda | Produk | Promo | Tentang Kami | Blog | FAQ | Kontak
- Animasi transisi & efek hover halus.

B. Login & Registrasi

- Role berbeda: User, Admin, Kurir
- Setelah login → diarahkan ke dashboard masing-masing.

C. Halaman Produk

- Menampilkan semua produk refill dengan gambar, harga, dan deskripsi.
- Ada filter & urutan produk.
- Tombol “Tambah ke Keranjang” & “Favorit”.
- Efek hover 3D, tombol interaktif, dan transisi halus.


D. Keranjang Belanja

- Daftar produk yang dipilih muncul dengan jumlah & harga.
- Tombol + dan - untuk menambah/kurangi jumlah produk.
- Hitung total harga otomatis tanpa reload.
- Tombol Checkout mengarah ke halaman pembayaran.

E. Checkout & Pembayaran

1. Isi atau pilih alamat pengiriman (manual / via peta interaktif).
2. Pilih metode pembayaran (E-Wallet / Transfer / COD).
3. Masukkan kode voucher (jika ada).
4. Klik “Bayar Sekarang” → tampil popup konfirmasi.
5. Setelah sukses → notifikasi otomatis:
    o User: “Pembayaran berhasil!”
    o Admin: “Pesanan baru menunggu konfirmasi.”

F. Pengiriman (Kurir)

- Kurir login → lihat pesanan → klik “Mulai Kirim”.
- Posisi kurir muncul di Google Maps (real-time).
- Setelah barang diterima → notifikasi dikirim ke user & admin.

G. Dashboard Admin

- Menampilkan grafik transaksi, produk laku, jumlah user, pendapatan.
- CRUD Produk, Pesanan, User, Voucher, Blog, FAQ.
- Notifikasi aktivitas sistem (pesanan baru, pengiriman, pembayaran sukses).

H. Blog & FAQ

- Artikel edukatif tentang refill & gaya hidup hijau.
- FAQ berisi pertanyaan umum seputar pemesanan, pembayaran, dan pengiriman.


I. Footer Website

- Info kontak, alamat, sosial media, dan newsletter.
- Desain warna biru muda-putih dengan tampilan bersih & lembut.
5. Alur Pengguna Singkat
1. Registrasi / Login
2. Pilih Produk → Tambah ke Keranjang
3. Checkout → Isi / Pilih Alamat (Maps)
4. Masukkan Voucher → Pilih Pembayaran
5. Pembayaran Sukses → Notifikasi
6. Kurir Mengantar → User Lacak Posisi
7. User Terima Barang → Beri Ulasan