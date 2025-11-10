<?php
/**
 * GoRefill Test Runner
 * 
 * Runs all test suites and displays summary
 * Usage: php tests/run-all-tests.php
 */

echo "\n";
echo "========================================\n";
echo "  🧪 GoRefill Testing Suite\n";
echo "========================================\n";
echo "Starting tests...\n\n";

$startTime = microtime(true);
$allResults = [];

/**
 * Run a test file
 */
function runTest($file, $name) {
    global $allResults;
    
    if (!file_exists($file)) {
        echo "⚠️  SKIP: {$name} (file not found)\n";
        return;
    }
    
    echo "▶️  Running: {$name}...\n";
    
    ob_start();
    include $file;
    $output = ob_get_clean();
    
    echo $output;
    
    // Try to extract results (if test class provides getResults method)
    // This is a simple approach - tests will output their own summaries
}

// ========================================
// UNIT TESTS
// ========================================
echo "\n📦 UNIT TESTS\n";
echo "========================================\n";

runTest(__DIR__ . '/unit/ProductModelTest.php', 'Product Model Test');
runTest(__DIR__ . '/unit/VoucherModelTest.php', 'Voucher Model Test');
runTest(__DIR__ . '/unit/MailServiceTest.php', 'Mail Service Test');

// ========================================
// INTEGRATION TESTS
// ========================================
echo "\n🔗 INTEGRATION TESTS\n";
echo "========================================\n";

runTest(__DIR__ . '/integration/CheckoutFlowTest.php', 'Checkout Flow Test');

// ========================================
// API TESTS
// ========================================
echo "\n🔌 API TESTS\n";
echo "========================================\n";

runTest(__DIR__ . '/api/CartApiTest.php', 'Cart API Test');

// ========================================
// SUMMARY
// ========================================
$endTime = microtime(true);
$duration = round($endTime - $startTime, 2);

echo "\n";
echo "========================================\n";
echo "  📊 TEST SUMMARY\n";
echo "========================================\n";
echo "Total Duration: {$duration}s\n";
echo "Status: ✅ All tests completed\n";
echo "========================================\n\n";

echo "💡 TIP: Run individual tests with:\n";
echo "   php tests/unit/ProductModelTest.php\n";
echo "   php tests/integration/CheckoutFlowTest.php\n\n";

echo "📋 For manual testing checklist, see:\n";
echo "   tests/manual-checklist.md\n\n";
