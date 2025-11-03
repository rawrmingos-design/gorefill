<?php

/**
 * Favorite Model
 * Handles user's favorite/wishlist products
 */
class Favorite
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Add product to user's favorites
     * 
     * @param int $userId User ID
     * @param int $productId Product ID
     * @return bool Success status
     */
    public function add($userId, $productId)
    {
        try {
            // Check if already exists
            if ($this->exists($userId, $productId)) {
                return true; // Already favorited
            }

            $stmt = $this->pdo->prepare("
                INSERT INTO favorites (user_id, product_id, created_at) 
                VALUES (:user_id, :product_id, NOW())
            ");

            return $stmt->execute([
                'user_id' => $userId,
                'product_id' => $productId
            ]);

        } catch (PDOException $e) {
            error_log("Add favorite error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove product from user's favorites
     * 
     * @param int $userId User ID
     * @param int $productId Product ID
     * @return bool Success status
     */
    public function remove($userId, $productId)
    {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM favorites 
                WHERE user_id = :user_id AND product_id = :product_id
            ");

            return $stmt->execute([
                'user_id' => $userId,
                'product_id' => $productId
            ]);

        } catch (PDOException $e) {
            error_log("Remove favorite error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all favorites for a user with product details
     * 
     * @param int $userId User ID
     * @return array List of favorite products
     */
    public function getByUserId($userId)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    f.id as favorite_id,
                    f.created_at as favorited_at,
                    p.*,
                    c.name as category_name,
                    c.slug as category_slug
                FROM favorites f
                INNER JOIN products p ON f.product_id = p.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE f.user_id = :user_id
                ORDER BY f.created_at DESC
            ");

            $stmt->execute(['user_id' => $userId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Get favorites error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Check if product is in user's favorites
     * 
     * @param int $userId User ID
     * @param int $productId Product ID
     * @return bool True if favorited
     */
    public function exists($userId, $productId)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count 
                FROM favorites 
                WHERE user_id = :user_id AND product_id = :product_id
            ");

            $stmt->execute([
                'user_id' => $userId,
                'product_id' => $productId
            ]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;

        } catch (PDOException $e) {
            error_log("Check favorite exists error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get favorite count for a user
     * 
     * @param int $userId User ID
     * @return int Count of favorites
     */
    public function getCount($userId)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count 
                FROM favorites 
                WHERE user_id = :user_id
            ");

            $stmt->execute(['user_id' => $userId]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $result['count'];

        } catch (PDOException $e) {
            error_log("Get favorite count error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get favorited product IDs for a user
     * Useful for checking multiple products at once
     * 
     * @param int $userId User ID
     * @return array Array of product IDs
     */
    public function getFavoritedProductIds($userId)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT product_id 
                FROM favorites 
                WHERE user_id = :user_id
            ");

            $stmt->execute(['user_id' => $userId]);

            $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
            return $results;

        } catch (PDOException $e) {
            error_log("Get favorited product IDs error: " . $e->getMessage());
            return [];
        }
    }
}
