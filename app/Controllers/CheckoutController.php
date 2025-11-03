<?php

require_once __DIR__ . '/../Models/Address.php';
require_once __DIR__ . '/../Models/Voucher.php';
require_once __DIR__ . '/../Models/Product.php';
require_once __DIR__ . '/../Models/Order.php';
require_once __DIR__ . '/../Services/MailService.php';
require_once __DIR__ . '/BaseController.php';

// Autoload Midtrans
require_once __DIR__ . '/../../vendor/autoload.php';

class CheckoutController extends BaseController {
    private $addressModel;
    private $voucherModel;
    private $productModel;
    private $orderModel;
    private $midtransConfig;

    public function __construct() {
        parent::__construct();
        $this->addressModel = new Address($this->pdo);
        $this->voucherModel = new Voucher($this->pdo);
        $this->productModel = new Product($this->pdo);
        $this->orderModel = new Order($this->pdo);
        
        // Load Midtrans config
        $this->midtransConfig = require __DIR__ . '/../../config/midtrans.php';
        
        // Set Midtrans configuration
        \Midtrans\Config::$serverKey = $this->midtransConfig['server_key'];
        \Midtrans\Config::$isProduction = $this->midtransConfig['is_production'];
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = $this->midtransConfig['is_3ds'];
    }

