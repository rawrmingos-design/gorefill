<?php

class Address {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get all addresses for a user
     */
    public function getByUserId($userId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM addresses 
            WHERE user_id = :user_id 
            ORDER BY is_default DESC, created_at DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a single address by ID
     */
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM addresses WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get default address for a user
     */
    public function getDefaultByUserId($userId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM addresses 
            WHERE user_id = :user_id AND is_default = 1 
            LIMIT 1
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new address
     */
    public function create($userId, $data) {
        // If this is set as default, unset other defaults
        if (!empty($data['is_default'])) {
            $this->unsetDefaultForUser($userId);
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO addresses (user_id, label, place_name, street, city, postal_code, lat, lng, is_default) 
            VALUES (:user_id, :label, :place_name, :street, :city, :postal_code, :lat, :lng, :is_default)
        ");
        
        $stmt->execute([
            'user_id' => $userId,
            'label' => $data['label'] ?? null,
            'place_name' => $data['place_name'] ?? null,
            'street' => $data['street'] ?? null,
            'city' => $data['city'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'lat' => $data['lat'] ?? null,
            'lng' => $data['lng'] ?? null,
            'is_default' => $data['is_default'] ?? 0
        ]);

        return $this->pdo->lastInsertId();
    }

    /**
     * Update an existing address
     */
    public function update($id, $userId, $data) {
        // If this is set as default, unset other defaults
        if (!empty($data['is_default'])) {
            $this->unsetDefaultForUser($userId);
        }

        $stmt = $this->pdo->prepare("
            UPDATE addresses 
            SET label = :label, 
                place_name = :place_name, 
                street = :street, 
                city = :city, 
                postal_code = :postal_code, 
                lat = :lat, 
                lng = :lng, 
                is_default = :is_default
            WHERE id = :id AND user_id = :user_id
        ");
        
        return $stmt->execute([
            'id' => $id,
            'user_id' => $userId,
            'label' => $data['label'] ?? null,
            'place_name' => $data['place_name'] ?? null,
            'street' => $data['street'] ?? null,
            'city' => $data['city'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'lat' => $data['lat'] ?? null,
            'lng' => $data['lng'] ?? null,
            'is_default' => $data['is_default'] ?? 0
        ]);
    }

    /**
     * Delete an address
     */
    public function delete($id, $userId) {
        $stmt = $this->pdo->prepare("DELETE FROM addresses WHERE id = :id AND user_id = :user_id");
        return $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }

    /**
     * Set an address as default
     */
    public function setDefault($id, $userId) {
        // First unset all defaults for this user
        $this->unsetDefaultForUser($userId);

        // Then set this address as default
        $stmt = $this->pdo->prepare("
            UPDATE addresses 
            SET is_default = 1 
            WHERE id = :id AND user_id = :user_id
        ");
        return $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }

    /**
     * Unset default for all addresses of a user
     */
    private function unsetDefaultForUser($userId) {
        $stmt = $this->pdo->prepare("UPDATE addresses SET is_default = 0 WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
    }

    /**
     * Verify address belongs to user
     */
    public function belongsToUser($id, $userId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM addresses WHERE id = :id AND user_id = :user_id");
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        return $stmt->fetchColumn() > 0;
    }
}
