<?php

require_once __DIR__ . '/../Models/Order.php';
require_once __DIR__ . '/BaseController.php';

// Autoload Midtrans
require_once __DIR__ . '/../../vendor/autoload.php';

class PaymentController extends BaseController {
    private $orderModel;
    private $midtransConfig;

    public function __construct() {
        parent::__construct();
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
     * Handle Midtrans notification callback
     * This is called by Midtrans server when payment status changes
     */
    public function callback() {
        try {
            // Get notification from Midtrans
            $notification = new \Midtrans\Notification();
            
            $orderNumber = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status;
            $paymentType = $notification->payment_type;
            $transactionId = $notification->transaction_id;
            
            // Get order
            $order = $this->orderModel->getByOrderNumber($orderNumber);
            
            if (!$order) {
                http_response_code(404);
                echo json_encode(['message' => 'Order not found']);
                exit;
            }
            
            // Log notification
            error_log('Midtrans Notification: ' . json_encode([
                'order_number' => $orderNumber,
                'transaction_status' => $transactionStatus,
                'payment_type' => $paymentType,
                'transaction_id' => $transactionId
            ]));
            
            // Process based on transaction status
            if ($transactionStatus == 'capture') {
                // For credit card transaction
                if ($fraudStatus == 'accept') {
                    $this->orderModel->updatePaymentStatus($orderNumber, 'paid', $transactionId, $paymentType);
                } else if ($fraudStatus == 'challenge') {
                    $this->orderModel->updatePaymentStatus($orderNumber, 'pending', $transactionId, $paymentType);
                }
            } else if ($transactionStatus == 'settlement') {
                // Payment success
                $this->orderModel->updatePaymentStatus($orderNumber, 'paid', $transactionId, $paymentType);
            } else if ($transactionStatus == 'pending') {
                // Waiting for payment (VA, etc)
                $this->orderModel->updatePaymentStatus($orderNumber, 'pending', $transactionId, $paymentType);
            } else if ($transactionStatus == 'deny') {
                // Payment denied
                $this->orderModel->updatePaymentStatus($orderNumber, 'failed', $transactionId, $paymentType);
            } else if ($transactionStatus == 'expire') {
                // Payment expired
                $this->orderModel->updatePaymentStatus($orderNumber, 'expired', $transactionId, $paymentType);
            } else if ($transactionStatus == 'cancel') {
                // Payment cancelled
                $this->orderModel->updatePaymentStatus($orderNumber, 'cancelled', $transactionId, $paymentType);
            }
            
            http_response_code(200);
            echo json_encode(['message' => 'Notification processed']);
            
        } catch (Exception $e) {
            error_log('Payment callback error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['message' => 'Internal server error']);
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
}
