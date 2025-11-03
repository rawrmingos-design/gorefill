<?php

class Voucher {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get voucher by code
     */
    public function getByCode($code) {
        $stmt = $this->pdo->prepare("SELECT * FROM vouchers WHERE code = :code");
        $stmt->execute(['code' => strtoupper($code)]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get voucher by ID
     */
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM vouchers WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Validate voucher code and return validation result
     * 
     * @param string $code Voucher code
     * @param float $totalAmount Total purchase amount
     * @return array ['valid' => bool, 'message' => string, 'voucher' => array|null, 'discount' => float]
     */
    public function validate($code, $totalAmount) {
        $voucher = $this->getByCode($code);

        // Check if voucher exists
        if (!$voucher) {
            return [
                'valid' => false,
                'message' => 'Kode voucher tidak ditemukan',
                'voucher' => null,
                'discount' => 0
            ];
        }

        // Check if voucher is expired
        if ($voucher['expires_at'] && strtotime($voucher['expires_at']) < time()) {
            return [
                'valid' => false,
                'message' => 'Voucher sudah kadaluarsa',
                'voucher' => null,
                'discount' => 0
            ];
        }

        // Check if voucher usage limit is reached
        if ($voucher['used_count'] >= $voucher['usage_limit']) {
            return [
                'valid' => false,
                'message' => 'Voucher sudah mencapai batas penggunaan',
                'voucher' => null,
                'discount' => 0
            ];
        }

        // Check minimum purchase requirement
        $minPurchase = $voucher['min_purchase'] ?? 0;
        if ($totalAmount < $minPurchase) {
            return [
                'valid' => false,
                'message' => 'Minimum pembelian Rp ' . number_format($minPurchase, 0, ',', '.') . ' untuk menggunakan voucher ini',
                'voucher' => null,
                'discount' => 0
            ];
        }

        // Calculate discount
        $discount = ($totalAmount * $voucher['discount_percent']) / 100;

        return [
            'valid' => true,
            'message' => 'Voucher berhasil diterapkan',
            'voucher' => $voucher,
            'discount' => $discount
        ];
    }

    /**
     * Increment usage count for a voucher
     */
    public function use($voucherId) {
        $stmt = $this->pdo->prepare("
            UPDATE vouchers 
            SET used_count = used_count + 1 
            WHERE id = :id
        ");
        return $stmt->execute(['id' => $voucherId]);
    }

    /**
     * Get all active vouchers
     */
    public function getActive() {
        $stmt = $this->pdo->prepare("
            SELECT * FROM vouchers 
            WHERE (expires_at IS NULL OR expires_at >= CURDATE())
            AND used_count < usage_limit
            ORDER BY discount_percent DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new voucher (admin only)
     */
    public function create($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO vouchers (code, discount_percent, min_purchase, usage_limit, expires_at) 
            VALUES (:code, :discount_percent, :min_purchase, :usage_limit, :expires_at)
        ");
        
        return $stmt->execute([
            'code' => strtoupper($data['code']),
            'discount_percent' => $data['discount_percent'],
            'min_purchase' => $data['min_purchase'] ?? 0,
            'usage_limit' => $data['usage_limit'] ?? 1,
            'expires_at' => $data['expires_at'] ?? null
        ]);
    }

    /**
     * Update voucher (admin only)
     */
    public function update($id, $data) {
        $stmt = $this->pdo->prepare("
            UPDATE vouchers 
            SET code = :code, 
                discount_percent = :discount_percent, 
                min_purchase = :min_purchase,
                usage_limit = :usage_limit, 
                expires_at = :expires_at
            WHERE id = :id
        ");
        
        return $stmt->execute([
            'id' => $id,
            'code' => strtoupper($data['code']),
            'discount_percent' => $data['discount_percent'],
            'min_purchase' => $data['min_purchase'] ?? 0,
            'usage_limit' => $data['usage_limit'],
            'expires_at' => $data['expires_at']
        ]);
    }

    /**
     * Delete voucher (admin only)
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM vouchers WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    
    /**
     * Get all vouchers with pagination
     * 
     * @param int $limit Limit per page
     * @param int $offset Offset for pagination
     * @return array List of vouchers
     */
    public function getAll($limit = 50, $offset = 0) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    v.*,
                    (v.usage_limit - v.used_count) as remaining_uses,
                    CASE 
                        WHEN v.expires_at IS NULL THEN 'No Expiry'
                        WHEN v.expires_at < CURDATE() THEN 'Expired'
                        WHEN v.used_count >= v.usage_limit THEN 'Used Up'
                        ELSE 'Active'
                    END as status
                FROM vouchers v
                ORDER BY v.created_at DESC
                LIMIT :limit OFFSET :offset
            ");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get all vouchers error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get total voucher count
     * 
     * @return int Total vouchers
     */
    public function getTotalCount() {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM vouchers");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $result['total'];
        } catch (PDOException $e) {
            error_log("Get voucher count error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get voucher usage history
     * Shows which orders used this voucher
     * 
     * @param int $voucherId Voucher ID
     * @return array Usage history
     */
    public function getUsageHistory($voucherId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    o.order_number,
                    o.user_id,
                    u.name as user_name,
                    u.email as user_email,
                    o.total_price,
                    o.discount_amount,
                    o.voucher_code,
                    o.created_at as used_at
                FROM orders o
                INNER JOIN users u ON o.user_id = u.id
                WHERE o.voucher_code = (
                    SELECT code FROM vouchers WHERE id = :voucher_id
                )
                ORDER BY o.created_at DESC
            ");
            $stmt->execute(['voucher_id' => $voucherId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get voucher usage history error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get user's voucher history
     * Shows vouchers a user has used
     * 
     * @param int $userId User ID
     * @return array User's voucher usage
     */
    public function getUserVoucherHistory($userId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT DISTINCT
                    v.*,
                    o.order_number,
                    o.discount_amount,
                    o.created_at as used_at
                FROM vouchers v
                INNER JOIN orders o ON v.code = o.voucher_code
                WHERE o.user_id = :user_id
                ORDER BY o.created_at DESC
            ");
            $stmt->execute(['user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get user voucher history error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Check if voucher code already exists
     * 
     * @param string $code Voucher code
     * @param int|null $excludeId Exclude this ID (for update)
     * @return bool True if exists
     */
    public function codeExists($code, $excludeId = null) {
        try {
            $sql = "SELECT COUNT(*) as count FROM vouchers WHERE code = :code";
            if ($excludeId) {
                $sql .= " AND id != :exclude_id";
            }
            
            $stmt = $this->pdo->prepare($sql);
            $params = ['code' => strtoupper($code)];
            if ($excludeId) {
                $params['exclude_id'] = $excludeId;
            }
            
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Check voucher code error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get available vouchers for user at checkout
     * 
     * @param float $totalAmount Cart total amount
     * @return array Available vouchers
     */
    public function getAvailableForCheckout($totalAmount) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    *,
                    ROUND((discount_percent / 100) * :total_amount) as calculated_discount
                FROM vouchers 
                WHERE (expires_at IS NULL OR expires_at >= CURDATE())
                AND used_count < usage_limit
                AND (min_purchase IS NULL OR min_purchase <= :total_amount)
                ORDER BY discount_percent DESC
                LIMIT 5
            ");
            $stmt->execute(['total_amount' => $totalAmount]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get available vouchers error: " . $e->getMessage());
            return [];
        }
    }
}
