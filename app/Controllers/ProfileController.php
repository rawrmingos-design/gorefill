<?php

require_once __DIR__ . '/../Models/Order.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/BaseController.php';

class ProfileController extends BaseController {
    private $orderModel;
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->orderModel = new Order($this->pdo);
        $this->userModel = new User($this->pdo);
    }

    /**
     * Display user profile with order history
     */
    public function index() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Silakan login terlebih dahulu';
            header('Location: index.php?route=auth.login');
            exit;
        }

        // Get user info
        $user = $this->userModel->getById($_SESSION['user_id']);
        
        // Get user orders (latest 10)
        $orders = $this->orderModel->getByUserId($_SESSION['user_id'], 10, 0);
        
        // Get order statistics
        $stats = $this->getUserOrderStats($_SESSION['user_id']);
        
        $data = [
            'title' => 'Profile Saya',
            'user' => $user,
            'orders' => $orders,
            'stats' => $stats
        ];
        
        $this->render('profile/index', $data);
    }

    /**
     * Display all orders (paginated)
     */
    public function orders() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Silakan login terlebih dahulu';
            header('Location: index.php?route=auth.login');
            exit;
        }

        // Pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        // Get orders
        $orders = $this->orderModel->getByUserId($_SESSION['user_id'], $limit, $offset);
        
        // Get total orders for pagination
        $totalOrders = $this->getTotalUserOrders($_SESSION['user_id']);
        $totalPages = ceil($totalOrders / $limit);
        
        $data = [
            'title' => 'Pesanan Saya',
            'orders' => $orders,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ];
        
        $this->render('profile/orders', $data);
    }

    /**
     * Display order detail
     */
    public function orderDetail() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Silakan login terlebih dahulu';
            header('Location: index.php?route=auth.login');
            exit;
        }

        if (!isset($_GET['order_number'])) {
            $_SESSION['error'] = 'Order tidak ditemukan';
            header('Location: index.php?route=profile.orders');
            exit;
        }

        $orderNumber = $_GET['order_number'];
        $order = $this->orderModel->getOrderWithItems($orderNumber);
        
        if (!$order) {
            $_SESSION['error'] = 'Order tidak ditemukan';
            header('Location: index.php?route=profile.orders');
            exit;
        }

        // Verify order belongs to user
        if ($order['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Unauthorized access';
            header('Location: index.php?route=profile.orders');
            exit;
        }

        $data = [
            'title' => 'Detail Pesanan #' . $orderNumber,
            'order' => $order
        ];
        
        $this->render('profile/order-detail', $data);
    }

    /**
     * Display invoice (printable)
     */
    public function invoice() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Silakan login terlebih dahulu';
            header('Location: index.php?route=auth.login');
            exit;
        }

        if (!isset($_GET['order_number'])) {
            $_SESSION['error'] = 'Order tidak ditemukan';
            header('Location: index.php?route=profile.orders');
            exit;
        }

        $orderNumber = $_GET['order_number'];
        $order = $this->orderModel->getOrderWithItems($orderNumber);
        
        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Order tidak ditemukan';
            header('Location: index.php?route=profile.orders');
            exit;
        }

        // Only show invoice for paid orders
        if ($order['payment_status'] != 'paid') {
            $_SESSION['error'] = 'Invoice hanya tersedia untuk pesanan yang sudah dibayar';
            header('Location: index.php?route=profile.orderDetail&order_number=' . $orderNumber);
            exit;
        }

        $data = [
            'title' => 'Invoice #' . $orderNumber,
            'order' => $order
        ];
        
        $this->render('profile/invoice', $data);
    }

    /**
     * Edit profile
     */
    public function edit() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Silakan login terlebih dahulu';
            header('Location: index.php?route=auth.login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle profile update
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            
            // Validation
            if (empty($name) || empty($email)) {
                $_SESSION['error'] = 'Nama dan email wajib diisi';
                header('Location: index.php?route=profile.edit');
                exit;
            }

            // Check if email already used by other user
            $existingUser = $this->userModel->getByEmail($email);
            if ($existingUser && $existingUser['id'] != $_SESSION['user_id']) {
                $_SESSION['error'] = 'Email sudah digunakan';
                header('Location: index.php?route=profile.edit');
                exit;
            }

            // Update user
            $updated = $this->userModel->update($_SESSION['user_id'], [
                'name' => $name,
                'email' => $email,
                'phone' => $phone
            ]);

            if ($updated) {
                // Update session
                $_SESSION['name'] = $name;
                $_SESSION['email'] = $email;
                
                $_SESSION['success'] = 'Profile berhasil diupdate';
                header('Location: index.php?route=profile');
                exit;
            } else {
                $_SESSION['error'] = 'Gagal update profile';
                header('Location: index.php?route=profile.edit');
                exit;
            }
        }

        // Show edit form
        $user = $this->userModel->getById($_SESSION['user_id']);
        
        $data = [
            'title' => 'Edit Profile',
            'user' => $user
        ];
        
        $this->render('profile/edit', $data);
    }

    /**
     * Get user order statistics
     */
    private function getUserOrderStats($userId) {
        $stmt = $this->pdo->prepare("
            SELECT 
                COUNT(*) as total_orders,
                SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as completed_orders,
                SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                SUM(CASE WHEN payment_status = 'failed' THEN 1 ELSE 0 END) as failed_orders,
                SUM(CASE WHEN status = 'shipped' THEN 1 ELSE 0 END) as shipped_orders,
                SUM(CASE WHEN status = 'packing' THEN 1 ELSE 0 END) as packing_orders,
                SUM(CASE WHEN payment_status = 'paid' THEN total ELSE 0 END) as total_spent
            FROM orders
            WHERE user_id = :user_id
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get total user orders for pagination
     */
    private function getTotalUserOrders($userId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM orders WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}
