<?php
/**
 * Database Helper for Tests
 * 
 * Utilities for database operations in tests
 */

class DatabaseHelper
{
    private $pdo;
    
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    
    /**
     * Get a sample product for testing
     */
    public function getSampleProduct()
    {
        $stmt = $this->pdo->query("SELECT * FROM products LIMIT 1");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get a sample user for testing
     */
    public function getSampleUser($role = 'user')
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE role = ? LIMIT 1");
        $stmt->execute([$role]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get a sample order for testing
     */
    public function getSampleOrder()
    {
        $stmt = $this->pdo->query("SELECT * FROM orders LIMIT 1");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get an active voucher for testing
     */
    public function getActiveVoucher()
    {
        $stmt = $this->pdo->query("SELECT * FROM vouchers WHERE is_active = 1 AND (expiry_date IS NULL OR expiry_date > NOW()) LIMIT 1");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Count records in a table
     */
    public function countRecords($table, $where = null, $params = [])
    {
        $sql = "SELECT COUNT(*) as total FROM {$table}";
        if ($where) {
            $sql .= " WHERE {$where}";
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (int) $result['total'];
    }
    
    /**
     * Check if record exists
     */
    public function recordExists($table, $id)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM {$table} WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (int) $result['total'] > 0;
    }
    
    /**
     * Get latest record from table
     */
    public function getLatestRecord($table)
    {
        $stmt = $this->pdo->query("SELECT * FROM {$table} ORDER BY id DESC LIMIT 1");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Clean up test data (use with caution!)
     */
    public function cleanupTestData($table, $where, $params)
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$table} WHERE {$where}");
        return $stmt->execute($params);
    }
}
