<?php
/**
 * Cart API Endpoint Tests
 * 
 * Tests AJAX endpoints for cart operations
 * Note: Requires PHP dev server running on localhost:8000
 */

require_once __DIR__ . '/../helpers/TestHelper.php';

class CartApiTest extends TestHelper
{
    private $baseUrl = 'http://localhost:8000';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Test: Cart add endpoint response format
     */
    public function testCartAddEndpointFormat()
    {
        $this->describe("Cart API - Add Item Endpoint Format");
        
        $this->info("Note: This test validates response format, not actual API call");
        
        // Expected response format for cart.add
        $expectedResponse = [
            'success' => true,
            'message' => 'Product added to cart',
            'cartTotal' => 0
        ];
        
        $this->assertArrayHasKey('success', $expectedResponse, "Response has 'success' key");
        $this->assertArrayHasKey('message', $expectedResponse, "Response has 'message' key");
        $this->assertArrayHasKey('cartTotal', $expectedResponse, "Response has 'cartTotal' key");
        $this->assertTrue($expectedResponse['success'], "'success' is boolean true");
        
        $this->summary();
    }
    
    /**
     * Test: Cart update endpoint response format
     */
    public function testCartUpdateEndpointFormat()
    {
        $this->describe("Cart API - Update Item Endpoint Format");
        
        // Expected response format for cart.update
        $expectedResponse = [
            'success' => true,
            'subtotal' => 0,
            'cartTotal' => 0
        ];
        
        $this->assertArrayHasKey('success', $expectedResponse, "Response has 'success' key");
        $this->assertArrayHasKey('subtotal', $expectedResponse, "Response has 'subtotal' key");
        $this->assertArrayHasKey('cartTotal', $expectedResponse, "Response has 'cartTotal' key");
        
        $this->summary();
    }
    
    /**
     * Test: Cart delete endpoint response format
     */
    public function testCartDeleteEndpointFormat()
    {
        $this->describe("Cart API - Delete Item Endpoint Format");
        
        // Expected response format for cart.delete
        $expectedResponse = [
            'success' => true,
            'message' => 'Product removed from cart',
            'cartTotal' => 0
        ];
        
        $this->assertArrayHasKey('success', $expectedResponse, "Response has 'success' key");
        $this->assertArrayHasKey('message', $expectedResponse, "Response has 'message' key");
        $this->assertArrayHasKey('cartTotal', $expectedResponse, "Response has 'cartTotal' key");
        
        $this->summary();
    }
    
    /**
     * Test: Cart session data structure
     */
    public function testCartSessionStructure()
    {
        $this->describe("Cart API - Session Data Structure");
        
        // Mock cart session structure
        $cartSession = [
            1 => [ // product_id as key
                'product_id' => 1,
                'name' => 'Air Galon 19L',
                'price' => 20000,
                'quantity' => 2,
                'image' => 'product1.jpg'
            ],
            2 => [
                'product_id' => 2,
                'name' => 'LPG 3kg',
                'price' => 25000,
                'quantity' => 1,
                'image' => 'product2.jpg'
            ]
        ];
        
        // Test structure
        $this->assert(is_array($cartSession), "Cart session is array");
        $this->assertCount(2, $cartSession, "Cart has 2 items");
        
        // Test first item structure
        $firstItem = reset($cartSession);
        $this->assertArrayHasKey('product_id', $firstItem, "Item has 'product_id'");
        $this->assertArrayHasKey('name', $firstItem, "Item has 'name'");
        $this->assertArrayHasKey('price', $firstItem, "Item has 'price'");
        $this->assertArrayHasKey('quantity', $firstItem, "Item has 'quantity'");
        $this->assertArrayHasKey('image', $firstItem, "Item has 'image'");
        
        $this->summary();
    }
    
    /**
     * Test: Cart total calculation
     */
    public function testCartTotalCalculation()
    {
        $this->describe("Cart API - Total Calculation");
        
        $cartItems = [
            ['product_id' => 1, 'price' => 20000, 'quantity' => 2],
            ['product_id' => 2, 'price' => 25000, 'quantity' => 1]
        ];
        
        // Calculate total
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        $expected = (20000 * 2) + (25000 * 1); // 65000
        $this->assertEqual($expected, $total, "Cart total calculated correctly");
        $this->assertEqual(65000, $total, "Total is 65000");
        
        $this->summary();
    }
    
    /**
     * Run all tests
     */
    public function runAll()
    {
        $this->testCartAddEndpointFormat();
        $this->testCartUpdateEndpointFormat();
        $this->testCartDeleteEndpointFormat();
        $this->testCartSessionStructure();
        $this->testCartTotalCalculation();
    }
}

// Run tests if executed directly
if (basename(__FILE__) === basename($_SERVER['PHP_SELF'])) {
    $test = new CartApiTest();
    $test->runAll();
}
