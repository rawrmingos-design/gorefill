<?php

require_once __DIR__ . '/../Models/Order.php';
require_once __DIR__ . '/../Models/Product.php';
require_once __DIR__ . '/../Services/MailService.php';
require_once __DIR__ . '/../../config/midtrans.php';
require_once __DIR__ . '/BaseController.php';

// Autoload Midtrans
require_once __DIR__ . '/../../vendor/autoload.php';

class PaymentController extends BaseController {
    private $orderModel;
    private $productModel;
    private $midtransConfig;

    public function __construct() {
        parent::__construct();
        $this->orderModel = new Order($this->pdo);
        $this->productModel = new Product($this->pdo);
        $this->midtransConfig = require __DIR__ . '/../../config/midtrans.php';
        
        // Initialize Midtrans configuration
        \Midtrans\Config::$serverKey = $this->midtransConfig['server_key'];
        \Midtrans\Config::$isProduction = $this->midtransConfig['is_production'];
        \Midtrans\Config::$isSanitized = $this->midtransConfig['is_sanitized'];
        \Midtrans\Config::$is3ds = $this->midtransConfig['is_3ds'];
    }

    /**
     * Handle Midtrans notification callback
     * This is called by Midtrans server when payment status changes
     */
    public function callback() {
        try {
            // Get notification from Midtrans
            $notification = new \Midtrans\Notification();
            
            $orderNumber = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status ?? null;
            
            // Get order
            $order = $this->orderModel->getByOrderNumber($orderNumber);
            
            if (!$order) {
                http_response_code(404);
                echo json_encode(['message' => 'Order not found']);
                exit;
            }
            
            // Prepare complete Midtrans data array
            $midtransData = [
                'transaction_id' => $notification->transaction_id ?? null,
                'order_id' => $notification->order_id ?? null,
                'payment_type' => $notification->payment_type ?? null,
                'transaction_status' => $notification->transaction_status ?? null,
                'fraud_status' => $notification->fraud_status ?? null,
                'transaction_time' => $notification->transaction_time ?? null,
                'settlement_time' => $notification->settlement_time ?? null,
                'gross_amount' => $notification->gross_amount ?? null,
                'currency' => $notification->currency ?? 'IDR',
                'signature_key' => $notification->signature_key ?? null,
                'status_code' => $notification->status_code ?? null,
                'status_message' => $notification->status_message ?? null,
            ];
            
            // Add bank info if available
            if (isset($notification->bank)) {
                $midtransData['bank'] = $notification->bank;
            }
            
            // Add VA number if available
            if (isset($notification->va_numbers)) {
                $midtransData['va_numbers'] = $notification->va_numbers;
            }
            
            // Add payment code if available (Indomaret, Alfamart)
            if (isset($notification->payment_code)) {
                $midtransData['payment_code'] = $notification->payment_code;
            }
            
            // Add store if available
            if (isset($notification->store)) {
                $midtransData['store'] = $notification->store;
            }
            
            // Add bill key and biller code (Mandiri Bill)
            if (isset($notification->bill_key)) {
                $midtransData['bill_key'] = $notification->bill_key;
            }
            if (isset($notification->biller_code)) {
                $midtransData['biller_code'] = $notification->biller_code;
            }
            
            // Add PDF URL if available
            if (isset($notification->pdf_url)) {
                $midtransData['pdf_url'] = $notification->pdf_url;
            }
            
            // Add finish redirect URL
            if (isset($notification->finish_redirect_url)) {
                $midtransData['finish_redirect_url'] = $notification->finish_redirect_url;
            }
            
            // Add expiry time
            if (isset($notification->expiry_time)) {
                $midtransData['expiry_time'] = $notification->expiry_time;
            }
            
            // Log notification
            error_log('Midtrans Notification: ' . json_encode($midtransData));
            
            // Determine payment status based on transaction status
            $paymentStatus = 'pending';
            
            if ($transactionStatus == 'capture') {
                // For credit card transaction
                if ($fraudStatus == 'accept') {
                    $paymentStatus = 'paid';
                } else if ($fraudStatus == 'challenge') {
                    $paymentStatus = 'pending';
                }
            } else if ($transactionStatus == 'settlement') {
                // Payment success
                $paymentStatus = 'paid';
            } else if ($transactionStatus == 'pending') {
                // Waiting for payment (VA, convenience store, etc)
                $paymentStatus = 'pending';
            } else if ($transactionStatus == 'deny') {
                // Payment denied
                $paymentStatus = 'failed';
            } else if ($transactionStatus == 'expire') {
                // Payment expired
                $paymentStatus = 'expired';
            } else if ($transactionStatus == 'cancel') {
                // Payment cancelled
                $paymentStatus = 'cancelled';
            }
            
            // Update order with complete Midtrans data
            $this->orderModel->updatePaymentStatus($orderNumber, $paymentStatus, $midtransData);
            
            // ✅ NEW: Reduce product stock when payment is successful
            if ($paymentStatus === 'paid') {
                $this->reduceProductStock($orderNumber);
                
                // Send payment success email (Week 4 Day 19)
                try {
                    $order = $this->orderModel->getOrderWithItems($orderNumber);
                    if ($order) {
                        $mailService = new MailService();
                        $mailService->sendPaymentSuccess($order);
                    }
                } catch (Exception $e) {
                    error_log("Failed to send payment success email: " . $e->getMessage());
                }
            }
            
            http_response_code(200);
            echo json_encode([
                'message' => 'Notification processed',
                'order_number' => $orderNumber,
                'payment_status' => $paymentStatus
            ]);
            
        } catch (Exception $e) {
            error_log('Payment callback error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['message' => 'Internal server error', 'error' => $e->getMessage()]);
        }
    }

    /**
     * Payment success page
     */
    public function success() {
        if (!isset($_GET['order_id'])) {
            header('Location: index.php?route=home');
            exit;
        }
        
        $orderNumber = $_GET['order_id'];
        $order = $this->orderModel->getOrderWithItems($orderNumber);
        
        if (!$order) {
            $_SESSION['error'] = 'Order tidak ditemukan';
            header('Location: index.php?route=home');
            exit;
        }
        
        // Verify this order belongs to logged in user
        if (!isset($_SESSION['user_id']) || $order['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Unauthorized access';
            header('Location: index.php?route=home');
            exit;
        }
        
        $data = [
            'title' => 'Pembayaran Berhasil',
            'order' => $order
        ];
        
        $this->render('payment/success', $data);
    }

    /**
     * Payment pending page (waiting for payment)
     */
    public function pending() {
        if (!isset($_GET['order_id'])) {
            header('Location: index.php?route=home');
            exit;
        }
        
        $orderNumber = $_GET['order_id'];
        $order = $this->orderModel->getOrderWithItems($orderNumber);
        
        if (!$order) {
            $_SESSION['error'] = 'Order tidak ditemukan';
            header('Location: index.php?route=home');
            exit;
        }
        
        // Verify this order belongs to logged in user
        if (!isset($_SESSION['user_id']) || $order['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Unauthorized access';
            header('Location: index.php?route=home');
            exit;
        }
        
        $data = [
            'title' => 'Menunggu Pembayaran',
            'order' => $order
        ];
        
        $this->render('payment/pending', $data);
    }

    /**
     * Payment failed page
     */
    public function failed() {
        if (!isset($_GET['order_id'])) {
            header('Location: index.php?route=home');
            exit;
        }
        
        $orderNumber = $_GET['order_id'];
        $order = $this->orderModel->getOrderWithItems($orderNumber);
        
        if (!$order) {
            $_SESSION['error'] = 'Order tidak ditemukan';
            header('Location: index.php?route=home');
            exit;
        }
        
        // Verify this order belongs to logged in user
        if (!isset($_SESSION['user_id']) || $order['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Unauthorized access';
            header('Location: index.php?route=home');
            exit;
        }
        
        $data = [
            'title' => 'Pembayaran Gagal',
            'order' => $order
        ];
        
        $this->render('payment/failed', $data);
    }

    /**
     * Check payment status (AJAX)
     */
    public function checkStatus() {
        header('Content-Type: application/json');
        
        if (!isset($_GET['order_id'])) {
            echo json_encode(['success' => false, 'message' => 'Order ID required']);
            exit;
        }
        
        $orderNumber = $_GET['order_id'];
        $order = $this->orderModel->getByOrderNumber($orderNumber);
        
        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Order not found']);
            exit;
        }
        
        // Verify ownership
        if (!isset($_SESSION['user_id']) || $order['user_id'] != $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        try {
            // Get status from Midtrans
            $status = \Midtrans\Transaction::status($orderNumber);
            
            echo json_encode([
                'success' => true,
                'payment_status' => $order['payment_status'],
                'order_status' => $order['status'],
                'transaction_status' => $status->transaction_status,
                'payment_type' => $status->payment_type ?? null
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => true,
                'payment_status' => $order['payment_status'],
                'order_status' => $order['status']
            ]);
        }
    }
    
    /**
     * ✅ NEW: Reduce product stock after successful payment
     * Called from Midtrans callback when payment_status = 'paid'
     * 
     * @param string $orderNumber
     */
    private function reduceProductStock($orderNumber)
    {
        try {
            // Get order items
            $stmt = $this->pdo->prepare("
                SELECT product_id, quantity 
                FROM order_items 
                WHERE order_number = :order_number
            ");
            $stmt->execute(['order_number' => $orderNumber]);
            $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Reduce stock for each product
            foreach ($orderItems as $item) {
                $updateStmt = $this->pdo->prepare("
                    UPDATE products 
                    SET stock = stock - :quantity 
                    WHERE id = :product_id 
                    AND stock >= :quantity
                ");
                
                $success = $updateStmt->execute([
                    'quantity' => $item['quantity'],
                    'product_id' => $item['product_id']
                ]);
                
                if ($success) {
                    error_log("Stock reduced: Product #{$item['product_id']} by {$item['quantity']} units");
                } else {
                    error_log("Stock reduction failed: Product #{$item['product_id']} - insufficient stock");
                }
            }
            
        } catch (Exception $e) {
            error_log("Stock reduction error for order {$orderNumber}: " . $e->getMessage());
            // Don't throw - payment already successful, just log the error
        }
    }
}
