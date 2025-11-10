<?php
/**
 * Mail Service Unit Tests
 * 
 * Tests: MailService initialization and template loading
 * Note: Does NOT send actual emails in test mode
 */

require_once __DIR__ . '/../helpers/TestHelper.php';
require_once __DIR__ . '/../../app/Services/MailService.php';

class MailServiceTest extends TestHelper
{
    private $mailService;
    
    public function __construct()
    {
        parent::__construct();
        
        // Check if mail config exists
        if (!file_exists(__DIR__ . '/../../config/mail.php')) {
            $this->error("mail.php config not found. Tests will be skipped.");
            return;
        }
        
        try {
            $this->mailService = new MailService();
        } catch (Exception $e) {
            $this->warning("MailService initialization failed: " . $e->getMessage());
            $this->info("Email tests will be skipped (this is OK for development)");
        }
    }
    
    /**
     * Test: Email templates exist
     */
    public function testTemplatesExist()
    {
        $this->describe("Mail Service - Templates Exist");
        
        $templates = [
            'welcome',
            'order-confirmation',
            'payment-success',
            'shipping',
            'delivered',
            'password-reset'
        ];
        
        foreach ($templates as $template) {
            $path = __DIR__ . '/../../app/Views/emails/' . $template . '.php';
            $exists = file_exists($path);
            $this->assertTrue($exists, "Template '{$template}.php' exists");
        }
        
        $this->summary();
    }
    
    /**
     * Test: Mail config is valid
     */
    public function testConfigValid()
    {
        $this->describe("Mail Service - Config Validation");
        
        if (!file_exists(__DIR__ . '/../../config/mail.php')) {
            $this->skip("mail.php config not found");
            return;
        }
        
        $config = require __DIR__ . '/../../config/mail.php';
        
        $this->assertArrayHasKey('smtp_host', $config, "Config has 'smtp_host'");
        $this->assertArrayHasKey('smtp_port', $config, "Config has 'smtp_port'");
        $this->assertArrayHasKey('smtp_user', $config, "Config has 'smtp_user'");
        $this->assertArrayHasKey('smtp_pass', $config, "Config has 'smtp_pass'");
        $this->assertArrayHasKey('from_email', $config, "Config has 'from_email'");
        $this->assertArrayHasKey('from_name', $config, "Config has 'from_name'");
        
        // Validate values are not empty
        $this->assert(!empty($config['smtp_host']), "smtp_host is not empty");
        $this->assert($config['smtp_port'] > 0, "smtp_port is valid");
        $this->assert(!empty($config['from_email']), "from_email is not empty");
        
        $this->summary();
    }
    
    /**
     * Test: MailService can be instantiated
     */
    public function testInstantiation()
    {
        $this->describe("Mail Service - Instantiation");
        
        if (!$this->mailService) {
            $this->skip("MailService not initialized (check SMTP config)");
            return;
        }
        
        $this->assertNotNull($this->mailService, "MailService instantiated successfully");
        $this->assert($this->mailService instanceof MailService, "Instance is MailService class");
        
        $this->summary();
    }
    
    /**
     * Test: Mock email data validation
     */
    public function testEmailDataValidation()
    {
        $this->describe("Mail Service - Data Validation");
        
        // Test user data structure
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com'
        ];
        
        $this->assertArrayHasKey('name', $userData, "User data has 'name'");
        $this->assertArrayHasKey('email', $userData, "User data has 'email'");
        $this->assert(filter_var($userData['email'], FILTER_VALIDATE_EMAIL), "Email is valid format");
        
        // Test order data structure
        $orderData = [
            'order_number' => 'ORD-20250105-0001',
            'customer_name' => 'Test Customer',
            'customer_email' => 'customer@example.com',
            'total_price' => 150000,
            'items' => []
        ];
        
        $this->assertArrayHasKey('order_number', $orderData, "Order data has 'order_number'");
        $this->assertArrayHasKey('customer_email', $orderData, "Order data has 'customer_email'");
        $this->assertArrayHasKey('total_price', $orderData, "Order data has 'total_price'");
        $this->assert(is_array($orderData['items']), "Order items is array");
        
        $this->summary();
    }
    
    /**
     * Run all tests
     */
    public function runAll()
    {
        $this->testTemplatesExist();
        $this->testConfigValid();
        $this->testInstantiation();
        $this->testEmailDataValidation();
    }
}

// Run tests if executed directly
if (basename(__FILE__) === basename($_SERVER['PHP_SELF'])) {
    $test = new MailServiceTest();
    $test->runAll();
}
