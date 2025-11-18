<?php

require_once __DIR__ . '/BaseController.php';

class FaqController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $faqs = $this->getFaqData();
        
        $this->render('faq/index', [
            'title' => 'FAQ - Pertanyaan Umum',
            'faqs' => $faqs
        ]);
    }

    private function getFaqData()
    {
        return [
            [
                'category' => 'Pemesanan',
                'icon' => 'fa-shopping-cart',
                'color' => 'blue',
                'questions' => [
                    [
                        'question' => 'Bagaimana cara memesan produk di GoRefill?',
                        'answer' => 'Pilih produk yang Anda inginkan, klik "Tambah ke Keranjang", lalu lanjutkan ke halaman checkout. Pilih alamat pengiriman, terapkan voucher jika ada, dan lakukan pembayaran melalui Midtrans.'
                    ],
                    [
                        'question' => 'Apakah saya harus membuat akun untuk memesan?',
                        'answer' => 'Ya, Anda harus login atau mendaftar terlebih dahulu untuk melakukan pemesanan. Ini memudahkan Anda untuk tracking pesanan dan mendapatkan notifikasi.'
                    ],
                    [
                        'question' => 'Bagaimana cara menambahkan produk ke favorit?',
                        'answer' => 'Klik icon hati (❤️) pada produk yang Anda suka. Produk favorit dapat Anda lihat di halaman Favorit untuk mempermudah pembelian selanjutnya.'
                    ]
                ]
            ],
            [
                'category' => 'Pembayaran',
                'icon' => 'fa-credit-card',
                'color' => 'green',
                'questions' => [
                    [
                        'question' => 'Metode pembayaran apa saja yang tersedia?',
                        'answer' => 'Kami menerima berbagai metode pembayaran melalui Midtrans: Kartu Kredit/Debit, GoPay, ShopeePay, Transfer Bank (BCA, Mandiri, BNI, BRI), Alfamart, dan Indomaret.'
                    ],
                    [
                        'question' => 'Apakah pembayaran aman?',
                        'answer' => 'Ya, sangat aman! Kami menggunakan Midtrans yang telah tersertifikasi PCI-DSS dengan enkripsi tingkat bank. Data pembayaran Anda tidak kami simpan.'
                    ],
                    [
                        'question' => 'Berapa lama batas waktu pembayaran?',
                        'answer' => 'Batas waktu pembayaran tergantung metode yang dipilih. Untuk Virtual Account dan convenience store biasanya 24 jam. Untuk e-wallet seperti GoPay adalah 15 menit.'
                    ],
                    [
                        'question' => 'Bagaimana cara menggunakan voucher diskon?',
                        'answer' => 'Masukkan kode voucher di halaman checkout sebelum melakukan pembayaran. Diskon akan otomatis diterapkan jika voucher valid dan memenuhi syarat minimum pembelian.'
                    ]
                ]
            ],
            [
                'category' => 'Pengiriman',
                'icon' => 'fa-shipping-fast',
                'color' => 'purple',
                'questions' => [
                    [
                        'question' => 'Berapa lama waktu pengiriman?',
                        'answer' => 'Waktu pengiriman biasanya 1-3 hari kerja tergantung lokasi Anda. Kami akan mengirimkan notifikasi email ketika pesanan dikirim dan Anda dapat tracking secara real-time.'
                    ],
                    [
                        'question' => 'Bagaimana cara tracking pesanan saya?',
                        'answer' => 'Masuk ke akun Anda, pilih menu "Pesanan Saya", lalu klik detail pesanan yang ingin di-tracking. Anda akan melihat status real-time dan lokasi kurir jika sedang dalam pengiriman.'
                    ],
                    [
                        'question' => 'Apakah bisa mengubah alamat setelah order?',
                        'answer' => 'Maaf, alamat tidak bisa diubah setelah order dikonfirmasi. Pastikan alamat pengiriman sudah benar sebelum melakukan pembayaran.'
                    ],
                    [
                        'question' => 'Bagaimana jika pesanan tidak sampai?',
                        'answer' => 'Jika pesanan tidak sampai dalam waktu yang dijanjikan, segera hubungi customer service kami melalui halaman Kontak. Kami akan membantu menyelesaikan masalah Anda.'
                    ]
                ]
            ],
            [
                'category' => 'Produk & Refill',
                'icon' => 'fa-recycle',
                'color' => 'teal',
                'questions' => [
                    [
                        'question' => 'Apa itu sistem refill?',
                        'answer' => 'Sistem refill adalah konsep ramah lingkungan di mana Anda dapat mengisi ulang wadah produk yang sudah habis, mengurangi sampah plastik dan lebih hemat.'
                    ],
                    [
                        'question' => 'Apakah semua produk bisa di-refill?',
                        'answer' => 'Tidak semua produk mendukung refill. Produk yang bisa di-refill akan ditandai dengan badge 🟢 Eco-Friendly. Cek detail produk untuk informasi lebih lanjut.'
                    ],
                    [
                        'question' => 'Bagaimana cara memberikan review produk?',
                        'answer' => 'Anda hanya bisa memberikan review setelah membeli dan menerima produk. Buka halaman detail produk, scroll ke bagian Reviews, lalu klik "Tulis Review Anda".'
                    ]
                ]
            ],
            [
                'category' => 'Akun & Keamanan',
                'icon' => 'fa-user-shield',
                'color' => 'red',
                'questions' => [
                    [
                        'question' => 'Bagaimana cara reset password?',
                        'answer' => 'Klik "Lupa Password" di halaman login, masukkan email Anda, dan kami akan mengirimkan link reset password ke email Anda.'
                    ],
                    [
                        'question' => 'Apakah data pribadi saya aman?',
                        'answer' => 'Ya, kami sangat menjaga keamanan data Anda. Data pribadi dan transaksi dilindungi dengan enkripsi SSL dan tidak akan dibagikan ke pihak ketiga tanpa izin Anda.'
                    ],
                    [
                        'question' => 'Bagaimana cara menghubungi customer service?',
                        'answer' => 'Anda dapat menghubungi kami melalui halaman Kontak, atau email ke support@gorefill.com. Kami akan merespon dalam 1x24 jam pada hari kerja.'
                    ]
                ]
            ]
        ];
    }
}
