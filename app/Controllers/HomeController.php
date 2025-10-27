<?php
/**
 * Home Controller for GoRefill Application
 */

require_once __DIR__ . '/../Models/Product.php';
require_once __DIR__ . '/../Models/Category.php';
require_once __DIR__ . '/BaseController.php';

class HomeController extends BaseController
{
    private $productModel;
    private $categoryModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product($this->pdo);
        $this->categoryModel = new Category($this->pdo);
    }
    
    /**
     * Homepage with featured products
     */
    public function index()
    {
        // Get featured products (latest 8 products)
        $featuredProducts = $this->getFeaturedProducts();
        
        // Get all categories
        $categories = $this->categoryModel->getAll();
        
        $this->render('home', [
            'featuredProducts' => $featuredProducts,
            'categories' => $categories
        ]);
    }
    
    /**
     * Get featured products with category names
     */
    private function getFeaturedProducts()
    {
        try {
            $sql = "SELECT p.*, c.name AS category_name 
                    FROM products p
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE p.stock > 0
                    ORDER BY p.created_at DESC
                    LIMIT 8";
            
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Get featured products error: " . $e->getMessage());
            return [];
        }
    }
}
