<?php

/**
 * CourierLocation Model
 * Week 3 Day 12: Courier Tracking Backend
 * 
 * Manages courier GPS location data for real-time tracking
 */
class CourierLocation {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Update courier location (upsert pattern)
     * If courier exists, update location & timestamp
     * If not, insert new record
     * 
     * @param int $courierId - User ID with courier role
     * @param float $lat - Latitude
     * @param float $lng - Longitude
     * @return bool Success status
     */
    public function updateLocation($courierId, $lat, $lng) {
        try {
            // Use INSERT ... ON DUPLICATE KEY UPDATE for upsert
            $stmt = $this->pdo->prepare("
                INSERT INTO courier_locations (courier_id, lat, lng, updated_at)
                VALUES (:courier_id, :lat, :lng, NOW())
                ON DUPLICATE KEY UPDATE 
                    lat = :lat_update,
                    lng = :lng_update,
                    updated_at = NOW()
            ");
            
            return $stmt->execute([
                'courier_id' => $courierId,
                'lat' => $lat,
                'lng' => $lng,
                'lat_update' => $lat,
                'lng_update' => $lng
            ]);
        } catch (\PDOException $e) {
            error_log("CourierLocation updateLocation error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get latest location for a courier
     * 
     * @param int $courierId - Courier user ID
     * @return array|false Location data with timestamp
     */
    public function getLocation($courierId) {
        $stmt = $this->pdo->prepare("
            SELECT id, courier_id, lat, lng, updated_at
            FROM courier_locations
            WHERE courier_id = :courier_id
            LIMIT 1
        ");
        
        $stmt->execute(['courier_id' => $courierId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get location history for a courier (breadcrumb trail)
     * Note: Current schema only stores latest location per courier
     * This method returns single record, but structure supports future expansion
     * 
     * @param int $courierId - Courier user ID
     * @param int $limit - Max records to retrieve (default 50)
     * @return array Location history
     */
    public function getLocationHistory($courierId, $limit = 50) {
        $stmt = $this->pdo->prepare("
            SELECT id, courier_id, lat, lng, updated_at
            FROM courier_locations
            WHERE courier_id = :courier_id
            ORDER BY updated_at DESC
            LIMIT :limit
        ");
        
        $stmt->bindValue(':courier_id', $courierId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all active couriers with their latest locations
     * Useful for admin/dispatcher to see all courier positions
     * 
     * @return array All courier locations with user info
     */
    public function getAllActiveCouriers() {
        $stmt = $this->pdo->prepare("
            SELECT 
                cl.id,
                cl.courier_id,
                cl.lat,
                cl.lng,
                cl.updated_at,
                u.name as courier_name,
                u.email as courier_email,
                u.phone as courier_phone,
                TIMESTAMPDIFF(MINUTE, cl.updated_at, NOW()) as minutes_ago
            FROM courier_locations cl
            JOIN users u ON cl.courier_id = u.id
            WHERE u.role = 'courier'
            ORDER BY cl.updated_at DESC
        ");
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Check if location is recent (updated within last N minutes)
     * Helps determine if courier is actively tracking
     * 
     * @param int $courierId - Courier user ID
     * @param int $minutes - Time threshold (default 10 minutes)
     * @return bool True if location is recent
     */
    public function isLocationRecent($courierId, $minutes = 10) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM courier_locations
            WHERE courier_id = :courier_id
            AND updated_at >= DATE_SUB(NOW(), INTERVAL :minutes MINUTE)
        ");
        
        $stmt->execute([
            'courier_id' => $courierId,
            'minutes' => $minutes
        ]);
        
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Delete courier location (for privacy/cleanup)
     * 
     * @param int $courierId - Courier user ID
     * @return bool Success status
     */
    public function deleteLocation($courierId) {
        $stmt = $this->pdo->prepare("
            DELETE FROM courier_locations 
            WHERE courier_id = :courier_id
        ");
        
        return $stmt->execute(['courier_id' => $courierId]);
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     * Helper method for proximity calculations
     * 
     * @param float $lat1 - Latitude point 1
     * @param float $lng1 - Longitude point 1
     * @param float $lat2 - Latitude point 2
     * @param float $lng2 - Longitude point 2
     * @return float Distance in kilometers
     */
    public static function calculateDistance($lat1, $lng1, $lat2, $lng2) {
        $earthRadius = 6371; // km

        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lngDelta / 2) * sin($lngDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
