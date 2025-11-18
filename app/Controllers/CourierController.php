<?php

require_once __DIR__ . '/../Models/CourierLocation.php';
require_once __DIR__ . '/../Models/Order.php';

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

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->courierLocationModel = new CourierLocation($pdo);
        $this->orderModel = new Order($pdo);
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
