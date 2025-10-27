<?php

class Order {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Create a new order with items
     * 
     * @param int $userId
     * @param int $addressId
     * @param int|null $voucherId
     * @param float $subtotal
     * @param float $discountAmount
     * @param float $total
     * @param array $items - Cart items with product details
     * @param array $shippingInfo - Address snapshot
     * @return string Order number
     */
    public function create($userId, $addressId, $voucherId, $subtotal, $discountAmount, $total, $items, $shippingInfo) {
        try {
            $this->pdo->beginTransaction();
            
            // Generate order number: ORD-YYYYMMDD-XXXX
            $orderNumber = $this->generateOrderNumber();
            
            // Insert order
            $stmt = $this->pdo->prepare("
                INSERT INTO orders (
                    order_number, user_id, address_id, voucher_id,
                    subtotal, discount_amount, total,
                    shipping_name, shipping_phone, shipping_address, 
                    shipping_city, shipping_postal_code,
                    payment_status, status
                ) VALUES (
                    :order_number, :user_id, :address_id, :voucher_id,
                    :subtotal, :discount_amount, :total,
                    :shipping_name, :shipping_phone, :shipping_address,
                    :shipping_city, :shipping_postal_code,
                    'pending', 'pending'
                )
            ");
            
            $stmt->execute([
                'order_number' => $orderNumber,
                'user_id' => $userId,
                'address_id' => $addressId,
                'voucher_id' => $voucherId,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'total' => $total,
                'shipping_name' => $shippingInfo['recipient_name'] ?? $shippingInfo['label'],
                'shipping_phone' => $shippingInfo['phone'] ?? '-',
                'shipping_address' => $shippingInfo['street'] . ($shippingInfo['village'] ? ', ' . $shippingInfo['village'] : '') . ($shippingInfo['district'] ? ', ' . $shippingInfo['district'] : ''),
                'shipping_city' => $shippingInfo['regency'] ?? $shippingInfo['city'] ?? '',
                'shipping_postal_code' => $shippingInfo['postal_code'] ?? ''
            ]);
            
            $orderId = $this->pdo->lastInsertId();
            
            // Insert order items
            $stmtItem = $this->pdo->prepare("
                INSERT INTO order_items (
                    order_id, product_id, product_name, product_image, 
                    product_price, quantity, price, subtotal
                ) VALUES (
                    :order_id, :product_id, :product_name, :product_image,
                    :product_price, :quantity, :price, :subtotal
                )
            ");
            
            foreach ($items as $item) {
                $stmtItem->execute([
                    'order_id' => $orderId,
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'product_image' => $item['image'],
                    'product_price' => $item['price'],
                    'quantity' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal']
                ]);
            }
            
            $this->pdo->commit();
            
            return $orderNumber;
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Generate unique order number
     * Format: ORD-YYYYMMDD-XXXX
     */
    private function generateOrderNumber() {
        $date = date('Ymd');
        $prefix = 'ORD-' . $date . '-';
        
        // Get last order number for today
        $stmt = $this->pdo->prepare("
            SELECT order_number FROM orders 
            WHERE order_number LIKE :prefix 
            ORDER BY order_number DESC 
            LIMIT 1
        ");
        $stmt->execute(['prefix' => $prefix . '%']);
        $lastOrder = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($lastOrder) {
            // Extract sequence number and increment
            $lastSequence = (int) substr($lastOrder['order_number'], -4);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }
        
        return $prefix . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Update Snap Token from Midtrans
     */
    public function updateSnapToken($orderNumber, $snapToken) {
        $stmt = $this->pdo->prepare("
            UPDATE orders 
            SET snap_token = :snap_token 
            WHERE order_number = :order_number
        ");
        return $stmt->execute([
            'snap_token' => $snapToken,
            'order_number' => $orderNumber
        ]);
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus($orderNumber, $status, $transactionId = null, $paymentMethod = null) {
        $sql = "
            UPDATE orders 
            SET payment_status = :status,
                transaction_id = :transaction_id,
                payment_method = :payment_method";
        
        $params = [
            'status' => $status,
            'transaction_id' => $transactionId,
            'payment_method' => $paymentMethod,
            'order_number' => $orderNumber
        ];
        
        // If status is paid, set paid_at and change order status
        if ($status === 'paid') {
            $sql .= ", paid_at = CURRENT_TIMESTAMP, status = 'confirmed'";
        }
        
        $sql .= " WHERE order_number = :order_number";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Update order status
     */
    public function updateStatus($orderNumber, $status, $trackingNumber = null, $courier = null) {
        $sql = "UPDATE orders SET status = :status";
        $params = ['status' => $status, 'order_number' => $orderNumber];
        
        if ($trackingNumber) {
            $sql .= ", tracking_number = :tracking_number";
            $params['tracking_number'] = $trackingNumber;
        }
        
        if ($courier) {
            $sql .= ", courier = :courier";
            $params['courier'] = $courier;
        }
        
        $sql .= " WHERE order_number = :order_number";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Get order by order number
     */
    public function getByOrderNumber($orderNumber) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM orders 
            WHERE order_number = :order_number
        ");
        $stmt->execute(['order_number' => $orderNumber]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get order by ID
     */
    public function getById($orderId) {
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = :id");
        $stmt->execute(['id' => $orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get orders by user ID
     */
    public function getByUserId($userId, $limit = 10, $offset = 0) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM orders 
            WHERE user_id = :user_id 
            ORDER BY created_at DESC 
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get order items
     */
    public function getOrderItems($orderId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM order_items 
            WHERE order_id = :order_id
        ");
        $stmt->execute(['order_id' => $orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get order with items
     */
    public function getOrderWithItems($orderNumber) {
        $order = $this->getByOrderNumber($orderNumber);
        if ($order) {
            $order['items'] = $this->getOrderItems($order['id']);
        }
        return $order;
    }

    /**
     * Cancel order (if not paid yet)
     */
    public function cancel($orderNumber, $reason = null) {
        $order = $this->getByOrderNumber($orderNumber);
        
        if (!$order) {
            return false;
        }
        
        // Can only cancel if payment is pending
        if ($order['payment_status'] !== 'pending') {
            return false;
        }
        
        $stmt = $this->pdo->prepare("
            UPDATE orders 
            SET status = 'cancelled', 
                payment_status = 'cancelled',
                notes = CONCAT(COALESCE(notes, ''), '\nCancelled: ', :reason)
            WHERE order_number = :order_number
        ");
        
        return $stmt->execute([
            'reason' => $reason ?? 'User cancelled',
            'order_number' => $orderNumber
        ]);
    }

    /**
     * Get all orders (admin)
     */
    public function getAll($filters = [], $limit = 20, $offset = 0) {
        $sql = "SELECT o.*, u.name as customer_name, u.email as customer_email 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['status'])) {
            $sql .= " AND o.status = :status";
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['payment_status'])) {
            $sql .= " AND o.payment_status = :payment_status";
            $params['payment_status'] = $filters['payment_status'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (o.order_number LIKE :search OR u.name LIKE :search OR u.email LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        $sql .= " ORDER BY o.created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get order statistics
     */
    public function getStats() {
        $stmt = $this->pdo->query("
            SELECT 
                COUNT(*) as total_orders,
                SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid_orders,
                SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                SUM(CASE WHEN payment_status = 'paid' THEN total ELSE 0 END) as total_revenue,
                AVG(CASE WHEN payment_status = 'paid' THEN total ELSE NULL END) as average_order_value
            FROM orders
        ");
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Assign courier to an order
     * Week 3 Day 12: Courier Tracking Backend
     * 
     * @param int $orderId - Order ID
     * @param int $courierId - User ID with courier role
     * @return bool Success status
     */
    public function assignCourier($orderId, $courierId) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE orders 
                SET courier_id = :courier_id,
                    status = 'packing'
                WHERE id = :order_id
                AND payment_status = 'paid'
            ");
            
            return $stmt->execute([
                'order_id' => $orderId,
                'courier_id' => $courierId
            ]);
        } catch (\PDOException $e) {
            error_log("Order assignCourier error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all orders assigned to a courier
     * Week 3 Day 12: Courier Tracking Backend
     * 
     * @param int $courierId - Courier user ID
     * @return array Orders with customer & item details
     */
    public function getOrdersForCourier($courierId) {
        $stmt = $this->pdo->prepare("
            SELECT 
                o.id,
                o.order_number,
                o.total,
                o.status,
                o.created_at,
                o.shipping_name,
                o.shipping_phone,
                o.shipping_address,
                o.shipping_city,
                o.shipping_postal_code,
                o.shipping_latitude,
                o.shipping_longitude,
                u.name as customer_name,
                u.email as customer_email,
                u.phone as customer_phone,
                COUNT(oi.id) as item_count
            FROM orders o
            JOIN users u ON o.user_id = u.id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.courier_id = :courier_id
            AND o.payment_status = 'paid'
            AND o.status IN ('packing', 'shipped', 'delivered')
            GROUP BY o.id
            ORDER BY 
                FIELD(o.status, 'packing', 'shipped', 'delivered'),
                o.created_at DESC
        ");
        
        $stmt->execute(['courier_id' => $courierId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get order items for courier view
     * 
     * @param int $orderId - Order ID
     * @return array Order items
     */
    public function getOrderItemsForCourier($orderId) {
        $stmt = $this->pdo->prepare("
            SELECT 
                product_name,
                product_image,
                quantity,
                price,
                subtotal
            FROM order_items
            WHERE order_id = :order_id
        ");
        
        $stmt->execute(['order_id' => $orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
