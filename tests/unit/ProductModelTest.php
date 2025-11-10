<?php
/**
 * Product Model Unit Tests
 * 
 * Tests: getById, getAll, search, getBySlug
 */

require_once __DIR__ . '/../helpers/TestHelper.php';
require_once __DIR__ . '/../helpers/DatabaseHelper.php';
require_once __DIR__ . '/../../app/Models/Product.php';

class ProductModelTest extends TestHelper
{
    private $productModel;
    private $dbHelper;
    
    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
        $this->dbHelper = new DatabaseHelper($this->pdo);
    }
    
    /**
     * Test: Get product by ID
     */
    public function testGetById()
    {
        $this->describe("Product Model - Get By ID");
        
        // Get a sample product
        $sampleProduct = $this->dbHelper->getSampleProduct();
        
        if (!$sampleProduct) {
            $this->skip("No products in database");
            return;
        }
        
        // Test: Valid ID
        $product = $this->productModel->getById($sampleProduct['id']);
        $this->assertNotNull($product, "Product found with valid ID");
        $this->assertEqual($sampleProduct['id'], $product['id'], "Product ID matches");
        $this->assertArrayHasKey('name', $product, "Product has 'name' field");
        $this->assertArrayHasKey('price', $product, "Product has 'price' field");
        $this->assertArrayHasKey('category_name', $product, "Product has 'category_name' (JOIN works)");
        
        // Test: Invalid ID
        $product = $this->productModel->getById(999999);
        $this->assertNull($product, "Returns null for invalid ID");
        
        $this->summary();
    }
    
    /**
     * Test: Get all products
     */
    public function testGetAll()
    {
        $this->describe("Product Model - Get All");
        
        // Test: Get products with default params
        $products = $this->productModel->getAll(null, 10, 0);
        $this->assert(is_array($products), "Returns array");
        $this->assert(count($products) > 0, "Returns products");
        $this->assert(count($products) <= 10, "Respects limit parameter");
        
        // Test: First product has required fields
        if (count($products) > 0) {
            $firstProduct = $products[0];
            $this->assertArrayHasKey('id', $firstProduct, "Product has 'id'");
            $this->assertArrayHasKey('name', $firstProduct, "Product has 'name'");
            $this->assertArrayHasKey('price', $firstProduct, "Product has 'price'");
            $this->assertArrayHasKey('category_name', $firstProduct, "Product has 'category_name'");
        }
        
        $this->summary();
    }
    
    /**
     * Test: Search products
     */
    public function testSearch()
    {
        $this->describe("Product Model - Search");
        
        // Get a sample product to search for
        $sampleProduct = $this->dbHelper->getSampleProduct();
        
        if (!$sampleProduct) {
            $this->skip("No products in database");
            return;
        }
        
        // Test: Search by product name (first 3 chars)
        $searchTerm = substr($sampleProduct['name'], 0, 3);
        $results = $this->productModel->search($searchTerm, 10);
        
        $this->assert(is_array($results), "Returns array");
        $this->assert(count($results) > 0, "Found products matching '{$searchTerm}'");
        
        // Test: Search returns matching products
        $foundMatch = false;
        foreach ($results as $product) {
            if (stripos($product['name'], $searchTerm) !== false) {
                $foundMatch = true;
                break;
            }
        }
        $this->assertTrue($foundMatch, "Search results contain matching product");
        
        // Test: Empty search term
        $results = $this->productModel->search('', 10);
        $this->assert(is_array($results), "Empty search returns array");
        
        $this->summary();
    }
    
    /**
     * Test: Get product by slug
     */
    public function testGetBySlug()
    {
        $this->describe("Product Model - Get By Slug");
        
        // Get a sample product
        $sampleProduct = $this->dbHelper->getSampleProduct();
        
        if (!$sampleProduct) {
            $this->skip("No products in database");
            return;
        }
        
        if (!isset($sampleProduct['slug']) || empty($sampleProduct['slug'])) {
            $this->skip("Sample product has no slug");
            return;
        }
        
        // Test: Valid slug
        $product = $this->productModel->getBySlug($sampleProduct['slug']);
        $this->assertNotNull($product, "Product found with valid slug");
        $this->assertEqual($sampleProduct['id'], $product['id'], "Product ID matches");
        
        // Test: Invalid slug
        $product = $this->productModel->getBySlug('invalid-slug-that-does-not-exist');
        $this->assertNull($product, "Returns null for invalid slug");
        
        $this->summary();
    }
    
    /**
     * Run all tests
     */
    public function runAll()
    {
        $this->testGetById();
        $this->testGetAll();
        $this->testSearch();
        $this->testGetBySlug();
    }
}

// Run tests if executed directly
if (basename(__FILE__) === basename($_SERVER['PHP_SELF'])) {
    $test = new ProductModelTest();
    $test->runAll();
}
