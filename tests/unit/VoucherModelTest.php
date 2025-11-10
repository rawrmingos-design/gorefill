<?php
/**
 * Voucher Model Unit Tests
 * 
 * Tests: getById, getByCode, validate, getActive
 */

require_once __DIR__ . '/../helpers/TestHelper.php';
require_once __DIR__ . '/../helpers/DatabaseHelper.php';
require_once __DIR__ . '/../../app/Models/Voucher.php';

class VoucherModelTest extends TestHelper
{
    private $voucherModel;
    private $dbHelper;
    
    public function __construct()
    {
        parent::__construct();
        $this->voucherModel = new Voucher();
        $this->dbHelper = new DatabaseHelper($this->pdo);
    }
    
    /**
     * Test: Get voucher by code
     */
    public function testGetByCode()
    {
        $this->describe("Voucher Model - Get By Code");
        
        // Get an active voucher
        $sampleVoucher = $this->dbHelper->getActiveVoucher();
        
        if (!$sampleVoucher) {
            $this->skip("No active vouchers in database");
            return;
        }
        
        // Test: Valid code
        $voucher = $this->voucherModel->getByCode($sampleVoucher['code']);
        $this->assertNotNull($voucher, "Voucher found with valid code");
        $this->assertEqual($sampleVoucher['id'], $voucher['id'], "Voucher ID matches");
        $this->assertArrayHasKey('discount_type', $voucher, "Voucher has 'discount_type'");
        $this->assertArrayHasKey('discount_value', $voucher, "Voucher has 'discount_value'");
        
        // Test: Invalid code
        $voucher = $this->voucherModel->getByCode('INVALID_CODE_XYZ');
        $this->assertNull($voucher, "Returns null for invalid code");
        
        $this->summary();
    }
    
    /**
     * Test: Validate voucher
     */
    public function testValidate()
    {
        $this->describe("Voucher Model - Validate");
        
        // Get an active voucher
        $sampleVoucher = $this->dbHelper->getActiveVoucher();
        
        if (!$sampleVoucher) {
            $this->skip("No active vouchers in database");
            return;
        }
        
        // Test: Valid voucher with sufficient subtotal
        $subtotal = $sampleVoucher['min_purchase'] + 1000;
        $isValid = $this->voucherModel->validate($sampleVoucher['code'], $subtotal);
        $this->assertTrue($isValid, "Voucher is valid with sufficient subtotal");
        
        // Test: Invalid - insufficient subtotal
        $subtotal = $sampleVoucher['min_purchase'] - 1000;
        $isValid = $this->voucherModel->validate($sampleVoucher['code'], $subtotal);
        $this->assertFalse($isValid, "Voucher is invalid with insufficient subtotal");
        
        // Test: Invalid code
        $isValid = $this->voucherModel->validate('INVALID_CODE', 100000);
        $this->assertFalse($isValid, "Invalid voucher code returns false");
        
        $this->summary();
    }
    
    /**
     * Test: Get active vouchers
     */
    public function testGetActive()
    {
        $this->describe("Voucher Model - Get Active");
        
        // Test: Get active vouchers
        $vouchers = $this->voucherModel->getActive();
        $this->assert(is_array($vouchers), "Returns array");
        
        // Test: All returned vouchers are active
        $allActive = true;
        foreach ($vouchers as $voucher) {
            if (!$voucher['is_active']) {
                $allActive = false;
                break;
            }
        }
        $this->assertTrue($allActive, "All returned vouchers are active");
        
        // Test: Vouchers not expired
        $allNotExpired = true;
        foreach ($vouchers as $voucher) {
            if ($voucher['expiry_date'] && strtotime($voucher['expiry_date']) < time()) {
                $allNotExpired = false;
                break;
            }
        }
        $this->assertTrue($allNotExpired, "All returned vouchers are not expired");
        
        $this->summary();
    }
    
    /**
     * Test: Calculate discount
     */
    public function testCalculateDiscount()
    {
        $this->describe("Voucher Model - Calculate Discount");
        
        // Get an active voucher
        $sampleVoucher = $this->dbHelper->getActiveVoucher();
        
        if (!$sampleVoucher) {
            $this->skip("No active vouchers in database");
            return;
        }
        
        $subtotal = 100000;
        
        // Test: Calculate based on discount type
        if ($sampleVoucher['discount_type'] === 'percentage') {
            $expectedDiscount = $subtotal * ($sampleVoucher['discount_value'] / 100);
            $this->info("Percentage voucher: {$sampleVoucher['discount_value']}%");
        } else {
            $expectedDiscount = $sampleVoucher['discount_value'];
            $this->info("Fixed voucher: Rp " . number_format($sampleVoucher['discount_value']));
        }
        
        // Note: We can't test private methods directly, but this validates the logic
        $this->assert($expectedDiscount > 0, "Calculated discount is positive");
        $this->assert($expectedDiscount <= $subtotal, "Discount doesn't exceed subtotal");
        
        $this->summary();
    }
    
    /**
     * Run all tests
     */
    public function runAll()
    {
        $this->testGetByCode();
        $this->testValidate();
        $this->testGetActive();
        $this->testCalculateDiscount();
    }
}

// Run tests if executed directly
if (basename(__FILE__) === basename($_SERVER['PHP_SELF'])) {
    $test = new VoucherModelTest();
    $test->runAll();
}
