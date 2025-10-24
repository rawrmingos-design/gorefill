<?php
/**
 * Product Model for GoRefill Application
 * 
 * Handles all product-related database operations including:
 * - Product CRUD operations
 * - Product search and filtering
 * - Stock management
 * - Category filtering
 * 
 * Security:
 * - All queries use PDO prepared statements
 * - Input sanitization handled by controller
 */

class Product
{
    /**
     * PDO database connection
     * @var PDO
     */
    private $pdo;
    
    /**
     * Constructor
     * @param PDO $pdo Database connection
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    
    /**
     * Get all products with optional filtering and sorting
     * 
     * @param string|null $category Filter by category
     * @param int $limit Number of products per page
     * @param int $offset Offset for pagination
     * @param string $sortBy Sort field (id, name, created_at, etc.)
     * @param string $order Sort order (asc or desc)
     * @return array List of products
     */
    public function getAll($category = null, $limit = 20, $offset = 0, $sortBy = 'created_at', $order = 'desc')
    {
        try {
            $sql = "SELECT * FROM products WHERE 1=1";
            $params = [];
            
            // Filter by category if provided
            if ($category) {
                $sql .= " AND category = ?";
                $params[] = $category;
            }
            
            // Validate sort field
            $allowedFields = ['id', 'name', 'price', 'stock', 'category', 'created_at'];
            if (!in_array($sortBy, $allowedFields)) {
                $sortBy = 'created_at';
            }
            
            // Validate order
            $order = strtoupper($order);
            if (!in_array($order, ['ASC', 'DESC'])) {
                $order = 'DESC';
            }
            
            $sql .= " ORDER BY $sortBy $order LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Get all products error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get product by ID
     * 
     * @param int $id Product ID
     * @return array|false Product data or false if not found
     */
    public function getById($id)
    {
        try {
            $sql = "SELECT * FROM products WHERE id = ? LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            return $product ?: false;
            
        } catch (PDOException $e) {
            error_log("Get product by ID error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create new product
     * 
     * @param array $data Product data
     * @return int|false Product ID on success, false on failure
     */
    public function create($data)
    {
        try {
            $sql = "INSERT INTO products (name, description, price, stock, category, image, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $data['name'],
                $data['description'] ?? null,
                $data['price'],
                $data['stock'] ?? 0,
                $data['category'],
                $data['image'] ?? null
            ]);
            
            return $this->pdo->lastInsertId();
            
        } catch (PDOException $e) {
            error_log("Create product error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update existing product
     * 
     * @param int $id Product ID
     * @param array $data Updated data
     * @return bool Success status
     */
    public function update($id, $data)
    {
        try {
            // Build UPDATE query dynamically
            $fields = [];
            $values = [];
            
            if (isset($data['name'])) {
                $fields[] = "name = ?";
                $values[] = $data['name'];
            }
            
            if (isset($data['description'])) {
                $fields[] = "description = ?";
                $values[] = $data['description'];
            }
            
            if (isset($data['price'])) {
                $fields[] = "price = ?";
                $values[] = $data['price'];
            }
            
            if (isset($data['stock'])) {
                $fields[] = "stock = ?";
                $values[] = $data['stock'];
            }
            
            if (isset($data['category'])) {
                $fields[] = "category = ?";
                $values[] = $data['category'];
            }
            
            if (isset($data['image'])) {
                $fields[] = "image = ?";
                $values[] = $data['image'];
            }
            
            // Nothing to update
            if (empty($fields)) {
                return false;
            }
            
            // Add product ID
            $values[] = $id;
            
            $sql = "UPDATE products SET " . implode(", ", $fields) . " WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            
            return $stmt->execute($values);
            
        } catch (PDOException $e) {
            error_log("Update product error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete product
     * 
     * @param int $id Product ID
     * @return bool Success status
     */
    public function delete($id)
    {
        try {
            // Get product to delete image file
            $product = $this->getById($id);
            
            if ($product && $product['image']) {
                $imagePath = __DIR__ . '/../../uploads/products/' . $product['image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            $sql = "DELETE FROM products WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            
            return $stmt->execute([$id]);
            
        } catch (PDOException $e) {
            error_log("Delete product error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Search products by keyword
     * 
     * @param string $keyword Search keyword
     * @param int $limit Number of results
     * @return array List of matching products
     */
    public function search($keyword, $limit = 20)
    {
        try {
            $sql = "SELECT * FROM products 
                    WHERE name LIKE ? OR description LIKE ? 
                    ORDER BY created_at DESC 
                    LIMIT ?";
            
            $searchTerm = "%$keyword%";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$searchTerm, $searchTerm, $limit]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Search products error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get products by category
     * 
     * @param string $category Category name
     * @param int $limit Number of products
     * @param int $offset Offset for pagination
     * @return array List of products in category
     */
    public function getByCategory($category, $limit = 20, $offset = 0)
    {
        return $this->getAll($category, $limit, $offset);
    }
    
    /**
     * Update product stock
     * 
     * @param int $id Product ID
     * @param int $qty Quantity to add/subtract (positive or negative)
     * @return bool Success status
     */
    public function updateStock($id, $qty)
    {
        try {
            $sql = "UPDATE products SET stock = stock + ? WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            
            return $stmt->execute([$qty, $id]);
            
        } catch (PDOException $e) {
            error_log("Update stock error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get product count (total or by category)
     * 
     * @param string|null $category Optional category filter
     * @return int Product count
     */
    public function count($category = null)
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM products WHERE 1=1";
            $params = [];
            
            if ($category) {
                $sql .= " AND category = ?";
                $params[] = $category;
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return (int) $result['total'];
            
        } catch (PDOException $e) {
            error_log("Count products error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get all categories
     * 
     * @return array List of unique categories
     */
    public function getCategories()
    {
        try {
            $sql = "SELECT DISTINCT category FROM products WHERE category IS NOT NULL ORDER BY category";
            $stmt = $this->pdo->query($sql);
            
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
            
        } catch (PDOException $e) {
            error_log("Get categories error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Check if product has sufficient stock
     * 
     * @param int $id Product ID
     * @param int $qty Required quantity
     * @return bool True if sufficient stock available
     */
    public function hasStock($id, $qty)
    {
        $product = $this->getById($id);
        
        if (!$product) {
            return false;
        }
        
        return $product['stock'] >= $qty;
    }
}
