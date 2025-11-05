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
require_once __DIR__ . '/../Models/Favorite.php';
require_once __DIR__ . '/../Models/ProductReview.php';
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
     * Favorite model instance
     * @var Favorite
     */
    private $favoriteModel;
    
    /**
     * ProductReview model instance
     * @var ProductReview
     */
    private $reviewModel;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product($this->pdo);
        $this->categoryModel = new Category($this->pdo);
        $this->favoriteModel = new Favorite($this->pdo);
        $this->reviewModel = new ProductReview($this->pdo);
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
        
        // ✅ FIX: Sanitize filter parameters - convert empty strings to null
        // This prevents empty form submissions from being treated as filters
        $category = isset($_GET['category']) && trim($_GET['category']) !== '' ? (int)$_GET['category'] : null;
        
        // ✅ FIX: Only set price filters if value is not empty AND greater than 0
        $minPrice = null;
        if (isset($_GET['min']) && trim($_GET['min']) !== '') {
            $minPrice = (float)$_GET['min'];
            // If converted to 0 or negative, treat as null (invalid)
            if ($minPrice <= 0) {
                $minPrice = null;
            }
        }
        
        $maxPrice = null;
        if (isset($_GET['max']) && trim($_GET['max']) !== '') {
            $maxPrice = (float)$_GET['max'];
            // If converted to 0 or negative, treat as null (invalid)
            if ($maxPrice <= 0) {
                $maxPrice = null;
            }
        }
        
        // ✅ FIX: Only set search if value is not empty after trimming
        $search = isset($_GET['search']) && trim($_GET['search']) !== '' ? trim($_GET['search']) : null;
        
        // Get sorting parameters
        $sort = $_GET['sort'] ?? 'created_at';
        $order = $_GET['order'] ?? 'desc';
        
        // Build WHERE conditions
        $products = [];
        $totalProducts = 0;
        
        // ✅ FIX: Only enter search mode if search term is valid (not null/empty)
        if ($search !== null) {
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
        
        // Get favorited product IDs for current user
        $favoritedIds = [];
        if (isset($_SESSION['user_id'])) {
            $favoritedIds = $this->favoriteModel->getFavoritedProductIds($_SESSION['user_id']);
        }
        
        // Get average ratings for all products
        // Using pre-calculated rating from products table for better performance
        $productRatings = [];
        foreach ($products as $product) {
            // Get review count from product_reviews table
            $reviewCount = $this->reviewModel->getReviewCount($product['id']);
            
            $productRatings[$product['id']] = [
                'average_rating' => (float) $product['rating'], // From products.rating column
                'review_count' => $reviewCount
            ];
        }
        
        $this->render('products/index', [
            'title' => 'Products - GoRefill',
            'products' => $products,
            'categories' => $categories,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
            'favoritedIds' => $favoritedIds,
            'productRatings' => $productRatings,
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
     * Product detail page
     * ✅ NEW: Now supports SLUG (SEO-friendly) or ID (backward compatible)
     */
    public function detail()
    {
        $slug = $_GET['slug'] ?? null;
        $productId = $_GET['id'] ?? null;
        
        if (!$slug && !$productId) {
            $this->flash('error', 'Product not found');
            $this->redirect('products');
        }
        
        // ✅ Try to get by slug first (SEO-friendly), fallback to ID
        if ($slug) {
            $product = $this->productModel->getBySlug($slug);
        } else {
            $product = $this->productModel->getById($productId);
        }
        
        if (!$product) {
            $this->flash('error', 'Product not found');
            $this->redirect('products');
        }
        
        // Get related products (same category, limit 4)
        $relatedProducts = $this->productModel->getByCategory($product['category_id'], 4, 0);
        
        // Remove current product from related
        $relatedProducts = array_filter($relatedProducts, function($p) use ($product) {
            return $p['id'] != $product['id'];
        });
        
        // Check if product is favorited by current user
        $isFavorite = false;
        if (isset($_SESSION['user_id'])) {
            $isFavorite = $this->favoriteModel->exists($_SESSION['user_id'], $product['id']);
        }
        
        // Get review data
        $reviewData = $this->reviewModel->getAverageRating($product['id']);
        $reviews = $this->reviewModel->getByProductId($product['id'], 10, 0);
        $ratingDistribution = $this->reviewModel->getRatingDistribution($product['id']);
        
        // Check if user can review
        $canReview = false;
        $hasReviewed = false;
        if (isset($_SESSION['user_id'])) {
            $canReview = $this->reviewModel->canUserReview($product['id'], $_SESSION['user_id']);
            $hasReviewed = $this->reviewModel->hasUserReviewed($product['id'], $_SESSION['user_id']);
        }
        
        $this->render('products/detail', [
            'title' => $product['name'] . ' - GoRefill',
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'isFavorite' => $isFavorite,
            'averageRating' => $reviewData['average_rating'],
            'reviewCount' => $reviewData['review_count'],
            'reviews' => $reviews,
            'ratingDistribution' => $ratingDistribution,
            'canReview' => $canReview,
            'hasReviewed' => $hasReviewed
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
    
    /**
     * Add product review (AJAX)
     * POST /index.php?route=product.addReview
     */
    public function addReview()
    {
        header('Content-Type: application/json');
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu'
            ]);
            exit;
        }
        
        // Get POST data
        $input = json_decode(file_get_contents('php://input'), true);
        $productId = $input['product_id'] ?? null;
        $rating = $input['rating'] ?? null;
        $review = $input['review'] ?? '';
        
        // Validation
        if (!$productId) {
            echo json_encode([
                'success' => false,
                'message' => 'Product ID required'
            ]);
            exit;
        }
        
        if (!$rating || $rating < 1 || $rating > 5) {
            echo json_encode([
                'success' => false,
                'message' => 'Rating harus antara 1-5 bintang'
            ]);
            exit;
        }
        
        if (empty(trim($review))) {
            echo json_encode([
                'success' => false,
                'message' => 'Review tidak boleh kosong'
            ]);
            exit;
        }
        
        // Check if user has purchased the product
        if (!$this->reviewModel->canUserReview($productId, $_SESSION['user_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Anda hanya bisa mereview produk yang sudah Anda beli'
            ]);
            exit;
        }
        
        // Check if user has already reviewed
        if ($this->reviewModel->hasUserReviewed($productId, $_SESSION['user_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Anda sudah mereview produk ini'
            ]);
            exit;
        }
        
        // Create review
        $reviewId = $this->reviewModel->create($productId, $_SESSION['user_id'], $rating, trim($review));
        
        if ($reviewId) {
            // Update product rating in products table
            $this->productModel->updateRating($productId);
            
            // Get updated average rating
            $reviewData = $this->reviewModel->getAverageRating($productId);
            
            echo json_encode([
                'success' => true,
                'message' => 'Review berhasil ditambahkan! Terima kasih atas feedback Anda.',
                'average_rating' => $reviewData['average_rating'],
                'review_count' => $reviewData['review_count']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal menambahkan review. Silakan coba lagi.'
            ]);
        }
    }
}
