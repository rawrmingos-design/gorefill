<?php

require_once __DIR__ . '/../Models/Order.php';
require_once __DIR__ . '/../Models/CourierLocation.php';

/**
 * OrderController
 * Week 3 Day 13: Real-Time Courier Tracking UI
 * 
 * Handles customer order tracking and viewing
 */
class OrderController {
    private $pdo;
    private $orderModel;
    private $courierLocationModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->orderModel = new Order($pdo);
        $this->courierLocationModel = new CourierLocation($pdo);
    }

    /**
     * Display order tracking page with real-time courier location
     * Week 3 Day 13: Real-Time Courier Tracking UI
     * 
     * GET: ?route=order.track&id=ORDER_NUMBER
     */
    public function track() {
        // Validate user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?route=auth.login');
            exit;
        }

        // Get order number from query
        $orderNumber = $_GET['id'] ?? null;

        if (!$orderNumber) {
            header('Location: index.php?route=profile.orders');
            exit;
        }

        // Get order details with courier info
        $order = $this->orderModel->getByOrderNumber($orderNumber);

        if (!$order) {
            $_SESSION['error'] = 'Order not found';
            header('Location: index.php?route=profile.orders');
            exit;
        }

        // Verify order belongs to logged-in user
        if ($order['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Unauthorized access';
            header('Location: index.php?route=profile.orders');
            exit;
        }

        // Get order items
        $items = $this->orderModel->getOrderItemsForCourier($order['id']);

        // Get courier info if assigned
        $courier = null;
        $courierLocation = null;
        if ($order['courier_id']) {
            $courier = $this->getCourierInfo($order['courier_id']);
            $courierLocation = $this->courierLocationModel->getLocation($order['courier_id']);
        }

        // Load view
        $title = 'Track Order #' . $orderNumber;
        require_once __DIR__ . '/../Views/orders/track.php';
    }

    /**
     * Get courier information
     * 
     * @param int $courierId
     * @return array|false
     */
    private function getCourierInfo($courierId) {
        $stmt = $this->pdo->prepare("
            SELECT id, name, email, phone
            FROM users
            WHERE id = :id AND role = 'kurir'
        ");
        $stmt->execute(['id' => $courierId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Display user's orders list
     * GET: ?route=order.index or ?route=profile.orders
     */
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?route=auth.login');
            exit;
        }

        $orders = $this->orderModel->getByUserId($_SESSION['user_id']);

        $title = 'My Orders';
        require_once __DIR__ . '/../Views/orders/index.php';
    }

    /**
     * Display order details
     * GET: ?route=order.show&id=ORDER_NUMBER
     */
    public function show() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?route=auth.login');
            exit;
        }

        $orderNumber = $_GET['id'] ?? null;

        if (!$orderNumber) {
            header('Location: index.php?route=order.index');
            exit;
        }

        $order = $this->orderModel->getByOrderNumber($orderNumber);

        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Order not found';
            header('Location: index.php?route=order.index');
            exit;
        }

        $items = $this->orderModel->getOrderItemsForCourier($order['id']);

        $title = 'Order #' . $orderNumber;
        require_once __DIR__ . '/../Views/orders/show.php';
    }
}
