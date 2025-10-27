<?php
/**
 * Front Controller for GoRefill Application
 * 
 * This file serves as the entry point for all requests.
 * It implements routing using URL query parameter ?route=controller.method
 * 
 * Route Format: index.php?route=controller.method&param=value
 * Examples:
 * - index.php?route=home
 * - index.php?route=auth.login
 * - index.php?route=product.detail&id=5
 * - index.php?route=cart.add
 */

// Load bootstrap (includes config, PDO, helpers, session)
require_once __DIR__ . '/../app/bootstrap.php';

// Get route from query string, default to 'home'
$route = $_GET['route'] ?? 'home';
$method = $_SERVER['REQUEST_METHOD'];

// Parse route into controller and action
$parts = explode('.', $route);
$controllerName = $parts[0] ?? 'home';
$actionName = $parts[1] ?? 'index';

// Route dispatcher using switch/case
try {
    switch ($route) {
        // ==================== HOME ROUTES ====================
        case 'home':
            require_once __DIR__ . '/../app/Controllers/HomeController.php';
            $homeController = new HomeController();
            $homeController->index();
            break;
            
        // ==================== AUTH ROUTES ====================
        case 'auth.login':
            require_once __DIR__ . '/../app/Controllers/AuthController.php';
            $authController = new AuthController();
            if ($method === 'POST') {
                $authController->login();
            } else {
                $authController->showLoginForm();
            }
            break;
            
        case 'auth.register':
            require_once __DIR__ . '/../app/Controllers/AuthController.php';
            $authController = new AuthController();
            if ($method === 'POST') {
                $authController->register();
            } else {
                $authController->showRegisterForm();
            }
            break;
            
        case 'auth.logout':
            require_once __DIR__ . '/../app/Controllers/AuthController.php';
            $authController = new AuthController();
            $authController->logout();
            break;
            
        case 'auth.check':
            require_once __DIR__ . '/../app/Controllers/AuthController.php';
            $authController = new AuthController();
            $authController->checkAuth();
            break;
            
        // // ==================== PROFILE ROUTES ====================
        // case 'profile':
        //     // Check if user is logged in
        //     if (!isset($_SESSION['user_id'])) {
        //         redirect('index.php?route=auth.login');
        //     }
        //     require_once __DIR__ . '/../app/Views/profile.php';
        //     break;
            
        // case 'profile.edit':
        //     require_once __DIR__ . '/../app/Controllers/AuthController.php';
        //     $authController = new AuthController();
        //     $authController->editProfile();
        //     break;
            
        case 'profile.change-password':
            require_once __DIR__ . '/../app/Controllers/AuthController.php';
            $authController = new AuthController();
            $authController->changePassword();
            break;
            
        // case 'profile.delete':
        //     require_once __DIR__ . '/../app/Controllers/AuthController.php';
        //     $authController = new AuthController();
        //     $authController->deleteAccount();
        //     break;
            
        // ==================== PRODUCT ROUTES ====================
        case 'product.list':
        case 'products':
            require_once __DIR__ . '/../app/Controllers/ProductController.php';
            $productController = new ProductController();
            $productController->index();
            break;
            
        case 'product.detail':
            require_once __DIR__ . '/../app/Controllers/ProductController.php';
            $productController = new ProductController();
            $productController->detail();
            break;
            
        case 'product.search':
            require_once __DIR__ . '/../app/Controllers/ProductController.php';
            $productController = new ProductController();
            $productController->search();
            break;
            
        // ==================== CART ROUTES ====================
        case 'cart':
        case 'cart.view':
            require_once __DIR__ . '/../app/Controllers/CartController.php';
            $cartController = new CartController();
            $cartController->index();
            break;
            
        case 'cart.add':
            require_once __DIR__ . '/../app/Controllers/CartController.php';
            $cartController = new CartController();
            $cartController->add();
            break;
            
        case 'cart.update':
            require_once __DIR__ . '/../app/Controllers/CartController.php';
            $cartController = new CartController();
            $cartController->update();
            break;
            
        case 'cart.remove':
            require_once __DIR__ . '/../app/Controllers/CartController.php';
            $cartController = new CartController();
            $cartController->remove();
            break;
            
        case 'cart.get':
            require_once __DIR__ . '/../app/Controllers/CartController.php';
            $cartController = new CartController();
            $cartController->get();
            break;
            
        case 'cart.count':
            require_once __DIR__ . '/../app/Controllers/CartController.php';
            $cartController = new CartController();
            $cartController->count();
            break;
            
        case 'cart.clear':
            require_once __DIR__ . '/../app/Controllers/CartController.php';
            $cartController = new CartController();
            $cartController->clear();
            break;
            
        // ==================== CHECKOUT ROUTES ====================
        case 'checkout':
        case 'checkout.index':
            require_once __DIR__ . '/../app/Controllers/CheckoutController.php';
            $checkoutController = new CheckoutController();
            $checkoutController->index();
            break;
            
        case 'checkout.buyNow':
            require_once __DIR__ . '/../app/Controllers/CheckoutController.php';
            $checkoutController = new CheckoutController();
            $checkoutController->buyNow();
            break;
            
        case 'checkout.selectAddress':
            require_once __DIR__ . '/../app/Controllers/CheckoutController.php';
            $checkoutController = new CheckoutController();
            $checkoutController->selectAddress();
            break;
            
        case 'checkout.applyVoucher':
            require_once __DIR__ . '/../app/Controllers/CheckoutController.php';
            $checkoutController = new CheckoutController();
            $checkoutController->applyVoucher();
            break;
            
        case 'checkout.removeVoucher':
            require_once __DIR__ . '/../app/Controllers/CheckoutController.php';
            $checkoutController = new CheckoutController();
            $checkoutController->removeVoucher();
            break;
            
        case 'checkout.createAddress':
            require_once __DIR__ . '/../app/Controllers/CheckoutController.php';
            $checkoutController = new CheckoutController();
            $checkoutController->createAddress();
            break;
            
        case 'checkout.create':
            require_once __DIR__ . '/../app/Controllers/CheckoutController.php';
            $checkoutController = new CheckoutController();
            $checkoutController->create();
            break;
            
        // ==================== PAYMENT ROUTES (Day 9) ====================
        case 'payment.callback':
            require_once __DIR__ . '/../app/Controllers/PaymentController.php';
            $paymentController = new PaymentController();
            $paymentController->callback();
            break;
            
        case 'payment.success':
            require_once __DIR__ . '/../app/Controllers/PaymentController.php';
            $paymentController = new PaymentController();
            $paymentController->success();
            break;
            
        case 'payment.pending':
            require_once __DIR__ . '/../app/Controllers/PaymentController.php';
            $paymentController = new PaymentController();
            $paymentController->pending();
            break;
            
        case 'payment.failed':
            require_once __DIR__ . '/../app/Controllers/PaymentController.php';
            $paymentController = new PaymentController();
            $paymentController->failed();
            break;
            
        case 'payment.checkStatus':
            require_once __DIR__ . '/../app/Controllers/PaymentController.php';
            $paymentController = new PaymentController();
            $paymentController->checkStatus();
            break;
            
        // ==================== PROFILE ROUTES ====================
        case 'profile':
            require_once __DIR__ . '/../app/Controllers/ProfileController.php';
            $profileController = new ProfileController();
            $profileController->index();
            break;
            
        case 'profile.orders':
            require_once __DIR__ . '/../app/Controllers/ProfileController.php';
            $profileController = new ProfileController();
            $profileController->orders();
            break;
            
        case 'profile.orderDetail':
            require_once __DIR__ . '/../app/Controllers/ProfileController.php';
            $profileController = new ProfileController();
            $profileController->orderDetail();
            break;
            
        case 'profile.invoice':
            require_once __DIR__ . '/../app/Controllers/ProfileController.php';
            $profileController = new ProfileController();
            $profileController->invoice();
            break;
            
        case 'profile.edit':
            require_once __DIR__ . '/../app/Controllers/ProfileController.php';
            $profileController = new ProfileController();
            $profileController->edit();
            break;
            
        // ==================== ORDER ROUTES ====================
        case 'order.index':
        case 'order.list':
            require_once __DIR__ . '/../app/Controllers/OrderController.php';
            $orderController = new OrderController($pdo);
            $orderController->index();
            break;
            
        case 'order.track':
            require_once __DIR__ . '/../app/Controllers/OrderController.php';
            $orderController = new OrderController($pdo);
            $orderController->track();
            break;
            
        case 'order.show':
            require_once __DIR__ . '/../app/Controllers/OrderController.php';
            $orderController = new OrderController($pdo);
            $orderController->show();
            break;
            
        // ==================== ADMIN ROUTES ====================
        case 'admin':
        case 'admin.dashboard':
            require_once __DIR__ . '/../app/Controllers/AdminController.php';
            $adminController = new AdminController();
            $adminController->dashboard();
            break;
            
        case 'admin.products':
            require_once __DIR__ . '/../app/Controllers/AdminController.php';
            $adminController = new AdminController();
            $adminController->products();
            break;
            
        case 'admin.products.create':
            require_once __DIR__ . '/../app/Controllers/AdminController.php';
            $adminController = new AdminController();
            if ($method === 'POST') {
                $adminController->createProduct();
            } else {
                $adminController->showCreateProduct();
            }
            break;
            
        case 'admin.products.edit':
            require_once __DIR__ . '/../app/Controllers/AdminController.php';
            $adminController = new AdminController();
            if ($method === 'POST') {
                $adminController->editProduct();
            } else {
                $adminController->showEditProduct();
            }
            break;
            
        case 'admin.products.delete':
            require_once __DIR__ . '/../app/Controllers/AdminController.php';
            $adminController = new AdminController();
            $adminController->deleteProduct();
            break;
            
        // ==================== ADMIN CATEGORY ROUTES ====================
        case 'admin.categories':
            require_once __DIR__ . '/../app/Controllers/AdminCategoryController.php';
            $categoryController = new AdminCategoryController();
            $categoryController->index();
            break;
            
        case 'admin.categories.create':
            require_once __DIR__ . '/../app/Controllers/AdminCategoryController.php';
            $categoryController = new AdminCategoryController();
            $categoryController->create();
            break;
            
        case 'admin.categories.store':
            require_once __DIR__ . '/../app/Controllers/AdminCategoryController.php';
            $categoryController = new AdminCategoryController();
            $categoryController->store();
            break;
            
        case 'admin.categories.edit':
            require_once __DIR__ . '/../app/Controllers/AdminCategoryController.php';
            $categoryController = new AdminCategoryController();
            $categoryController->edit();
            break;
            
        case 'admin.categories.update':
            require_once __DIR__ . '/../app/Controllers/AdminCategoryController.php';
            $categoryController = new AdminCategoryController();
            $categoryController->update();
            break;
            
        case 'admin.categories.destroy':
            require_once __DIR__ . '/../app/Controllers/AdminCategoryController.php';
            $categoryController = new AdminCategoryController();
            $categoryController->destroy();
            break;
            
        // ==================== COURIER ROUTES ====================
        case 'courier':
        case 'courier.dashboard':
            require_once __DIR__ . '/../app/Controllers/CourierController.php';
            $courierController = new CourierController($pdo);
            $courierController->index();
            break;
            
        case 'courier.updateLocation':
            require_once __DIR__ . '/../app/Controllers/CourierController.php';
            $courierController = new CourierController($pdo);
            $courierController->updateLocation();
            break;
            
        case 'courier.getMyLocation':
            require_once __DIR__ . '/../app/Controllers/CourierController.php';
            $courierController = new CourierController($pdo);
            $courierController->getMyLocation();
            break;
            
        case 'courier.getLocation':
            require_once __DIR__ . '/../app/Controllers/CourierController.php';
            $courierController = new CourierController($pdo);
            $courierController->getLocation();
            break;
            
        case 'courier.startDelivery':
            require_once __DIR__ . '/../app/Controllers/CourierController.php';
            $courierController = new CourierController($pdo);
            $courierController->startDelivery();
            break;
            
        case 'courier.completeDelivery':
            require_once __DIR__ . '/../app/Controllers/CourierController.php';
            $courierController = new CourierController($pdo);
            $courierController->completeDelivery();
            break;
            
        // ==================== TESTING ROUTE ====================
        case 'test.routing':
            // Test route to verify routing system works
            echo '<!DOCTYPE html>
            <html>
            <head>
                <title>Routing Test</title>
                <script src="https://cdn.tailwindcss.com"></script>
            </head>
            <body class="bg-gray-100 p-8">
                <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow">
                    <h1 class="text-3xl font-bold mb-4">✅ Routing System Working!</h1>
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4">
                        <p class="text-green-800 font-semibold">Day 2 Complete: Front Controller Routing</p>
                    </div>
                    <div class="space-y-2">
                        <p><strong>Current Route:</strong> <code class="bg-gray-100 px-2 py-1 rounded">' . e($route) . '</code></p>
                        <p><strong>Request Method:</strong> ' . e($method) . '</p>
                        <p><strong>Controller:</strong> ' . e($controllerName) . '</p>
                        <p><strong>Action:</strong> ' . e($actionName) . '</p>
                    </div>
                    <div class="mt-6">
                        <h2 class="text-xl font-bold mb-2">Test Routes:</h2>
                        <ul class="space-y-1">
                            <li><a href="?route=home" class="text-blue-600 hover:underline">Home</a></li>
                            <li><a href="?route=auth.login" class="text-blue-600 hover:underline">Login</a></li>
                            <li><a href="?route=auth.register" class="text-blue-600 hover:underline">Register</a></li>
                            <li><a href="?route=products" class="text-blue-600 hover:underline">Products</a></li>
                            <li><a href="?route=cart" class="text-blue-600 hover:underline">Cart</a></li>
                        </ul>
                    </div>
                </div>
            </body>
            </html>';
            break;
            
        // ==================== 404 NOT FOUND ====================
        default:
            http_response_code(404);
            require_once __DIR__ . '/../app/Views/errors/404.php';
            break;
    }
    
} catch (Exception $e) {
    // Handle errors
    if ($config['app']['debug']) {
        // Show detailed error in development
        echo '<h1>Error</h1>';
        echo '<p>' . e($e->getMessage()) . '</p>';
        echo '<pre>' . e($e->getTraceAsString()) . '</pre>';
    } else {
        // Show generic error in production
        http_response_code(500);
        echo '<h1>Sorry, something went wrong.</h1>';
        error_log($e->getMessage());
    }
}
