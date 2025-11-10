<?php
/**
 * Test Helper Base Class
 * 
 * Simple testing framework without external dependencies
 * Provides assertion methods and colored output
 */

class TestHelper
{
    protected $testName = '';
    protected $passed = 0;
    protected $failed = 0;
    protected $startTime;
    protected $pdo;
    
    public function __construct()
    {
        $this->startTime = microtime(true);
        $this->setupDatabase();
    }
    
    /**
     * Setup database connection
     */
    protected function setupDatabase()
    {
        try {
            $config = require __DIR__ . '/../../config/config.php';
            $this->pdo = new PDO(
                "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4",
                $config['db_user'],
                $config['db_pass'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            $this->error("Database connection failed: " . $e->getMessage());
            exit(1);
        }
    }
    
    /**
     * Start test description
     */
    protected function describe($testName)
    {
        $this->testName = $testName;
        $this->passed = 0;
        $this->failed = 0;
        
        echo "\n";
        echo $this->colorize("========================================", 'cyan');
        echo "\n  TEST: " . $this->colorize($testName, 'bold');
        echo "\n";
        echo $this->colorize("========================================", 'cyan');
        echo "\n";
    }
    
    /**
     * Assert condition is true
     */
    protected function assert($condition, $message)
    {
        if ($condition) {
            $this->passed++;
            echo $this->colorize("✅ PASS: ", 'green') . $message . "\n";
            return true;
        } else {
            $this->failed++;
            echo $this->colorize("❌ FAIL: ", 'red') . $message . "\n";
            return false;
        }
    }
    
    /**
     * Assert two values are equal
     */
    protected function assertEqual($expected, $actual, $message)
    {
        $condition = $expected === $actual;
        if (!$condition) {
            $message .= " (Expected: " . var_export($expected, true) . ", Got: " . var_export($actual, true) . ")";
        }
        return $this->assert($condition, $message);
    }
    
    /**
     * Assert value is not null
     */
    protected function assertNotNull($value, $message)
    {
        return $this->assert($value !== null, $message);
    }
    
    /**
     * Assert value is null
     */
    protected function assertNull($value, $message)
    {
        return $this->assert($value === null, $message);
    }
    
    /**
     * Assert array has key
     */
    protected function assertArrayHasKey($key, $array, $message)
    {
        return $this->assert(isset($array[$key]), $message);
    }
    
    /**
     * Assert count
     */
    protected function assertCount($expected, $array, $message)
    {
        $actual = is_array($array) ? count($array) : 0;
        return $this->assertEqual($expected, $actual, $message);
    }
    
    /**
     * Assert value is true
     */
    protected function assertTrue($value, $message)
    {
        return $this->assert($value === true, $message);
    }
    
    /**
     * Assert value is false
     */
    protected function assertFalse($value, $message)
    {
        return $this->assert($value === false, $message);
    }
    
    /**
     * Print test summary
     */
    protected function summary()
    {
        $endTime = microtime(true);
        $duration = round($endTime - $this->startTime, 2);
        $total = $this->passed + $this->failed;
        
        echo $this->colorize("----------------------------------------", 'cyan');
        echo "\n";
        
        if ($this->failed === 0) {
            echo $this->colorize("Result: {$this->passed}/{$total} tests passed", 'green');
        } else {
            echo $this->colorize("Result: {$this->passed}/{$total} tests passed, {$this->failed} failed", 'red');
        }
        
        echo "\n";
        echo "Time: {$duration}s\n";
        echo $this->colorize("========================================", 'cyan');
        echo "\n\n";
        
        return $this->failed === 0;
    }
    
    /**
     * Log info message
     */
    protected function info($message)
    {
        echo $this->colorize("ℹ️  INFO: ", 'blue') . $message . "\n";
    }
    
    /**
     * Log warning message
     */
    protected function warning($message)
    {
        echo $this->colorize("⚠️  WARN: ", 'yellow') . $message . "\n";
    }
    
    /**
     * Log error message
     */
    protected function error($message)
    {
        echo $this->colorize("❌ ERROR: ", 'red') . $message . "\n";
    }
    
    /**
     * Colorize output for terminal
     */
    protected function colorize($text, $color)
    {
        $colors = [
            'red' => "\033[31m",
            'green' => "\033[32m",
            'yellow' => "\033[33m",
            'blue' => "\033[34m",
            'cyan' => "\033[36m",
            'bold' => "\033[1m",
            'reset' => "\033[0m"
        ];
        
        $colorCode = $colors[$color] ?? '';
        $resetCode = $colors['reset'];
        
        return $colorCode . $text . $resetCode;
    }
    
    /**
     * Skip test with message
     */
    protected function skip($message)
    {
        echo $this->colorize("⏭️  SKIP: ", 'yellow') . $message . "\n";
    }
    
    /**
     * Get total results
     */
    public function getResults()
    {
        return [
            'passed' => $this->passed,
            'failed' => $this->failed,
            'total' => $this->passed + $this->failed
        ];
    }
}
