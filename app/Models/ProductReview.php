<?php

/**
 * ProductReview Model
 * Handles product reviews and ratings
 */
class ProductReview
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Create new product review
     * 
     * @param int $productId Product ID
     * @param int $userId User ID
     * @param int $rating Rating (1-5)
     * @param string $review Review text
     * @return int|false Review ID on success, false on failure
     */
    public function create($productId, $userId, $rating, $comment)
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO product_reviews (product_id, user_id, rating, comment, created_at) 
                VALUES (:product_id, :user_id, :rating, :comment, NOW())
            ");

            $success = $stmt->execute([
                'product_id' => $productId,
                'user_id' => $userId,
                'rating' => $rating,
                'comment' => $comment
            ]);

            return $success ? $this->pdo->lastInsertId() : false;

        } catch (PDOException $e) {
            error_log("Create review error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get reviews for a product with pagination
     * 
     * @param int $productId Product ID
     * @param int $limit Number of reviews per page
     * @param int $offset Offset for pagination
     * @return array List of reviews with user details
     */
    public function getByProductId($productId, $limit = 10, $offset = 0)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    pr.*,
                    u.name as user_name,
                    u.email as user_email
                FROM product_reviews pr
                INNER JOIN users u ON pr.user_id = u.id
                WHERE pr.product_id = :product_id
                ORDER BY pr.created_at DESC
                LIMIT :limit OFFSET :offset
            ");

            $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Get reviews error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get average rating for a product
     * 
     * @param int $productId Product ID
     * @return array Contains average rating and review count
     */
    public function getAverageRating($productId)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    ROUND(AVG(rating), 1) as average_rating,
                    COUNT(*) as review_count
                FROM product_reviews
                WHERE product_id = :product_id
            ");

            $stmt->execute(['product_id' => $productId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                'average_rating' => $result['average_rating'] ? (float) $result['average_rating'] : 0,
                'review_count' => (int) $result['review_count']
            ];

        } catch (PDOException $e) {
            error_log("Get average rating error: " . $e->getMessage());
            return ['average_rating' => 0, 'review_count' => 0];
        }
    }

    /**
     * Check if user has already reviewed a product
     * 
     * @param int $productId Product ID
     * @param int $userId User ID
     * @return bool True if user has reviewed
     */
    public function hasUserReviewed($productId, $userId)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count 
                FROM product_reviews 
                WHERE product_id = :product_id AND user_id = :user_id
            ");

            $stmt->execute([
                'product_id' => $productId,
                'user_id' => $userId
            ]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;

        } catch (PDOException $e) {
            error_log("Check user reviewed error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if user can review a product (must have purchased it)
     * 
     * @param int $productId Product ID
     * @param int $userId User ID
     * @return bool True if user has purchased this product
     */
    public function canUserReview($productId, $userId)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(DISTINCT o.id) as count
                FROM orders o
                INNER JOIN order_items oi ON o.id = oi.order_id
                WHERE oi.product_id = :product_id 
                AND o.user_id = :user_id
                AND o.payment_status = 'paid'
            ");

            $stmt->execute([
                'product_id' => $productId,
                'user_id' => $userId
            ]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;

        } catch (PDOException $e) {
            error_log("Check can review error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get total review count for a product
     * 
     * @param int $productId Product ID
     * @return int Review count
     */
    public function getReviewCount($productId)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count 
                FROM product_reviews 
                WHERE product_id = :product_id
            ");

            $stmt->execute(['product_id' => $productId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int) $result['count'];

        } catch (PDOException $e) {
            error_log("Get review count error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get rating distribution for a product
     * 
     * @param int $productId Product ID
     * @return array Rating distribution [5=>count, 4=>count, etc]
     */
    public function getRatingDistribution($productId)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    rating,
                    COUNT(*) as count
                FROM product_reviews
                WHERE product_id = :product_id
                GROUP BY rating
                ORDER BY rating DESC
            ");

            $stmt->execute(['product_id' => $productId]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Initialize all ratings with 0
            $distribution = [
                5 => 0,
                4 => 0,
                3 => 0,
                2 => 0,
                1 => 0
            ];

            // Fill actual counts
            foreach ($results as $row) {
                $distribution[$row['rating']] = (int) $row['count'];
            }

            return $distribution;

        } catch (PDOException $e) {
            error_log("Get rating distribution error: " . $e->getMessage());
            return [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        }
    }

    /**
     * Check if user has purchased the product (for verified badge)
     * 
     * @param int $productId Product ID
     * @param int $userId User ID
     * @return bool True if verified purchase
     */
    public function isVerifiedPurchase($productId, $userId)
    {
        return $this->canUserReview($productId, $userId);
    }

    /**
     * Delete a review
     * 
     * @param int $reviewId Review ID
     * @param int $userId User ID (for authorization)
     * @return bool Success status
     */
    public function delete($reviewId, $userId)
    {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM product_reviews 
                WHERE id = :id AND user_id = :user_id
            ");

            return $stmt->execute([
                'id' => $reviewId,
                'user_id' => $userId
            ]);

        } catch (PDOException $e) {
            error_log("Delete review error: " . $e->getMessage());
            return false;
        }
    }
}
