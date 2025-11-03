<?php
/**
 * Cart Controller for GoRefill Application
 * 
 * Handles shopping cart operations with AJAX:
 * - Add items to cart
 * - Update quantities
 * - Remove items
 * - Get cart data
 * - Count cart items
 */

require_once __DIR__ . '/../Models/Product.php';
require_once __DIR__ . '/BaseController.php';

class CartController extends BaseController
{
    /**
     * Product model instance
     * @var Product
     */
    private $productModel;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product($this->pdo);
        
        // Initialize cart session
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }
    
    /**
     * View Cart Page
     */
    public function index()
    {
        $this->requireAuth();
        
        $cartItems = $this->getCartItems();
        $cartTotal = $this->calculateTotal($cartItems);
        
        $this->render('cart/index', [
            'title' => 'Shopping Cart - GoRefill',
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
            'cartCount' => $this->getCartCount()
        ]);
    }
    
    /**
     * Add Product to Cart (AJAX)
     */
    public function add()
    {
        header('Content-Type: application/json');
        
        try {
            // Get POST data
            $data = json_decode(file_get_contents('php://input'), true);
            $productId = $data['product_id'] ?? null;
            $quantity = isset($data['quantity']) ? (int)$data['quantity'] : 1;
            
            // Validate
            if (!$productId) {
                $this->json(['success' => false, 'message' => 'Product ID required']);
                return;
            }
            
            if ($quantity < 1) {
                $this->json(['success' => false, 'message' => 'Invalid quantity']);
                return;
            }
            
            // Get product
            $product = $this->productModel->getById($productId);
            
            if (!$product) {
                $this->json(['success' => false, 'message' => 'Product not found']);
                return;
            }
            
            // Check stock
            if ($product['stock'] < $quantity) {
                $this->json(['success' => false, 'message' => 'Insufficient stock']);
                return;
            }
            
            // Add to cart
            if (isset($_SESSION['cart'][$productId])) {
                // Update existing item
                $newQty = $_SESSION['cart'][$productId]['qty'] + $quantity;
                
                if ($newQty > $product['stock']) {
                    $this->json(['success' => false, 'message' => 'Exceeds available stock']);
                    return;
                }
                
                $_SESSION['cart'][$productId]['qty'] = $newQty;
            } else {
                // Add new item
                $_SESSION['cart'][$productId] = [
                    'qty' => $quantity,
                    'price' => $product['price']
                ];
            }
            
            $this->json([
                'success' => true,
                'message' => 'Product added to cart',
                'cart_count' => $this->getCartCount(),
                'cart' => $_SESSION['cart']
            ]);
            
        } catch (Exception $e) {
            error_log("Cart add error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Failed to add product']);
        }
    }
    
    /**
     * Update Cart Item Quantity (AJAX)
     */
    public function update()
    {
        header('Content-Type: application/json');
        
        try {
            // Get POST data
            $data = json_decode(file_get_contents('php://input'), true);
            $productId = $data['product_id'] ?? null;
            $quantity = isset($data['quantity']) ? (int)$data['quantity'] : 0;
            
            // Validate
            if (!$productId) {
                $this->json(['success' => false, 'message' => 'Product ID required']);
                return;
            }
            
            if ($quantity < 0) {
                $this->json(['success' => false, 'message' => 'Invalid quantity']);
                return;
            }
            
            // Check if item exists in cart
            if (!isset($_SESSION['cart'][$productId])) {
                $this->json(['success' => false, 'message' => 'Item not in cart']);
                return;
            }
            
            // If quantity is 0, remove item
            if ($quantity === 0) {
                unset($_SESSION['cart'][$productId]);
                $this->json([
                    'success' => true,
                    'message' => 'Item removed from cart',
                    'cart_count' => $this->getCartCount()
                ]);
                return;
            }
            
            // Get product to check stock
            $product = $this->productModel->getById($productId);
            
            if (!$product) {
                $this->json(['success' => false, 'message' => 'Product not found']);
                return;
            }
            
            // Check stock
            if ($quantity > $product['stock']) {
                $this->json(['success' => false, 'message' => 'Exceeds available stock']);
                return;
            }
            
            // Update quantity
            $_SESSION['cart'][$productId]['qty'] = $quantity;
            
            // Get updated cart data
            $cartItems = $this->getCartItems();
            $cartTotal = $this->calculateTotal($cartItems);
            
            // Calculate new subtotal for this item
            $itemSubtotal = $product['price'] * $quantity;
            
            $this->json([
                'success' => true,
                'message' => 'Cart updated',
                'cart_count' => $this->getCartCount(),
                'item_subtotal' => $itemSubtotal,
                'cart_total' => $cartTotal,
                'quantity' => $quantity
            ]);
            
        } catch (Exception $e) {
            error_log("Cart update error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Failed to update cart']);
        }
    }
    
    /**
     * Remove Item from Cart (AJAX)
     */
    public function remove()
    {
        header('Content-Type: application/json');
        
        try {
            // Get POST data
            $data = json_decode(file_get_contents('php://input'), true);
            $productId = $data['product_id'] ?? null;
            
            // Validate
            if (!$productId) {
                $this->json(['success' => false, 'message' => 'Product ID required']);
                return;
            }
            
            // Check if item exists
            if (!isset($_SESSION['cart'][$productId])) {
                $this->json(['success' => false, 'message' => 'Item not in cart']);
                return;
            }
            
            // Remove item
            unset($_SESSION['cart'][$productId]);
            
            $this->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'cart_count' => $this->getCartCount()
            ]);
            
        } catch (Exception $e) {
            error_log("Cart remove error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Failed to remove item']);
        }
    }
    
    /**
     * Get Cart Data (AJAX)
     */
    public function get()
    {
        header('Content-Type: application/json');
        
        try {
            $cartItems = $this->getCartItems();
            $cartTotal = $this->calculateTotal($cartItems);
            
            $this->json([
                'success' => true,
                'cart' => $cartItems,
                'total' => $cartTotal,
                'count' => $this->getCartCount()
            ]);
            
        } catch (Exception $e) {
            error_log("Cart get error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Failed to get cart']);
        }
    }
    
    /**
     * Get Cart Item Count (AJAX)
     */
    public function count()
    {
        header('Content-Type: application/json');
        
        $this->json([
            'success' => true,
            'count' => $this->getCartCount()
        ]);
    }
    
    /**
     * Clear entire cart
     */
    public function clear()
    {
        header('Content-Type: application/json');
        
        $_SESSION['cart'] = [];
        
        $this->json([
            'success' => true,
            'message' => 'Cart cleared',
            'count' => 0
        ]);
    }
    
    /**
     * Get cart items with product details
     * 
     * @return array
     */
    private function getCartItems()
    {
        $cartItems = [];
        
        if (empty($_SESSION['cart'])) {
            return $cartItems;
        }
        
        foreach ($_SESSION['cart'] as $productId => $item) {
            $product = $this->productModel->getById($productId);
            
            if ($product) {
                $cartItems[] = [
                    'id' => $product['id'],
                    'slug' => $product['slug'], // ✅ Include slug for SEO-friendly links
                    'name' => $product['name'],
                    'image' => $product['image'],
                    'price' => $item['price'],
                    'qty' => $item['qty'],
                    'stock' => $product['stock'],
                    'subtotal' => $item['price'] * $item['qty']
                ];
            }
        }
        
        return $cartItems;
    }
    
    /**
     * Calculate cart total
     * 
     * @param array $cartItems
     * @return float
     */
    private function calculateTotal($cartItems)
    {
        $total = 0;
        
        foreach ($cartItems as $item) {
            $total += $item['subtotal'];
        }
        
        return $total;
    }
    
    /**
     * Get total cart item count
     * 
     * @return int
     */
    private function getCartCount()
    {
        if (empty($_SESSION['cart'])) {
            return 0;
        }
        
        $count = 0;
        foreach ($_SESSION['cart'] as $item) {
            $count += $item['qty'];
        }
        
        return $count;
    }
}
