<?php
/**
 * Product Controller for GoRefill Application
 * 
 * Handles product display for users:
 * - Product listing with filters
 * - Product detail page
 * - Search functionality
 * - Category filtering
 */

require_once __DIR__ . '/../Models/Product.php';
require_once __DIR__ . '/../Models/Category.php';
require_once __DIR__ . '/BaseController.php';

class ProductController extends BaseController
{
    /**
     * Product model instance
     * @var Product
     */
    private $productModel;
    
    /**
     * Category model instance
     * @var Category
     */
    private $categoryModel;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product($this->pdo);
        $this->categoryModel = new Category($this->pdo);
    }
    
    /**
     * Product Listing Page with Filters
     */
    public function index()
    {
        // Get pagination parameters
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 12; // 12 products per page
        $offset = ($page - 1) * $limit;
        
        // Get filter parameters
        $category = isset($_GET['category']) && $_GET['category'] !== '' ? (int)$_GET['category'] : null;
        $minPrice = isset($_GET['min']) ? (float)$_GET['min'] : null;
        $maxPrice = isset($_GET['max']) ? (float)$_GET['max'] : null;
        $search = $_GET['search'] ?? null;
        
        // Get sorting parameters
        $sort = $_GET['sort'] ?? 'created_at';
        $order = $_GET['order'] ?? 'desc';
        
        // Build WHERE conditions
        $products = [];
        $totalProducts = 0;
        
        if ($search) {
            // Search mode
            $products = $this->productModel->search($search, 100); // Get all matching
            
            // Apply filters on search results
            $products = $this->applyFilters($products, $category, $minPrice, $maxPrice);
            $totalProducts = count($products);
            
            // Pagination on filtered results
            $products = array_slice($products, $offset, $limit);
            
        } else {
            // Normal mode with filters
            $products = $this->getFilteredProducts($category, $minPrice, $maxPrice, $limit, $offset, $sort, $order);
            $totalProducts = $this->countFilteredProducts($category, $minPrice, $maxPrice);
        }
        
        $totalPages = ceil($totalProducts / $limit);
        
        // Get all categories for filter
        $categories = $this->categoryModel->getAll();
        
        $this->render('products/index', [
            'title' => 'Products - GoRefill',
            'products' => $products,
            'categories' => $categories,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
            'filters' => [
                'category' => $category,
                'minPrice' => $minPrice,
                'maxPrice' => $maxPrice,
                'search' => $search,
                'sort' => $sort,
                'order' => $order
            ]
        ]);
    }
    
    /**
     * Product Detail Page
     */
    public function detail()
    {
        $productId = $_GET['id'] ?? null;
        
        if (!$productId) {
            $this->flash('error', 'Product not found');
            $this->redirect('products');
        }
        
        $product = $this->productModel->getById($productId);
        
        if (!$product) {
            $this->flash('error', 'Product not found');
            $this->redirect('products');
        }
        
        // Get related products (same category, limit 4)
        $relatedProducts = $this->productModel->getByCategory($product['category_id'], 4, 0);
        
        // Remove current product from related
        $relatedProducts = array_filter($relatedProducts, function($p) use ($productId) {
            return $p['id'] != $productId;
        });
        
        $this->render('products/detail', [
            'title' => $product['name'] . ' - GoRefill',
            'product' => $product,
            'relatedProducts' => $relatedProducts
        ]);
    }
    
    /**
     * Search Products (AJAX)
     */
    public function search()
    {
        $keyword = $_GET['q'] ?? '';
        
        if (empty($keyword)) {
            $this->json(['products' => []]);
        }
        
        $products = $this->productModel->search($keyword, 10);
        
        $this->json([
            'success' => true,
            'products' => $products
        ]);
    }
    
    /**
     * Get filtered products from database
     * 
     * @param string|null $category
     * @param float|null $minPrice
     * @param float|null $maxPrice
     * @param int $limit
     * @param int $offset
     * @param string $sort
     * @param string $order
     * @return array
     */
    private function getFilteredProducts($category, $minPrice, $maxPrice, $limit, $offset, $sort = 'created_at', $order = 'desc')
    {
        try {
            $sql = "SELECT p.*, c.name AS category_name, c.slug AS category_slug
                    FROM products p
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE 1=1";
            $params = [];
            
            // Category filter
            if ($category) {
                $sql .= " AND p.category_id = ?";
                $params[] = $category;
            }
            
            // Price range filter
            if ($minPrice !== null) {
                $sql .= " AND p.price >= ?";
                $params[] = $minPrice;
            }
            
            if ($maxPrice !== null) {
                $sql .= " AND p.price <= ?";
                $params[] = $maxPrice;
            }
            
            // Validate sort field
            $allowedFields = ['id', 'name', 'price', 'stock', 'created_at'];
            if (!in_array($sort, $allowedFields)) {
                $sort = 'created_at';
            }
            
            // Validate order
            $order = strtoupper($order);
            if (!in_array($order, ['ASC', 'DESC'])) {
                $order = 'DESC';
            }
            
            // Namespace sort fields with alias "p"
            $sortColumn = 'p.' . $sort;
            $sql .= " ORDER BY $sortColumn $order LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Get filtered products error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Count filtered products
     * 
     * @param string|null $category
     * @param float|null $minPrice
     * @param float|null $maxPrice
     * @return int
     */
    private function countFilteredProducts($category, $minPrice, $maxPrice)
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM products p WHERE 1=1";
            $params = [];
            
            if ($category) {
                $sql .= " AND p.category_id = ?";
                $params[] = $category;
            }
            
            if ($minPrice !== null) {
                $sql .= " AND p.price >= ?";
                $params[] = $minPrice;
            }
            
            if ($maxPrice !== null) {
                $sql .= " AND p.price <= ?";
                $params[] = $maxPrice;
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return (int) $result['total'];
            
        } catch (PDOException $e) {
            error_log("Count filtered products error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Apply filters to array of products
     * 
     * @param array $products
     * @param string|null $category
     * @param float|null $minPrice
     * @param float|null $maxPrice
     * @return array
     */
    private function applyFilters($products, $category, $minPrice, $maxPrice)
    {
        return array_filter($products, function($product) use ($category, $minPrice, $maxPrice) {
            // Category filter
            if ($category && $product['category_id'] !== $category) {
                return false;
            }
            
            // Min price filter
            if ($minPrice !== null && $product['price'] < $minPrice) {
                return false;
            }
            
            // Max price filter
            if ($maxPrice !== null && $product['price'] > $maxPrice) {
                return false;
            }
            
            return true;
        });
    }
}
