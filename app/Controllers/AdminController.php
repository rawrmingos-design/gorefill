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
require_once __DIR__ . '/../Models/Order.php';
require_once __DIR__ . '/../Models/Voucher.php';
require_once __DIR__ . '/../Models/Analytics.php';
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
     * Order model instance
     * @var Order
     */
    private $orderModel;
    
    /**
     * Voucher model instance
     * @var Voucher
     */
    private $voucherModel;
    
    /**
     * Analytics model instance
     * @var Analytics
     */
    private $analyticsModel;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product($this->pdo);
        $this->userModel = new User($this->pdo);
        $this->categoryModel = new Category($this->pdo);
        $this->orderModel = new Order($this->pdo);
        $this->voucherModel = new Voucher($this->pdo);
        $this->analyticsModel = new Analytics($this->pdo);
    }
    
    /**
     * Admin Dashboard - Statistics Overview (Week 4 Day 18)
     */
    public function dashboard()
    {
        $this->requireAuth('admin');
        
        // Get comprehensive analytics data
        $revenueStats = $this->analyticsModel->getRevenueStats();
        $orderStats = $this->analyticsModel->getOrderStats();
        $categoryStats = $this->analyticsModel->getCategoryStats();
        $topProducts = $this->analyticsModel->getTopProducts(5);
        $recentOrders = $this->analyticsModel->getRecentOrders(10);
        $dashboardCounts = $this->analyticsModel->getDashboardCounts();
        
        // Get sales data for last 7 days (for chart)
        $endDate = date('Y-m-d');
        $startDate = date('Y-m-d', strtotime('-6 days'));
        $dailySales = $this->analyticsModel->getDailySales($startDate, $endDate);
        
        $this->render('admin/dashboard', [
            'title' => 'Admin Dashboard - GoRefill',
            'revenueStats' => $revenueStats,
            'orderStats' => $orderStats,
            'categoryStats' => $categoryStats,
            'topProducts' => $topProducts,
            'recentOrders' => $recentOrders,
            'dashboardCounts' => $dashboardCounts,
            'dailySales' => $dailySales
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
            'name' => 'required|min:3|max:200|unique:products,name',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'category_id' => 'required|numeric'
        ]);
        
        if (!empty($errors)) {
            $this->json(['errors' => $errors], 400);
        }

        // Generate slug from product name
        $slug = strtolower(trim($_POST['name']));
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug); // Remove special chars
        $slug = preg_replace('/[\s-]+/', '-', $slug); // Replace spaces with single hyphen
        $slug = trim($slug, '-'); // Remove leading/trailing hyphens
        
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
            'slug' => $slug,
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

        // Generate slug from product name
        $slug = strtolower(trim($_POST['name']));
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug); // Remove special chars
        $slug = preg_replace('/[\s-]+/', '-', $slug); // Replace spaces with single hyphen
        $slug = trim($slug, '-'); // Remove leading/trailing hyphens
        
        // Prepare update data
        $updateData = [
            'name' => trim($_POST['name']),
            'slug' => $slug,
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
     * Week 3 Day 14: Order Management - List all orders with filters
     * GET: ?route=admin.orders
     */
    public function orders()
    {
        $this->requireAuth('admin');
        
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        // Filters
        $status = $_GET['status'] ?? null;
        $paymentStatus = $_GET['payment_status'] ?? null;
        $search = trim($_GET['search'] ?? '');
        
        // Build query
        $query = "SELECT o.*, u.name as customer_name, u.email as customer_email
                  FROM orders o
                  JOIN users u ON o.user_id = u.id
                  WHERE 1=1";
        
        $params = [];
        
        if ($status) {
            $query .= " AND o.status = :status";
            $params['status'] = $status;
        }
        
        if ($paymentStatus) {
            $query .= " AND o.payment_status = :payment_status";
            $params['payment_status'] = $paymentStatus;
        }
        
        if ($search) {
            $query .= " AND (o.order_number LIKE :search OR u.name LIKE :search OR u.email LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }
        
        $query .= " ORDER BY o.created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->pdo->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get total count for pagination
        $countQuery = "SELECT COUNT(*) as total FROM orders o JOIN users u ON o.user_id = u.id WHERE 1=1";
        if ($status) $countQuery .= " AND o.status = :status";
        if ($paymentStatus) $countQuery .= " AND o.payment_status = :payment_status";
        if ($search) $countQuery .= " AND (o.order_number LIKE :search OR u.name LIKE :search OR u.email LIKE :search)";
        
        $stmtCount = $this->pdo->prepare($countQuery);
        foreach ($params as $key => $value) {
            $stmtCount->bindValue(':' . $key, $value);
        }
        $stmtCount->execute();
        $totalOrders = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
        $totalPages = ceil($totalOrders / $limit);
        
        $this->render('admin/orders/index', [
            'title' => 'Order Management - Admin',
            'orders' => $orders,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'status' => $status,
            'paymentStatus' => $paymentStatus,
            'search' => $search
        ]);
    }
    
    /**
     * Week 3 Day 14: Order Detail with Courier Assignment
     * GET: ?route=admin.orderDetail&id=ORDER_NUMBER
     */
    public function orderDetail()
    {
        $this->requireAuth('admin');
        
        $orderNumber = $_GET['id'] ?? null;
        
        if (!$orderNumber) {
            $_SESSION['error'] = 'Order number required';
            header('Location: index.php?route=admin.orders');
            exit;
        }
        
        // Get order with items
        $order = $this->orderModel->getOrderWithItems($orderNumber);
        
        if (!$order) {
            $_SESSION['error'] = 'Order not found';
            header('Location: index.php?route=admin.orders');
            exit;
        }
        
        // Get customer info
        $customer = $this->userModel->findById($order['user_id']);
        
        // Get courier info if assigned
        $courier = null;
        if ($order['courier_id']) {
            $courier = $this->userModel->findById($order['courier_id']);
        }
        
        // Get all available couriers
        $couriers = $this->userModel->getCouriers();
        
        $this->render('admin/orders/detail', [
            'title' => 'Order #' . $orderNumber . ' - Admin',
            'order' => $order,
            'customer' => $customer,
            'courier' => $courier,
            'couriers' => $couriers
        ]);
    }
    
    /**
     * Week 3 Day 14: Assign Courier to Order
     * POST: ?route=admin.assignCourier
     */
    public function assignCourier()
    {
        header('Content-Type: application/json');
        $this->requireAuth('admin');
        
        $orderNumber = $_POST['order_number'] ?? null;
        $courierId = $_POST['courier_id'] ?? null;
        
        if (!$orderNumber || !$courierId) {
            echo json_encode(['success' => false, 'message' => 'Order number and courier ID required']);
            exit;
        }
        
        // Validate courier exists
        $courier = $this->userModel->findById($courierId);
        if (!$courier || $courier['role'] !== 'kurir') {
            echo json_encode(['success' => false, 'message' => 'Invalid courier']);
            exit;
        }
        
        // Update order
        $stmt = $this->pdo->prepare("
            UPDATE orders 
            SET courier_id = :courier_id 
            WHERE order_number = :order_number
        ");
        
        $success = $stmt->execute([
            'courier_id' => $courierId,
            'order_number' => $orderNumber
        ]);
        
        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Courier assigned successfully',
                'courier_name' => $courier['name']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to assign courier']);
        }
        exit;
    }
    
    /**
     * Week 3 Day 14: Update Order Status
     * POST: ?route=admin.updateOrderStatus
     */
    public function updateOrderStatus()
    {
        header('Content-Type: application/json');
        $this->requireAuth('admin');
        
        $orderNumber = $_POST['order_number'] ?? null;
        $status = $_POST['status'] ?? null;
        
        if (!$orderNumber || !$status) {
            echo json_encode(['success' => false, 'message' => 'Order number and status required']);
            exit;
        }
        
        // Validate status
        $validStatuses = ['pending', 'confirmed', 'packing', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            echo json_encode(['success' => false, 'message' => 'Invalid status']);
            exit;
        }
        
        // Update status
        $success = $this->orderModel->updateStatus($orderNumber, $status);
        
        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Order status updated to ' . strtoupper($status)
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status']);
        }
        exit;
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
            error_log("Get order count error: " . $e->getMessage());
            return 0;
        }
    }
    
    // ==================== VOUCHER MANAGEMENT ====================
    
    /**
     * List all vouchers
     * GET /admin/vouchers
     */
    public function vouchers()
    {
        $this->requireAuth('admin');
        
        // Pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        // Get vouchers
        $vouchers = $this->voucherModel->getAll($limit, $offset);
        $totalVouchers = $this->voucherModel->getTotalCount();
        $totalPages = ceil($totalVouchers / $limit);
        
        $this->render('admin/vouchers/index', [
            'title' => 'Manage Vouchers - Admin',
            'vouchers' => $vouchers,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalVouchers' => $totalVouchers
        ]);
    }
    
    /**
     * Show create voucher form
     * GET /admin/vouchers/create
     */
    public function createVoucher()
    {
        $this->requireAuth('admin');
        
        // Handle POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreateVoucher();
            return;
        }
        
        // Show form
        $this->render('admin/vouchers/form', [
            'title' => 'Create Voucher - Admin',
            'voucher' => null,
            'action' => 'create'
        ]);
    }
    
    /**
     * Handle create voucher form submission
     */
    private function handleCreateVoucher()
    {
        // Validate input
        $code = trim($_POST['code'] ?? '');
        $discountPercent = (int)($_POST['discount_percent'] ?? 0);
        $minPurchase = (float)($_POST['min_purchase'] ?? 0);
        $usageLimit = (int)($_POST['usage_limit'] ?? 1);
        $expiresAt = trim($_POST['expires_at'] ?? '');
        
        // Validation
        $errors = [];
        
        if (empty($code)) {
            $errors[] = 'Kode voucher wajib diisi';
        } elseif ($this->voucherModel->codeExists($code)) {
            $errors[] = 'Kode voucher sudah digunakan';
        }
        
        if ($discountPercent <= 0 || $discountPercent > 100) {
            $errors[] = 'Diskon harus antara 1-100%';
        }
        
        if ($usageLimit <= 0) {
            $errors[] = 'Batas penggunaan harus lebih dari 0';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode(', ', $errors);
            header('Location: ?route=admin.createVoucher');
            exit;
        }
        
        // Create voucher
        $data = [
            'code' => $code,
            'discount_percent' => $discountPercent,
            'min_purchase' => $minPurchase,
            'usage_limit' => $usageLimit,
            'expires_at' => !empty($expiresAt) ? $expiresAt : null
        ];
        
        if ($this->voucherModel->create($data)) {
            $_SESSION['success'] = 'Voucher berhasil dibuat!';
            header('Location: ?route=admin.vouchers');
        } else {
            $_SESSION['error'] = 'Gagal membuat voucher';
            header('Location: ?route=admin.createVoucher');
        }
        exit;
    }
    
    /**
     * Show edit voucher form
     * GET /admin/vouchers/edit?id=X
     */
    public function editVoucher()
    {
        $this->requireAuth('admin');
        
        $id = (int)($_GET['id'] ?? 0);
        $voucher = $this->voucherModel->getById($id);
        
        if (!$voucher) {
            $_SESSION['error'] = 'Voucher tidak ditemukan';
            header('Location: ?route=admin.vouchers');
            exit;
        }
        
        // Handle POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleEditVoucher($id);
            return;
        }
        
        // Show form
        $this->render('admin/vouchers/form', [
            'title' => 'Edit Voucher - Admin',
            'voucher' => $voucher,
            'action' => 'edit'
        ]);
    }
    
    /**
     * Handle edit voucher form submission
     */
    private function handleEditVoucher($id)
    {
        // Validate input
        $code = trim($_POST['code'] ?? '');
        $discountPercent = (int)($_POST['discount_percent'] ?? 0);
        $minPurchase = (float)($_POST['min_purchase'] ?? 0);
        $usageLimit = (int)($_POST['usage_limit'] ?? 1);
        $expiresAt = trim($_POST['expires_at'] ?? '');
        
        // Validation
        $errors = [];
        
        if (empty($code)) {
            $errors[] = 'Kode voucher wajib diisi';
        } elseif ($this->voucherModel->codeExists($code, $id)) {
            $errors[] = 'Kode voucher sudah digunakan';
        }
        
        if ($discountPercent <= 0 || $discountPercent > 100) {
            $errors[] = 'Diskon harus antara 1-100%';
        }
        
        if ($usageLimit <= 0) {
            $errors[] = 'Batas penggunaan harus lebih dari 0';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode(', ', $errors);
            header('Location: ?route=admin.editVoucher&id=' . $id);
            exit;
        }
        
        // Update voucher
        $data = [
            'code' => $code,
            'discount_percent' => $discountPercent,
            'min_purchase' => $minPurchase,
            'usage_limit' => $usageLimit,
            'expires_at' => !empty($expiresAt) ? $expiresAt : null
        ];
        
        if ($this->voucherModel->update($id, $data)) {
            $_SESSION['success'] = 'Voucher berhasil diupdate!';
            header('Location: ?route=admin.vouchers');
        } else {
            $_SESSION['error'] = 'Gagal mengupdate voucher';
            header('Location: ?route=admin.editVoucher&id=' . $id);
        }
        exit;
    }
    
    /**
     * Delete voucher
     * POST /admin/vouchers/delete?id=X
     */
    public function deleteVoucher()
    {
        $this->requireAuth('admin');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=admin.vouchers');
            exit;
        }
        
        $id = (int)($_POST['id'] ?? 0);
        
        if ($this->voucherModel->delete($id)) {
            $_SESSION['success'] = 'Voucher berhasil dihapus!';
        } else {
            $_SESSION['error'] = 'Gagal menghapus voucher';
        }
        
        header('Location: ?route=admin.vouchers');
        exit;
    }
    
    /**
     * Show voucher usage statistics
     * GET /admin/vouchers/usage?id=X
     */
    public function voucherUsage()
    {
        $this->requireAuth('admin');
        
        $id = (int)($_GET['id'] ?? 0);
        $voucher = $this->voucherModel->getById($id);
        
        if (!$voucher) {
            $_SESSION['error'] = 'Voucher tidak ditemukan';
            header('Location: ?route=admin.vouchers');
            exit;
        }
        
        // Get usage history
        $usageHistory = $this->voucherModel->getUsageHistory($id);
        
        $this->render('admin/vouchers/usage', [
            'title' => 'Voucher Usage - Admin',
            'voucher' => $voucher,
            'usageHistory' => $usageHistory
        ]);
    }
    
    // ==================== ANALYTICS & REPORTS (Week 4 Day 18) ====================
    
    /**
     * Detailed reports page with date range selector
     * GET /admin/reports
     */
    public function reports()
    {
        $this->requireAuth('admin');
        
        // Get date range from query params or default to last 30 days
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        
        // Get analytics data
        $dailySales = $this->analyticsModel->getDailySales($startDate, $endDate);
        $topProducts = $this->analyticsModel->getTopProducts(20);
        $categoryStats = $this->analyticsModel->getCategoryStats();
        $revenueStats = $this->analyticsModel->getRevenueStats();
        $orderStats = $this->analyticsModel->getOrderStats();
        
        $this->render('admin/reports', [
            'title' => 'Sales Reports - Admin',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dailySales' => $dailySales,
            'topProducts' => $topProducts,
            'categoryStats' => $categoryStats,
            'revenueStats' => $revenueStats,
            'orderStats' => $orderStats
        ]);
    }
    
    /**
     * Export sales report to CSV
     * GET /admin/reports/export?type=sales&start_date=X&end_date=Y
     */
    public function exportReport()
    {
        $this->requireAuth('admin');
        
        $type = $_GET['type'] ?? 'sales';
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        
        if ($type === 'sales') {
            $csv = $this->analyticsModel->exportSalesCSV($startDate, $endDate);
            $filename = "sales_report_{$startDate}_to_{$endDate}.csv";
        } elseif ($type === 'products') {
            $csv = $this->analyticsModel->exportProductsCSV(100);
            $filename = "products_report_" . date('Y-m-d') . ".csv";
        } else {
            $_SESSION['error'] = 'Invalid export type';
            header('Location: ?route=admin.reports');
            exit;
        }
        
        // Send CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        echo $csv;
        exit;
    }
}
