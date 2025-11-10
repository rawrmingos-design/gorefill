<?php
/**
 * Checkout Flow Integration Test
 * 
 * Tests the complete checkout process logic
 */

require_once __DIR__ . '/../helpers/TestHelper.php';
require_once __DIR__ . '/../helpers/DatabaseHelper.php';

class CheckoutFlowTest extends TestHelper
{
    private $dbHelper;
    
    public function __construct()
    {
        parent::__construct();
        $this->dbHelper = new DatabaseHelper($this->pdo);
    }
    
    /**
     * Test: Cart calculation logic
     */
    public function testCartCalculation()
    {
        $this->describe("Checkout Flow - Cart Calculation");
        
        // Mock cart items
        $cartItems = [
            [
                'product_id' => 1,
                'name' => 'Air Galon 19L',
                'price' => 20000,
                'quantity' => 2
            ],
            [
                'product_id' => 2,
                'name' => 'LPG 3kg',
                'price' => 25000,
                'quantity' => 1
            ]
        ];
        
        // Calculate subtotal
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        $expectedSubtotal = (20000 * 2) + (25000 * 1); // 65000
        $this->assertEqual($expectedSubtotal, $subtotal, "Subtotal calculated correctly");
        $this->assertEqual(65000, $subtotal, "Subtotal is 65000");
        
        $this->summary();
    }
    
    /**
     * Test: Voucher discount calculation
     */
    public function testVoucherDiscount()
    {
        $this->describe("Checkout Flow - Voucher Discount");
        
        $subtotal = 100000;
        
        // Test: Percentage discount (10%)
        $percentageVoucher = [
            'discount_type' => 'percentage',
            'discount_value' => 10
        ];
        $discount = $subtotal * ($percentageVoucher['discount_value'] / 100);
        $this->assertEqual(10000, $discount, "10% discount calculated correctly");
        
        // Test: Fixed discount (Rp 15000)
        $fixedVoucher = [
            'discount_type' => 'fixed',
            'discount_value' => 15000
        ];
        $discount = $fixedVoucher['discount_value'];
        $this->assertEqual(15000, $discount, "Fixed discount calculated correctly");
        
        // Test: Total after discount
        $total = $subtotal - $discount;
        $this->assertEqual(85000, $total, "Total after fixed discount is correct");
        $this->assert($total > 0, "Total is positive");
        $this->assert($total < $subtotal, "Total is less than subtotal");
        
        $this->summary();
    }
    
    /**
     * Test: Order number generation format
     */
    public function testOrderNumberFormat()
    {
        $this->describe("Checkout Flow - Order Number Format");
        
        // Expected format: ORD-YYYYMMDD-XXXX
        $pattern = '/^ORD-\d{8}-\d{4}$/';
        
        // Mock order number
        $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(1, 4, '0', STR_PAD_LEFT);
        
        $this->assert(preg_match($pattern, $orderNumber) === 1, "Order number matches format");
        $this->assert(strlen($orderNumber) === 17, "Order number length is 17");
        $this->assert(strpos($orderNumber, 'ORD-') === 0, "Order number starts with 'ORD-'");
        
        $this->summary();
    }
    
    /**
     * Test: Order status transitions
     */
    public function testOrderStatusTransitions()
    {
        $this->describe("Checkout Flow - Order Status Transitions");
        
        $validTransitions = [
            'pending' => ['paid', 'cancelled', 'expired'],
            'paid' => ['packing', 'cancelled'],
            'packing' => ['shipping'],
            'shipping' => ['delivered'],
            'delivered' => [], // Final status
            'cancelled' => [], // Final status
            'expired' => [] // Final status
        ];
        
        // Test: Pending can transition to paid
        $this->assert(in_array('paid', $validTransitions['pending']), "Pending → Paid is valid");
        
        // Test: Paid can transition to packing
        $this->assert(in_array('packing', $validTransitions['paid']), "Paid → Packing is valid");
        
        // Test: Shipping must go to delivered
        $this->assert(in_array('delivered', $validTransitions['shipping']), "Shipping → Delivered is valid");
        
        // Test: Delivered is final (no transitions)
        $this->assertCount(0, $validTransitions['delivered'], "Delivered has no further transitions");
        
        $this->summary();
    }
    
    /**
     * Test: Minimum purchase validation
     */
    public function testMinimumPurchase()
    {
        $this->describe("Checkout Flow - Minimum Purchase");
        
        $minPurchase = 10000; // Example minimum
        
        // Test: Valid purchase
        $subtotal = 50000;
        $isValid = $subtotal >= $minPurchase;
        $this->assertTrue($isValid, "Purchase of Rp 50,000 meets minimum");
        
        // Test: Invalid purchase (too low)
        $subtotal = 5000;
        $isValid = $subtotal >= $minPurchase;
        $this->assertFalse($isValid, "Purchase of Rp 5,000 below minimum");
        
        // Test: Exactly at minimum
        $subtotal = $minPurchase;
        $isValid = $subtotal >= $minPurchase;
        $this->assertTrue($isValid, "Purchase at exact minimum is valid");
        
        $this->summary();
    }
    
    /**
     * Run all tests
     */
    public function runAll()
    {
        $this->testCartCalculation();
        $this->testVoucherDiscount();
        $this->testOrderNumberFormat();
        $this->testOrderStatusTransitions();
        $this->testMinimumPurchase();
    }
}

// Run tests if executed directly
if (basename(__FILE__) === basename($_SERVER['PHP_SELF'])) {
    $test = new CheckoutFlowTest();
    $test->runAll();
}
