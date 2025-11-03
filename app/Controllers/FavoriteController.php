<?php

require_once __DIR__ . '/../Models/Favorite.php';
require_once __DIR__ . '/../Models/Product.php';
require_once __DIR__ . '/BaseController.php';

/**
 * FavoriteController
 * Handles wishlist/favorites functionality
 */
class FavoriteController extends BaseController
{
    private $favoriteModel;
    private $productModel;

    public function __construct()
    {
        parent::__construct();
        $this->favoriteModel = new Favorite($this->pdo);
        $this->productModel = new Product($this->pdo);
    }

    /**
     * Add product to favorites (AJAX)
     * POST /index.php?route=favorite.add
     */
    public function add()
    {
        header('Content-Type: application/json');

        // Check if user logged in
        if (!isset($_SESSION['user_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu'
            ]);
            exit;
        }

        // Get POST data (support both JSON and form-data)
        $input = json_decode(file_get_contents('php://input'), true);
        $productId = $input['product_id'] ?? $_POST['product_id'] ?? null;

        if (!$productId) {
            echo json_encode([
                'success' => false,
                'message' => 'Product ID required'
            ]);
            exit;
        }

        // Validate product exists
        $product = $this->productModel->getById($productId);
        if (!$product) {
            echo json_encode([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ]);
            exit;
        }

        // Add to favorites
        $success = $this->favoriteModel->add($_SESSION['user_id'], $productId);

        if ($success) {
            $count = $this->favoriteModel->getCount($_SESSION['user_id']);

            echo json_encode([
                'success' => true,
                'message' => 'Produk ditambahkan ke favorit',
                'is_favorite' => true,
                'favorite_count' => $count
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal menambahkan ke favorit'
            ]);
        }
    }

    /**
     * Remove product from favorites (AJAX)
     * POST /index.php?route=favorite.remove
     */
    public function remove()
    {
        header('Content-Type: application/json');

        // Check if user logged in
        if (!isset($_SESSION['user_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu'
            ]);
            exit;
        }

        // Get POST data (support both JSON and form-data)
        $input = json_decode(file_get_contents('php://input'), true);
        $productId = $input['product_id'] ?? $_POST['product_id'] ?? null;

        if (!$productId) {
            echo json_encode([
                'success' => false,
                'message' => 'Product ID required'
            ]);
            exit;
        }

        // Remove from favorites
        $success = $this->favoriteModel->remove($_SESSION['user_id'], $productId);

        if ($success) {
            $count = $this->favoriteModel->getCount($_SESSION['user_id']);

            echo json_encode([
                'success' => true,
                'message' => 'Produk dihapus dari favorit',
                'is_favorite' => false,
                'favorite_count' => $count
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal menghapus dari favorit'
            ]);
        }
    }

    /**
     * Toggle favorite (add if not exists, remove if exists)
     * POST /index.php?route=favorite.toggle
     */
    public function toggle()
    {
        header('Content-Type: application/json');

        // Check if user logged in
        if (!isset($_SESSION['user_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu',
                'require_login' => true
            ]);
            exit;
        }

        // Get POST data
        $input = json_decode(file_get_contents('php://input'), true);
        $productId = $input['product_id'] ?? $_POST['product_id'] ?? null;

        if (!$productId) {
            echo json_encode([
                'success' => false,
                'message' => 'Product ID required'
            ]);
            exit;
        }

        // Check if already favorited
        $isFavorite = $this->favoriteModel->exists($_SESSION['user_id'], $productId);

        if ($isFavorite) {
            // Remove from favorites
            $success = $this->favoriteModel->remove($_SESSION['user_id'], $productId);
            $message = 'Dihapus dari favorit';
            $newState = false;
        } else {
            // Add to favorites
            $success = $this->favoriteModel->add($_SESSION['user_id'], $productId);
            $message = 'Ditambahkan ke favorit';
            $newState = true;
        }

        if ($success) {
            $count = $this->favoriteModel->getCount($_SESSION['user_id']);

            echo json_encode([
                'success' => true,
                'message' => $message,
                'is_favorite' => $newState,
                'favorite_count' => $count
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Terjadi kesalahan'
            ]);
        }
    }

    /**
     * Show user's favorites page
     * GET /index.php?route=favorites
     */
    public function index()
    {
        // Require authentication
        $this->requireAuth();

        // Get user's favorites
        $favorites = $this->favoriteModel->getByUserId($_SESSION['user_id']);

        // Render view
        $this->render('favorites/index', [
            'title' => 'Favorit Saya - GoRefill',
            'favorites' => $favorites,
            'favoriteCount' => count($favorites)
        ]);
    }
}
