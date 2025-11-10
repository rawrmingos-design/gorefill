# Warna dominan website ini birumuda dan putih denganNama website “GoRefill”

# Fitur Pengguna / Customer

# A. Interaksi Produkt & Belanja

- Keranjang Belanja Dinamis

$\circ$  Pengguna bisa menambah,CCCCCC, dan menghapus Produk.  
Total hora otomatis terhitung real-time tanpa reload halaman.  
$\circ$  Efek animasi ringan: ikon keranjang bergetar atau Produk meluncur ke cart.  
Data sementara disimpan di session PHP.

- wishlist / Produkt Favorit

O Pengguna dapat menandaiproduk (tombol  $\text{心}$  ）  
$\text{。}$  Produkt favorit tampil di halaman khusus "Produk Favorit".

Filter & Urutkan Produkt

o Berdasarkan kategori: Sabun, Deterjen, Cairan Pel, Refill Dapur, dlI.  
Berdasarkanonga:termurah-terminahal/termahal-terminurah.  
Berdasarkan rating tertinggi atopu Produk terpopuler.  
○ Gunakan dropdown atau tambol interaktif.

- Voucher / Kode Promo Otomatis

o Tabel database: kode_voucher, diskon_persen, batas_penggunaan, tanggal_expired.  
○ Saat pengguna memasukkan kode voucher di checkout, systemd otomatis menghitung potonganurgent.

# B. Checkout & Pembayaran

- Alamat Pengiriman Interaktif

Pengguna dapat:

1. Mengetik amat secara manual (jalan, kota, kode pos).

2. Memilih langsung dari peta interaktif (Google Maps API)

$\rightarrow$  Lokasi otomatis tersimpan sebagai koordinat (latitude, longitude) ke database.  
$\rightarrow$  Fitur geolocation dapat mendeteksi lokasi pengguna—that is, ini secara otomatis.

- Metode Pembayaran

E-Wallet (Dana, OVO, Gopay)  
Transfer Bank  
$\mathrm{COD}$  (Bayar di Tempat)

- Voucher Diskon

Totalongaotomatisdiperbarui sesuai kodevoucher yangdimasukkan.

- Notifikasi Pembelian BerHASIL

Setelah pembayaran sukses, muncul popup animasi sukses (SweetAlert / CSS animation).

# C. Riwayat & Ulasan Produkt

Riwayat Pesanan

○ Menampilkan.daftar transaksi sebelumnya dengan status:

- Menunggu, Dikemas, Dikirim, Selesai.

- Sistem Review & Rating

Setelah menerima Produk, pengguna biasa memberi rating (★1-5) dan ulasan komentar.  
Review tampil di bawah Produk dan bisa dikelola admin.

# D. Edukasi & Pengalaman Pengguna

- Badge Ramah Lingkungan ( ) padaproduk refill.  
- Halaman Blog Edukasi:

○ Artikel singkat tatsäch refill, zero waste, dan pengurangan sampah plastik.

- Loading Page情况进行 Logo GoRefill  
Animasi 2 detik sebelum masuk ke halaman utama.

- Desain Responsif

○ Tampilan menjadi perangkat mobile & tablet bagiakan media query.

# 2. Fitur Admin / Pengelola

# A. Manajemen Produkt & Voucher

CRUD Produkt

Tambah, ubah, dan hapus Produk{serta gambar langsung di dashboard.

- Manajemen Voucher & Diskon  
Admin dapat Memberuat dan inginut masa berlaku voucher.

# B. Manajemen Pesanan & Pengguna

Manajemen Pesanan

。Melihat semua pesanan masuk  $^+$  status (Menunggu, Dikirim, Selesai).

- Manajemen Pengguna & Kurir

Melihat dan menjadi akun pelanggan dan kurir.  
Admin dapat menetapkan pesanan ke kurir tertentu.  
Posisi kurir bisa dipantau secara real-time melalui Google Maps.

# C. Laporan & Analitik

- Tampilan grafik Statistik penjualan harian, mingguan, bulanan.  
- Produkt paling laku, jumlah user aktif, pendapatan total.

# 3. Fitur Kurir

- Login ke dashboard kurir.  
- Melihat daftar pesanan yang harus dikirim.  
- Update lokasi real-time di Google Maps (Tracking System).  
- Konfirmasi pesanan sudah diterima pengguna.  
- DapatCCCCCC penggungan via WhatsApp jika diperlukan.

# 4. Alur Sistem & Interaksi

# A. Halaman Awal (Landing Page)

- Diatas logo pakai Foto/logo GoRefill dengan font menarik{serta warna gradiasi  
- Logo bertema ramah lingkungan  
- Background ramah lingkungan yang menampilkan animasi gerakan kanan kiri  
- Menu: Beranda | Produkt | Promo | Tentang Kami | Blog | FAQ | Kontak  
- Animasi transisi & efek hover halus.

# B. Login & Registrasi

- Role berbeda: User, Admin, Kurir  
- Setelah login  $\rightarrow$  diarahkan ke dashboard masing-masing.

# C. Halaman Produkt

- Menampilkan semua produkt refill dengan gambar, tidak, dan deskripsi.  
- Ada filter & urutan Produk.  
Tombol "Tambah ke Keranjang" & "Favorit".  
- Efek hover 3D, tombol interaktif, dan transisi halus.

# D. Keranjang Belanja

- Daftar Produk yang dipilih muncul dengan rumah & harga.  
- Tombol + dan - untuk menambah/kurangi jumlah Produk.  
- Hitung total*harga otomatis tanpa reload.  
- Tombol Checkoutteringah ke halaman pembayaran.

# E. Checkout & Pembayaran

1. Isiederalized in the form of a command or command, including the use of a command to initiate an interaktion.  
2. Pilih metode pembayaran (E-Wallet / Transfer / COD).  
3. Masukkan kode voucher (jika ada).  
4. Klik "Bayar Sekarang"  $\rightarrow$  tampil popup konfirmasi.  
5. Setelah sukses  $\rightarrow$  notifikasi otomatis:

User: "Pembayaran berhasil!"  
Admin: "Pesananbaruumni konfirmasi."

# F. Pengiriman (Kurir)

- Kurir login  $\rightarrow$  lihat pesanan  $\rightarrow$  klik "Mulai Kirim".  
- Posisi kurir muncul di Google Maps (real-time).  
- Setelah Barang diterima  $\rightarrow$  notifikasi dikirim ke user & admin.

# G. Dashboard Admin

- Menampilkan grafik transaksi,produk laku,jumlah user,pendapatan.  
CRUD Produkt, Pesanan, User, Voucher, Blog, FAQ.  
- Notifikasi aktivitasystem (pesananantu, pengiriman, pembayaran sukses).

# H. Blog & FAQ

- Artikel edukatifARP&gaya hidup hijau.  
- FAQ berisi pertanyaan umum seputar pemesanan, pembayaran, dan pengiriman.

# I. Footer Website

- Info kontak, alamat, sosial media, dan newsletter.  
- Desain warna biru muda-putih dengan tampilan bersih & lembut.

# 5. Alur Pengguna Singkat

1. Registrasi / Login  
2. Pilih Produk  $\rightarrow$  Tambah ke Keranjang  
3. Checkout  $\rightarrow$  Isi / Pilih Alamat (Maps)  
4. Masukkan Voucher  $\rightarrow$  Pilih Pembayahan  
5. Pembavaran Sukses  $\rightarrow$  Notifikasi  
6. Kurir Mengantar  $\rightarrow$  User Lacak Posisi  
7. User Terima Barang  $\rightarrow$  Beri Ulasan
