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
     * @param array $address - Address snapshot
     * @return string Order number
     */
    public function create($userId, $addressId, $voucherId, $subtotal, $discountAmount, $total, $items, $address) {
        try {
            $this->pdo->beginTransaction();
            
            // Generate order number: ORD-YYYYMMDD-XXXX
            $orderNumber = $this->generateOrderNumber();
            
            // Get user info for customer_email and customer_phone
            $stmtUser = $this->pdo->prepare("SELECT email, phone FROM users WHERE id = :user_id");
            $stmtUser->execute(['user_id' => $userId]);
            $userInfo = $stmtUser->fetch(PDO::FETCH_ASSOC);
            
            // Insert order
            $stmt = $this->pdo->prepare("
                INSERT INTO orders (
                    order_number, user_id, address_id, voucher_id,
                    subtotal, discount_amount, total,
                    shipping_name, shipping_phone, shipping_address, 
                    shipping_city, shipping_postal_code,
                    shipping_latitude, shipping_longitude,
                    customer_email, customer_phone,
                    payment_status, status
                ) VALUES (
                    :order_number, :user_id, :address_id, :voucher_id,
                    :subtotal, :discount_amount, :total,
                    :shipping_name, :shipping_phone, :shipping_address,
                    :shipping_city, :shipping_postal_code,
                    :shipping_latitude, :shipping_longitude,
                    :customer_email, :customer_phone,
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
                'shipping_name' => $address['label'],
                'shipping_phone' => $address['phone'] ?? '-',
                'shipping_address' => $address['street'] . ($address['village'] ? ', ' . $address['village'] : '') . ($address['district'] ? ', ' . $address['district'] : ''),
                'shipping_city' => $address['regency'] ?? $address['city'] ?? '',
                'shipping_postal_code' => $address['postal_code'] ?? '',
                'shipping_latitude' => $address['lat'] ?? null,
                'shipping_longitude' => $address['lng'] ?? null,
                'customer_email' => $userInfo['email'] ?? null,
                'customer_phone' => $userInfo['phone'] ?? null
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
     * Format: ORD-YYYYMMDD-HHMMSS-RND (100% unique with timestamp + random)
     * 
     * ✅ FIX: Added timestamp + random to prevent Midtrans "order_id sudah digunakan" error
     * This ensures order_number is always unique even if:
     * - Order created but Midtrans token generation failed
     * - User retries checkout
     * - Race condition occurs
     */
    private function generateOrderNumber() {
        // Format: ORD-20251028-143052-A3F9
        // - YYYYMMDD: Date
        // - HHMMSS: Time (second precision)
        // - RND: 4 random alphanumeric chars
        
        $date = date('Ymd');
        $time = date('His'); // Hour, Minute, Second
        $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 4)); // 4 random chars
        
        $orderNumber = "ORD-{$date}-{$time}-{$random}";
        
        // Extra safety: Check if order_number already exists (extremely unlikely)
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM orders WHERE order_number = :order_number");
        $stmt->execute(['order_number' => $orderNumber]);
        $exists = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // If somehow exists (1 in billion chance), regenerate with new random
        if ($exists > 0) {
            usleep(100000); // Wait 100ms to get different timestamp
            return $this->generateOrderNumber(); // Recursive call
        }
        
        return $orderNumber;
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
     * Update payment status with Midtrans notification data
     * 
     * @param string $orderNumber
     * @param string $status - payment_status (paid, pending, failed, etc)
     * @param array $midtransData - Complete Midtrans notification data
     * @return bool
     */
    public function updatePaymentStatus($orderNumber, $status, $midtransData = []) {
        $sql = "
            UPDATE orders 
            SET payment_status = :status";
        
        $params = [
            'status' => $status,
            'order_number' => $orderNumber
        ];
        
        // Add Midtrans fields if provided
        if (!empty($midtransData['transaction_id'])) {
            $sql .= ", transaction_id = :transaction_id";
            $params['transaction_id'] = $midtransData['transaction_id'];
        }
        
        if (!empty($midtransData['payment_type'])) {
            $sql .= ", payment_type = :payment_type, payment_method = :payment_method";
            $params['payment_type'] = $midtransData['payment_type'];
            $params['payment_method'] = $midtransData['payment_type']; // Same for backward compatibility
        }
        
        if (!empty($midtransData['transaction_status'])) {
            $sql .= ", transaction_status = :transaction_status, midtrans_status = :midtrans_status";
            $params['transaction_status'] = $midtransData['transaction_status'];
            $params['midtrans_status'] = $midtransData['transaction_status'];
        }
        
        if (!empty($midtransData['fraud_status'])) {
            $sql .= ", fraud_status = :fraud_status";
            $params['fraud_status'] = $midtransData['fraud_status'];
        }
        
        if (!empty($midtransData['transaction_time'])) {
            $sql .= ", transaction_time = :transaction_time";
            $params['transaction_time'] = $midtransData['transaction_time'];
        }
        
        if (!empty($midtransData['settlement_time'])) {
            $sql .= ", settlement_time = :settlement_time";
            $params['settlement_time'] = $midtransData['settlement_time'];
        }
        
        if (!empty($midtransData['gross_amount'])) {
            $sql .= ", gross_amount = :gross_amount";
            $params['gross_amount'] = $midtransData['gross_amount'];
        }
        
        if (!empty($midtransData['currency'])) {
            $sql .= ", currency = :currency";
            $params['currency'] = $midtransData['currency'];
        }
        
        if (!empty($midtransData['signature_key'])) {
            $sql .= ", signature_key = :signature_key";
            $params['signature_key'] = $midtransData['signature_key'];
        }
        
        if (!empty($midtransData['bank'])) {
            $sql .= ", bank = :bank";
            $params['bank'] = $midtransData['bank'];
        }
        
        if (!empty($midtransData['va_numbers'])) {
            $sql .= ", va_number = :va_number";
            $params['va_number'] = $midtransData['va_numbers'][0]['va_number'] ?? null;
        }
        
        if (!empty($midtransData['bill_key'])) {
            $sql .= ", bill_key = :bill_key";
            $params['bill_key'] = $midtransData['bill_key'];
        }
        
        if (!empty($midtransData['biller_code'])) {
            $sql .= ", biller_code = :biller_code";
            $params['biller_code'] = $midtransData['biller_code'];
        }
        
        if (!empty($midtransData['pdf_url'])) {
            $sql .= ", pdf_url = :pdf_url";
            $params['pdf_url'] = $midtransData['pdf_url'];
        }
        
        if (!empty($midtransData['finish_redirect_url'])) {
            $sql .= ", finish_redirect_url = :finish_redirect_url";
            $params['finish_redirect_url'] = $midtransData['finish_redirect_url'];
        }
        
        if (!empty($midtransData['expiry_time'])) {
            $sql .= ", expiry_time = :expiry_time";
            $params['expiry_time'] = $midtransData['expiry_time'];
        }
        
        if (!empty($midtransData['store'])) {
            $sql .= ", store = :store";
            $params['store'] = $midtransData['store'];
        }
        
        if (!empty($midtransData['payment_code'])) {
            $sql .= ", payment_code = :payment_code";
            $params['payment_code'] = $midtransData['payment_code'];
        }
        
        // Save complete callback data as JSON
        $sql .= ", callback_data = :callback_data";
        $params['callback_data'] = json_encode($midtransData);
        
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
