<?php
/**
 * Admin Controller for GoRefill Application
 * 
 * Handles admin-only operations:
 * - Dashboard with statistics
 * - Product CRUD operations
 * - All methods protected with requireAuth('admin')
 */

require_once __DIR__ . '/../Models/Product.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Category.php';
require_once __DIR__ . '/BaseController.php';

class AdminController extends BaseController
{
    /**
     * Product model instance
     * @var Product
     */
    private $productModel;
    
    /**
     * User model instance
     * @var User
     */
    private $userModel;
    
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
        $this->userModel = new User($this->pdo);
        $this->categoryModel = new Category($this->pdo);
    }
    
    /**
     * Admin Dashboard - Statistics Overview
     */
    public function dashboard()
    {
        // Require admin role
        $this->requireAuth('admin');
        
        // Get statistics
        $stats = [
            'total_products' => $this->productModel->count(),
            'total_users' => $this->userModel->count(),
            'total_orders' => $this->getOrderCount(), // Will implement in Week 2
            'recent_products' => $this->productModel->getAll(null, 5, 0),
            'categories' => $this->productModel->getCategories()
        ];
        
        $this->render('admin/dashboard', [
            'title' => 'Admin Dashboard - GoRefill',
            'stats' => $stats
        ]);
    }
    
    /**
     * Product List with Search & Pagination
     */
    public function products()
    {
        $this->requireAuth('admin');

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $category = $_GET['category'] ?? null;
        $search = trim($_GET['search'] ?? '');
        $sort = $_GET['sort'] ?? 'created_at';
        $order = $_GET['order'] ?? 'desc';

        $allowedSortFields = ['id', 'name', 'price', 'stock', 'created_at'];
        if (!in_array($sort, $allowedSortFields)) {
            $sort = 'created_at';
        }

        $order = strtolower($order) === 'asc' ? 'asc' : 'desc';

        $query = "SELECT p.*, c.name AS category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE 1=1";

        $params = [];

        if ($category) {
            $query .= " AND p.category_id = ?";
            $params[] = $category;
        }

        if ($search !== '') {
            $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $query .= " ORDER BY p.$sort $order LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $countSql = "SELECT COUNT(*) FROM products WHERE 1=1";
        $countParams = [];

        if ($category) {
            $countSql .= " AND category_id = ?";
            $countParams[] = $category;
        }

        if ($search !== '') {
            $countSql .= " AND (name LIKE ? OR description LIKE ?)";
            $countParams[] = "%$search%";
            $countParams[] = "%$search%";
        }

        $countStmt = $this->pdo->prepare($countSql);
        $countStmt->execute($countParams);
        $totalProducts = (int) $countStmt->fetchColumn();

        $totalPages = ceil($totalProducts / $limit);

        if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
            $currentPage = $page;
            $totalPages = $totalPages;
            include __DIR__ . '/../Views/admin/products/_table.php';
            exit;
        }

        $this->render('admin/products/index', [
            'title' => 'Manage Products - Admin',
            'products' => $products,
            'categories' => $this->categoryModel->getAll(),
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
            'selectedCategory' => $category,
            'searchKeyword' => $search
        ]);
    }

    
    /**
     * Show Create Product Form
     */
    public function showCreateProduct()
    {
        // Require admin role
        $this->requireAuth('admin');
        
        $this->render('admin/products/create', [
            'title' => 'Add New Product - Admin',
            'categories' => $this->categoryModel->getAll()
        ]);
    }
    
    /**
     * Handle Create Product (POST)
     */
    public function createProduct()
    {
        // Require admin role
        $this->requireAuth('admin');
        
        // Only accept POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
        }
        
        // Validate input
        $errors = $this->validate($_POST, [
            'name' => 'required|min:3|max:200',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'category_id' => 'required|numeric'
        ]);
        
        if (!empty($errors)) {
            $this->json(['errors' => $errors], 400);
        }
        
        // Handle image upload or URL
        require_once __DIR__ . '/../Helpers/ImageHelper.php';
        $imageName = null;
        $imageType = $_POST['image_type'] ?? 'file';
        
        if ($imageType === 'url') {
            // Use Unsplash URL
            $imageUrl = trim($_POST['image_url'] ?? '');
            
            if (!empty($imageUrl)) {
                // Validate URL
                $validation = ImageHelper::validateImage($imageUrl, 'url');
                
                if (!$validation['valid']) {
                    $this->json(['error' => $validation['message']], 400);
                }
                
                // Store URL directly
                $imageName = $imageUrl;
            }
        } elseif ($imageType === 'file') {
            // Handle file upload
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../uploads/products/';
                $imageName = $this->uploadFile($_FILES['image_file'], $uploadDir, ['image/jpeg', 'image/png', 'image/webp']);
                
                if (!$imageName) {
                    $this->json(['error' => 'Image upload failed. Please use JPG, PNG, or WebP format.'], 400);
                }
            }
        }
        
        // Create product
        $productData = [
            'name' => trim($_POST['name']),
            'description' => trim($_POST['description'] ?? ''),
            'price' => (float) $_POST['price'],
            'stock' => (int) $_POST['stock'],
            'category_id' => (int) $_POST['category_id'],
            'image' => $imageName
        ];
        
        $productId = $this->productModel->create($productData);
        
        if (!$productId) {
            // Delete uploaded image if product creation failed
            if ($imageName) {
                unlink($uploadDir . $imageName);
            }
            $this->json(['error' => 'Failed to create product'], 500);
        }
        
        $this->flash('success', 'Product created successfully!');
        $this->json([
            'success' => true,
            'message' => 'Product created successfully',
            'product_id' => $productId,
            'redirect' => 'index.php?route=admin.products'
        ], 201);
    }
    
    /**
     * Show Edit Product Form
     */
    public function showEditProduct()
    {
        // Require admin role
        $this->requireAuth('admin');
        
        $productId = $_GET['id'] ?? null;
        
        if (!$productId) {
            $this->redirect('admin.products');
        }
        
        $product = $this->productModel->getById($productId);
        
        if (!$product) {
            $this->flash('error', 'Product not found');
            $this->redirect('admin.products');
        }
        
        $this->render('admin/products/edit', [
            'title' => 'Edit Product - Admin',
            'product' => $product,
            'categories' => $this->categoryModel->getAll()
        ]);
    }
    
    /**
     * Handle Edit Product (POST)
     */
    public function editProduct()
    {
        // Require admin role
        $this->requireAuth('admin');
        
        // Only accept POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
        }
        
        $productId = $_POST['id'] ?? null;
        
        if (!$productId) {
            $this->json(['error' => 'Product ID required'], 400);
        }
        
        // Check if product exists
        $existingProduct = $this->productModel->getById($productId);
        if (!$existingProduct) {
            $this->json(['error' => 'Product not found'], 404);
        }
        
        // Validate input
        $errors = $this->validate($_POST, [
            'name' => 'required|min:3|max:200',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'category_id' => 'required|numeric'
        ]);
        
        if (!empty($errors)) {
            $this->json(['errors' => $errors], 400);
        }
        
        // Prepare update data
        $updateData = [
            'name' => trim($_POST['name']),
            'description' => trim($_POST['description'] ?? ''),
            'price' => (float) $_POST['price'],
            'stock' => (int) $_POST['stock'],
            'category_id' => (int) $_POST['category_id']
        ];
        
        // Handle image update
        require_once __DIR__ . '/../Helpers/ImageHelper.php';
        $imageType = $_POST['image_type'] ?? 'keep';
        
        if ($imageType === 'url') {
            // Use Unsplash URL
            $imageUrl = trim($_POST['image_url'] ?? '');
            
            if (!empty($imageUrl)) {
                // Validate URL
                $validation = ImageHelper::validateImage($imageUrl, 'url');
                
                if (!$validation['valid']) {
                    $this->json(['error' => $validation['message']], 400);
                }
                
                // Delete old image if it was a local file
                if ($existingProduct['image'] && !ImageHelper::isExternalUrl($existingProduct['image'])) {
                    $uploadDir = __DIR__ . '/../../uploads/products/';
                    $oldImage = $uploadDir . $existingProduct['image'];
                    if (file_exists($oldImage)) {
                        unlink($oldImage);
                    }
                }
                
                // Store URL
                $updateData['image'] = $imageUrl;
            }
        } elseif ($imageType === 'file') {
            // Handle file upload
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../uploads/products/';
                $imageName = $this->uploadFile($_FILES['image_file'], $uploadDir, ['image/jpeg', 'image/png', 'image/webp']);
                
                if (!$imageName) {
                    $this->json(['error' => 'Image upload failed. Please use JPG, PNG, or WebP format.'], 400);
                }
                
                // Delete old image if it was a local file
                if ($existingProduct['image'] && !ImageHelper::isExternalUrl($existingProduct['image'])) {
                    $oldImage = $uploadDir . $existingProduct['image'];
                    if (file_exists($oldImage)) {
                        unlink($oldImage);
                    }
                }
                
                $updateData['image'] = $imageName;
            }
        }
        // If 'keep', don't update image field
        
        // Update product
        $success = $this->productModel->update($productId, $updateData);
        
        if (!$success) {
            $this->json(['error' => 'Failed to update product'], 500);
        }
        
        $this->flash('success', 'Product updated successfully!');
        $this->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'redirect' => 'index.php?route=admin.products'
        ], 200);
    }
    
    /**
     * Handle Delete Product
     */
    public function deleteProduct()
    {
        // Require admin role
        $this->requireAuth('admin');
        
        $productId = $_POST['id'] ?? $_GET['id'] ?? null;
        
        if (!$productId) {
            $this->json(['error' => 'Product ID required'], 400);
        }
        
        $success = $this->productModel->delete($productId);
        
        if (!$success) {
            $this->json(['error' => 'Failed to delete product'], 500);
        }
        
        $this->flash('success', 'Product deleted successfully!');
        $this->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ], 200);
    }
    
    /**
     * Get order count (placeholder for Week 2)
     * 
     * @return int Order count
     */
    private function getOrderCount()
    {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM orders");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $result['total'];
        } catch (PDOException $e) {
            return 0;
        }
    }
}
