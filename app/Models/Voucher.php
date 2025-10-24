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
}
