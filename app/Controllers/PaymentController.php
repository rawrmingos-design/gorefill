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
            $this->logToFile('midtrans.callback', 'invoked', [
                'method' => $_SERVER['REQUEST_METHOD'] ?? '',
                'uri' => $_SERVER['REQUEST_URI'] ?? ''
            ]);
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
            
            $wasPaidBefore = ($order['payment_status'] === 'paid');
            
            $this->logToFile('midtrans.callback', 'order state', [
                'order_number' => $orderNumber,
                'db_payment_status' => $order['payment_status'],
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus
            ]);

            
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
            $this->logToFile('midtrans.callback', 'notification payload', $midtransData);
            
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
            
            $this->logToFile('midtrans.callback', 'paymentStatus mapping', [
                'order_number' => $orderNumber,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
                'computed_payment_status' => $paymentStatus,
                'was_paid_before' => $wasPaidBefore
            ]);

            // Update order with complete Midtrans data
            $this->orderModel->updatePaymentStatus($orderNumber, $paymentStatus, $midtransData);
            
            // ✅ NEW: Reduce product stock when payment is successful
            if ($paymentStatus === 'paid') {
                $this->logToFile('midtrans.callback', 'calling reduceProductStock', [
                    'order_number' => $orderNumber
                ]);
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
            
            $responsePayload = [
                'message' => 'Notification processed',
                'order_number' => $orderNumber,
                'payment_status' => $paymentStatus
            ];
            
            $this->logToFile('midtrans.callback', 'response', $responsePayload);
            
            http_response_code(200);
            echo json_encode($responsePayload);
            
        } catch (Exception $e) {
            $this->logToFile('midtrans.callback', 'error', [
                'message' => $e->getMessage()
            ]);
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
            // Get order to retrieve internal ID
            $order = $this->orderModel->getByOrderNumber($orderNumber);
            
            if (!$order) {
                $this->logToFile('midtrans.stock', 'order not found for reduceProductStock', [
                    'order_number' => $orderNumber
                ]);
                return;
            }
            
            $this->logToFile('midtrans.stock', 'loaded order for stock reduction', [
                'order_number' => $orderNumber,
                'order_id' => $order['id'],
                'payment_status' => $order['payment_status'],
                'status' => $order['status']
            ]);
            
            // Get order items
            $stmt = $this->pdo->prepare("
                SELECT product_id, quantity 
                FROM order_items 
                WHERE order_id = :order_id
            ");
            $stmt->execute(['order_id' => $order['id']]);
            $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!$orderItems) {
                $this->logToFile('midtrans.stock', 'no order_items found for order', [
                    'order_id' => $order['id'],
                    'order_number' => $orderNumber
                ]);
                return;
            }
            
            $this->logToFile('midtrans.stock', 'order_items loaded', $orderItems);
            
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
                    $this->logToFile('midtrans.stock', 'stock reduced', [
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity']
                    ]);
                } else {
                    $this->logToFile('midtrans.stock', 'stock reduction failed', [
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'reason' => 'insufficient stock or update failed'
                    ]);
                }
            }
            
        } catch (Exception $e) {
            $this->logToFile('midtrans.stock', 'stock reduction error', [
                'order_number' => $orderNumber,
                'message' => $e->getMessage()
            ]);
            // Don't throw - payment already successful, just log the error
        }
    }
}
