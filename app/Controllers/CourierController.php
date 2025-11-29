<?php

require_once __DIR__ . '/../Models/CourierLocation.php';
require_once __DIR__ . '/../Models/Order.php';
require_once __DIR__ . '/../Models/User.php';

/**
 * CourierController
 * Week 3 Day 12: Courier Tracking Backend
 * 
 * Handles courier GPS tracking, order management, and delivery status updates
 */
class CourierController {
    private $pdo;
    private $courierLocationModel;
    private $orderModel;
    private $userModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->courierLocationModel = new CourierLocation($pdo);
        $this->orderModel = new Order($pdo);
        $this->userModel = new User($pdo);
    }

    /**
     * Update courier GPS location via AJAX
     * POST: {latitude: float, longitude: float}
     */
    public function updateLocation() {
        header('Content-Type: application/json');

        // Validate request method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        // Validate courier authentication
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'kurir') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized. Courier access only.']);
            exit;
        }

        // Parse JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            // Fallback to POST data
            $input = $_POST;
        }

        // Validate latitude and longitude
        if (!isset($input['latitude']) || !isset($input['longitude'])) {
            echo json_encode(['success' => false, 'message' => 'Latitude and longitude required']);
            exit;
        }

        $lat = floatval($input['latitude']);
        $lng = floatval($input['longitude']);

        // Validate coordinate ranges
        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            echo json_encode(['success' => false, 'message' => 'Invalid coordinates']);
            exit;
        }

        // Update location in database
        $success = $this->courierLocationModel->updateLocation($_SESSION['user_id'], $lat, $lng);

        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Location updated successfully',
                'data' => [
                    'courier_id' => $_SESSION['user_id'],
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update location']);
        }
    }

    /**
     * Show courier profile page with forms to update profile and password
     */
    public function profile() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'kurir') {
            $_SESSION['error'] = 'Silakan login sebagai kurir terlebih dahulu';
            header('Location: index.php?route=auth.login');
            exit;
        }

        $user = $this->userModel->getById($_SESSION['user_id']);
        if (!$user) {
            $_SESSION['error'] = 'Data kurir tidak ditemukan';
            header('Location: index.php?route=courier.dashboard');
            exit;
        }

        $title = 'Courier Profile';
        require_once __DIR__ . '/../Views/courier/profile.php';
    }

    /**
     * Handle courier profile update (name, email, phone)
     */
    public function updateProfile() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'kurir') {
            $_SESSION['error'] = 'Silakan login sebagai kurir terlebih dahulu';
            header('Location: index.php?route=auth.login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?route=courier.profile');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if ($name === '' || $email === '') {
            $_SESSION['error'] = 'Nama dan email wajib diisi';
            header('Location: index.php?route=courier.profile');
            exit;
        }

        $existingUser = $this->userModel->getByEmail($email);
        if ($existingUser && $existingUser['id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Email sudah digunakan oleh pengguna lain';
            header('Location: index.php?route=courier.profile');
            exit;
        }

        $updated = $this->userModel->update($_SESSION['user_id'], [
            'name' => $name,
            'email' => $email,
            'phone' => $phone
        ]);

        if ($updated) {
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            $_SESSION['phone'] = $phone;
            $_SESSION['success'] = 'Profil kurir berhasil diperbarui';
        } else {
            $_SESSION['error'] = 'Gagal memperbarui profil kurir';
        }

        header('Location: index.php?route=courier.profile');
        exit;
    }

    /**
     * Handle courier password change
     */
    public function changePassword() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'kurir') {
            $_SESSION['error'] = 'Silakan login sebagai kurir terlebih dahulu';
            header('Location: index.php?route=auth.login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?route=courier.profile');
            exit;
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
            $_SESSION['error'] = 'Semua field password wajib diisi';
            header('Location: index.php?route=courier.profile');
            exit;
        }

        if (strlen($newPassword) < 8) {
            $_SESSION['error'] = 'Password baru minimal 8 karakter';
            header('Location: index.php?route=courier.profile');
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = 'Konfirmasi password tidak cocok';
            header('Location: index.php?route=courier.profile');
            exit;
        }

        $success = $this->userModel->changePassword(
            $_SESSION['user_id'],
            $currentPassword,
            $newPassword
        );

        if (!$success) {
            $_SESSION['error'] = 'Password saat ini salah';
        } else {
            $_SESSION['success'] = 'Password berhasil diubah';
        }

        header('Location: index.php?route=courier.profile');
        exit;
    }

    /**
     * Get current location of logged-in courier
     * GET request
     */
    public function getMyLocation() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'kurir') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $location = $this->courierLocationModel->getLocation($_SESSION['user_id']);

        if ($location) {
            echo json_encode([
                'success' => true,
                'data' => $location
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Location not found. Please update your location first.'
            ]);
        }
    }

    /**
     * Get location of a specific courier (Public API for customer tracking)
     * Week 3 Day 13: Real-Time Courier Tracking UI
     * GET: ?route=courier.getLocation&id=COURIER_ID
     */
    public function getLocation() {
        header('Content-Type: application/json');

        // Get courier ID from query parameter
        $courierId = $_GET['id'] ?? null;

        if (!$courierId) {
            echo json_encode(['success' => false, 'message' => 'Courier ID required']);
            exit;
        }

        // Get courier location
        $location = $this->courierLocationModel->getLocation($courierId);

        if ($location) {
            echo json_encode([
                'success' => true,
                'data' => [
                    'latitude' => (float) $location['lat'],
                    'longitude' => (float) $location['lng'],
                    'updated_at' => $location['updated_at']
                ]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Courier location not available'
            ]);
        }
    }

    /**
     * Get all orders assigned to logged-in courier
     * GET request
     */
    public function getMyOrders() {
        // Validate courier authentication
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'kurir') {
            header('Location: index.php?route=auth.login');
            exit;
        }

        $orders = $this->orderModel->getOrdersForCourier($_SESSION['user_id']);

        // Load view
        $title = 'My Deliveries';
        require_once __DIR__ . '/../Views/courier/dashboard.php';
    }

    /**
     * Start delivery for an order
     * POST: order_id
     */
    public function startDelivery() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'kurir') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $orderId = $_POST['order_id'] ?? null;

        if (!$orderId) {
            echo json_encode(['success' => false, 'message' => 'Order ID required']);
            exit;
        }

        // Verify order is assigned to this courier
        $order = $this->orderModel->getById($orderId);

        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Order not found']);
            exit;
        }
        
        if ($order['courier_id'] != $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'Order not assigned to you']);
            exit;
        }

        if ($order['status'] !== 'packing') {
            echo json_encode(['success' => false, 'message' => 'Order must be in packing status']);
            exit;
        }

        // Update status to 'shipped'
        $success = $this->orderModel->updateStatus($order['order_number'], 'shipped');

        if ($success) {
            // Send shipping email notification
            try {
                require_once __DIR__ . '/../Services/MailService.php';
                $mailService = new MailService();
                
                // Get full order details with items
                $orderDetails = $this->orderModel->getOrderWithItems($order['order_number']);
                if ($orderDetails) {
                    $mailService->sendShippingNotification($orderDetails);
                }
            } catch (Exception $e) {
                error_log("Failed to send shipping email: " . $e->getMessage());
                // Don't fail the request if email fails
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Delivery started successfully',
                'order_id' => $orderId,
                'order_number' => $order['order_number']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to start delivery']);
        }
    }

    /**
     * Complete delivery for an order
     * POST: order_id
     */
    public function completeDelivery() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'kurir') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $orderId = $_POST['order_id'] ?? null;

        if (!$orderId) {
            echo json_encode(['success' => false, 'message' => 'Order ID required']);
            exit;
        }

        // Verify order is assigned to this courier
        $order = $this->orderModel->getById($orderId);

        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Order not found']);
            exit;
        }

        if ($order['courier_id'] != $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'Order not assigned to you']);
            exit;
        }

        if ($order['status'] !== 'shipped') {
            echo json_encode(['success' => false, 'message' => 'Order must be in shipped status']);
            exit;
        }

        // Update status to 'delivered'
        $success = $this->orderModel->updateStatus($order['order_number'], 'delivered');

        if ($success) {
            // Send delivered email notification
            try {
                require_once __DIR__ . '/../Services/MailService.php';
                $mailService = new MailService();
                
                // Get full order details with items
                $orderDetails = $this->orderModel->getOrderWithItems($order['order_number']);
                if ($orderDetails) {
                    $mailService->sendDeliveredNotification($orderDetails);
                }
            } catch (Exception $e) {
                error_log("Failed to send delivered email: " . $e->getMessage());
                // Don't fail the request if email fails
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Delivery completed successfully',
                'order_id' => $orderId,
                'order_number' => $order['order_number']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to complete delivery']);
        }
    }

    /**
     * Dashboard view (redirect to getMyOrders)
     */
    public function index() {
        $this->getMyOrders();
    }
}