    /**
     * Buy Now - Quick checkout for single product
     */
    public function buyNow() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Silakan login terlebih dahulu';
            header('Location: index.php?route=auth.login');
            exit;
        }

        // Get product data from POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Invalid request';
            header('Location: index.php?route=products');
            exit;
        }

        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);

        if ($productId <= 0 || $quantity <= 0) {
            $_SESSION['error'] = 'Data produk tidak valid';
            header('Location: index.php?route=products');
            exit;
        }

        // Get product details
        $product = $this->productModel->getById($productId);
        if (!$product) {
            $_SESSION['error'] = 'Produk tidak ditemukan';
            header('Location: index.php?route=products');
            exit;
        }

        // Check stock
        if ($product['stock'] < $quantity) {
            $_SESSION['error'] = 'Stok tidak mencukupi';
            header('Location: index.php?route=product.detail&id=' . $productId);
            exit;
        }

        // Create temporary buy now session (separate from cart)
        $_SESSION['buy_now'] = [
            'product_id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'image' => $product['image'],
            'stock' => $product['stock']
        ];

        // Redirect to checkout
        header('Location: index.php?route=checkout&buy_now=1');
        exit;
    }

    /**
     * Display checkout page
     */
    public function index() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Silakan login terlebih dahulu';
            header('Location: index.php?route=auth.login');
            exit;
        }

        // Check if it's buy now or regular cart checkout
        $isBuyNow = isset($_GET['buy_now']) && $_GET['buy_now'] == 1;

        if ($isBuyNow) {
            // Buy Now checkout
            if (empty($_SESSION['buy_now'])) {
                $_SESSION['error'] = 'Data pembelian tidak ditemukan';
                header('Location: index.php?route=products');
                exit;
            }
        } else {
            // Regular cart checkout
            if (empty($_SESSION['cart'])) {
                $_SESSION['error'] = 'Keranjang belanja Anda kosong';
                header('Location: index.php?route=cart');
                exit;
            }
        }

        

        // Get cart items with product details
        $cartItems = $this->getCartItems();
        
        // Calculate cart totals
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item['price'] * $item['qty'];
        }

        // Get user addresses
        $addresses = $this->addressModel->getByUserId($_SESSION['user_id']);
        
        // Get selected address from session or default
        $selectedAddressId = $_SESSION['checkout']['address_id'] ?? null;
        if (!$selectedAddressId && !empty($addresses)) {
            // Try to get default address
            $defaultAddress = $this->addressModel->getDefaultByUserId($_SESSION['user_id']);
            $selectedAddressId = $defaultAddress['id'] ?? $addresses[0]['id'];
        }
        
        // Get available vouchers for this cart total (Week 4 Day 17)
        $availableVouchers = $this->voucherModel->getAvailableForCheckout($subtotal);

        // Get applied voucher info
        $voucherDiscount = 0;
        $voucherInfo = null;
        if (!empty($_SESSION['checkout']['voucher_id'])) {
            $voucherInfo = $this->voucherModel->getById($_SESSION['checkout']['voucher_id']);
            if ($voucherInfo) {
                $voucherDiscount = ($subtotal * $voucherInfo['discount_percent']) / 100;
            }
        }

        // Calculate total
        $total = $subtotal - $voucherDiscount;

        $data = [
            'title' => 'Checkout',
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'voucherDiscount' => $voucherDiscount,
            'voucherInfo' => $voucherInfo,
            'total' => $total,
            'addresses' => $addresses,
            'selectedAddressId' => $selectedAddressId,
            'isBuyNow' => $isBuyNow,
            'availableVouchers' => $availableVouchers
        ];

        $this->render('checkout/index', $data);
    }

    /**
     * Select address for checkout
     */
    public function selectAddress() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $addressId = $_POST['address_id'] ?? null;

        if (!$addressId) {
            echo json_encode(['success' => false, 'message' => 'Address ID required']);
            exit;
        }

        // Verify address belongs to user
        if (!$this->addressModel->belongsToUser($addressId, $_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid address']);
            exit;
        }

        // Store in session
        $_SESSION['checkout']['address_id'] = $addressId;

        echo json_encode(['success' => true, 'message' => 'Address selected']);
    }

    /**
     * Apply voucher code
     */
    public function applyVoucher() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $voucherCode = $_POST['voucher_code'] ?? '';

        if (empty($voucherCode)) {
            echo json_encode(['success' => false, 'message' => 'Kode voucher tidak boleh kosong']);
            exit;
        }

        // Calculate cart subtotal
        $cartItems = $this->getCartItems();
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item['price'] * $item['qty'];
        }

        // Validate voucher
        $validation = $this->voucherModel->validate($voucherCode, $subtotal);

        if ($validation['valid']) {
            // Store voucher in session
            $_SESSION['checkout']['voucher_id'] = $validation['voucher']['id'];
            $_SESSION['checkout']['voucher_code'] = $validation['voucher']['code'];

            echo json_encode([
                'success' => true,
                'message' => $validation['message'],
                'discount' => $validation['discount'],
                'discount_percent' => $validation['voucher']['discount_percent'],
                'total' => $subtotal - $validation['discount']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => $validation['message']
            ]);
        }
    }

    /**
     * Remove applied voucher
     */
    public function removeVoucher() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        // Remove voucher from session
        unset($_SESSION['checkout']['voucher_id']);
        unset($_SESSION['checkout']['voucher_code']);

        // Calculate new total
        $cartItems = $this->getCartItems();
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item['price'] * $item['qty'];
        }

        echo json_encode([
            'success' => true,
            'message' => 'Voucher dihapus',
            'total' => $subtotal
        ]);
    }

    /**
     * Create address via AJAX
     */
    public function createAddress() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        // Validate input
        $required = ['label', 'street', 'province', 'regency', 'district', 'village'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                $fieldNames = [
                    'label' => 'Label Alamat',
                    'street' => 'Alamat Lengkap',
                    'province' => 'Provinsi',
                    'regency' => 'Kabupaten/Kota',
                    'district' => 'Kecamatan',
                    'village' => 'Kelurahan/Desa'
                ];
                echo json_encode(['success' => false, 'message' => $fieldNames[$field] . ' wajib diisi. Pastikan Anda sudah memilih lokasi di peta.']);
                exit;
            }
        }

        $data = [
            'label' => htmlspecialchars($_POST['label']),
            'street' => htmlspecialchars($_POST['street']),
            'city' => htmlspecialchars($_POST['city'] ?? ''),
            'province' => htmlspecialchars($_POST['province'] ?? ''),
            'regency' => htmlspecialchars($_POST['regency'] ?? ''),
            'district' => htmlspecialchars($_POST['district'] ?? ''),
            'village' => htmlspecialchars($_POST['village'] ?? ''),
            'postal_code' => htmlspecialchars($_POST['postal_code'] ?? ''),
            'lat' => $_POST['latitude'] ?? $_POST['lat'] ?? null,
            'lng' => $_POST['longitude'] ?? $_POST['lng'] ?? null,
            'is_default' => isset($_POST['is_default']) ? 1 : 0
        ];

        try {
            $addressId = $this->addressModel->create($_SESSION['user_id'], $data);
            
            // Auto-select the newly created address
            $_SESSION['checkout']['address_id'] = $addressId;

            echo json_encode([
                'success' => true,
                'message' => 'Alamat berhasil ditambahkan',
                'address_id' => $addressId
            ]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal menambahkan alamat']);
        }
    }

    /**
     * Get cart items with product details
     * Supports both regular cart and buy now
     */
    private function getCartItems() {
        $cartItems = [];
        
        // Check if it's buy now checkout (check both GET and session)
        // Session check is important for AJAX calls where GET params are not available
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
                $product = $this->productModel->getById($productId);
                if ($product) {
                    $cartItems[] = [
                        'id' => $product['id'],
                        'name' => $product['name'],
                        'price' => $item['price'],
                        'qty' => $item['qty'],
                        'image' => $product['image'],
                        'subtotal' => $item['price'] * $item['qty']
                    ];
                }
            }
        }

        return $cartItems;
    }

    /**
     * Process checkout and generate Midtrans Snap Token
     */
    public function create() {
        header('Content-Type: application/json');
        
        try {
            // Validate user logged in
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'message' => 'Silakan login terlebih dahulu']);
                exit;
            }
            
            // ✅ FIX: Prevent duplicate checkout (race condition protection)
            if (isset($_SESSION['checkout_processing']) && $_SESSION['checkout_processing'] === true) {
                // Check if processing started less than 30 seconds ago (timeout protection)
                if (isset($_SESSION['checkout_processing_time']) && (time() - $_SESSION['checkout_processing_time']) < 30) {
                    echo json_encode([
                        'success' => false, 
                        'message' => 'Checkout sedang diproses, mohon tunggu...'
                    ]);
                    exit;
                }
                // If more than 30 seconds, assume previous request failed, allow retry
                unset($_SESSION['checkout_processing']);
                unset($_SESSION['checkout_processing_time']);
            }
            
            // ✅ FIX: Set processing lock
            $_SESSION['checkout_processing'] = true;
            $_SESSION['checkout_processing_time'] = time();
            
            // ✅ FIX: Clean up abandoned pending orders (older than 15 minutes without snap_token)
            // This prevents database clutter from failed checkout attempts
            $cleanupStmt = $this->pdo->prepare("
                DELETE FROM orders 
                WHERE user_id = :user_id 
                AND payment_status = 'pending' 
                AND snap_token IS NULL 
                AND created_at < DATE_SUB(NOW(), INTERVAL 15 MINUTE)
            ");
            $cleanupStmt->execute(['user_id' => $_SESSION['user_id']]);
            
            // Validate cart or buy_now not empty
            if (empty($_SESSION['cart']) && empty($_SESSION['buy_now'])) {
                // ✅ FIX: Clear lock on validation error
                unset($_SESSION['checkout_processing']);
                unset($_SESSION['checkout_processing_time']);
                echo json_encode(['success' => false, 'message' => 'Keranjang belanja kosong']);
                exit;
            }
            
            // Validate address selected
            if (empty($_SESSION['checkout']['address_id'])) {
                // ✅ FIX: Clear lock on validation error
                unset($_SESSION['checkout_processing']);
                unset($_SESSION['checkout_processing_time']);
                echo json_encode(['success' => false, 'message' => 'Silakan pilih alamat pengiriman']);
                exit;
            }
            
            // Get cart items with product details
            $cartItems = $this->getCartItems();
            
            // Validate cart items not empty
            if (empty($cartItems)) {
                // ✅ FIX: Clear lock on validation error
                unset($_SESSION['checkout_processing']);
                unset($_SESSION['checkout_processing_time']);
                echo json_encode(['success' => false, 'message' => 'Tidak ada produk untuk checkout']);
                exit;
            }
            
            // Calculate totals
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $subtotal += $item['subtotal'];
            }
            
            // Validate subtotal
            if ($subtotal <= 0) {
                // ✅ FIX: Clear lock on validation error
                unset($_SESSION['checkout_processing']);
                unset($_SESSION['checkout_processing_time']);
                echo json_encode(['success' => false, 'message' => 'Total pembelian tidak valid']);
                exit;
            }
            
            // Apply voucher discount
            $discountAmount = 0;
            $voucherId = null;
            if (!empty($_SESSION['checkout']['voucher_id'])) {
                $voucher = $this->voucherModel->getById($_SESSION['checkout']['voucher_id']);
                if ($voucher) {
                    $discountAmount = ($subtotal * $voucher['discount_percent']) / 100;
                    $voucherId = $voucher['id'];
                }
            }
            
            $total = $subtotal - $discountAmount;
            
            // Get shipping address
            $address = $this->addressModel->getById($_SESSION['checkout']['address_id']);
            if (!$address) {
                // ✅ FIX: Clear lock on validation error
                unset($_SESSION['checkout_processing']);
                unset($_SESSION['checkout_processing_time']);
                echo json_encode(['success' => false, 'message' => 'Alamat tidak valid']);
                exit;
            }
            
            // Create order
            $orderNumber = $this->orderModel->create(
                $_SESSION['user_id'],
                $address['id'],
                $voucherId,
                $subtotal,
                $discountAmount,
                $total,
                $cartItems,
                $address
            );
            
            // Generate Midtrans Snap Token
            $transactionDetails = [
                'order_id' => $orderNumber,
                'gross_amount' => (int) $total
            ];
            
            $itemDetails = [];
            foreach ($cartItems as $item) {
                $itemDetails[] = [
                    'id' => $item['id'],
                    'price' => (int) $item['price'],
                    'quantity' => $item['qty'],
                    'name' => substr($item['name'], 0, 50) // Midtrans limit 50 chars
                ];
            }
            
            // Add discount as negative item if exists
            if ($discountAmount > 0) {
                $itemDetails[] = [
                    'id' => 'DISCOUNT',
                    'price' => -(int) $discountAmount,
                    'quantity' => 1,
                    'name' => 'Voucher Discount'
                ];
            }
            
            $customerDetails = [
                'first_name' => $_SESSION['name'],
                'email' => $_SESSION['email'],
                'phone' => $address['phone'] ?? '-',
                'shipping_address' => [
                    'first_name' => $address['label'],
                    'address' => $address['street'] . ($address['village'] ? ', ' . $address['village'] : ''),
                    'city' => $address['regency'] ?? $address['city'],
                    'postal_code' => $address['postal_code'],
                    'country_code' => 'IDN'
                ]
            ];
            
            $snapParams = [
                'transaction_details' => $transactionDetails,
                'item_details' => $itemDetails,
                'customer_details' => $customerDetails,
                'enabled_payments' => $this->midtransConfig['enabled_payments'],
                'credit_card' => $this->midtransConfig['credit_card']
            ];
            
            // Get Snap Token
            $snapToken = \Midtrans\Snap::getSnapToken($snapParams);
            
            // Save snap token to order
            $this->orderModel->updateSnapToken($orderNumber, $snapToken);
            
            // Send order confirmation email (Week 4 Day 19)
            try {
                $mailService = new MailService();
                $orderData = [
                    'order_number' => $orderNumber,
                    'customer_name' => $_SESSION['name'],
                    'customer_email' => $_SESSION['email'],
                    'total_price' => $total,
                    'items' => $cartItems
                ];
                $mailService->sendOrderConfirmation($orderData);
            } catch (Exception $e) {
                error_log("Failed to send order confirmation email: " . $e->getMessage());
            }
            
            // Clear cart or buy_now after successful checkout
            if (isset($_SESSION['buy_now'])) {
                unset($_SESSION['buy_now']);
            } else {
                unset($_SESSION['cart']);
            }
            unset($_SESSION['checkout']); // Clear checkout session
            
            // ✅ FIX: Clear processing lock after success
            unset($_SESSION['checkout_processing']);
            unset($_SESSION['checkout_processing_time']);
            
            echo json_encode([
                'success' => true,
                'snap_token' => $snapToken,
                'order_number' => $orderNumber,
                'message' => 'Order created successfully'
            ]);
            exit;
        } catch (Exception $e) {
        // ✅ FIX: Clear processing lock on exception
        unset($_SESSION['checkout_processing']);
        unset($_SESSION['checkout_processing_time']);
        
        error_log('Checkout error: ' . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ]);
    }
}
}