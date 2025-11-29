<?php
/**
 * Analytics Model
 * Provides statistical data for admin dashboard
 */

class Analytics
{
    private $pdo;
    
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    
    /**
     * Get daily sales within date range
     * 
     * @param string $startDate Start date (Y-m-d)
     * @param string $endDate End date (Y-m-d)
     * @return array Sales data by date
     */
    public function getDailySales($startDate, $endDate)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    DATE(created_at) as date,
                    COUNT(*) as order_count,
                    SUM(total) as total_sales,
                    AVG(total) as avg_order_value
                FROM orders
                WHERE DATE(created_at) BETWEEN :start_date AND :end_date
                AND payment_status = 'paid'
                GROUP BY DATE(created_at)
                ORDER BY date ASC
            ");
            $stmt->execute([
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get daily sales error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get top selling products
     * 
     * @param int $limit Number of products to return
     * @return array Top products with sales count
     */
    public function getTopProducts($limit = 10)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    p.id,
                    p.name,
                    p.image,
                    p.price,
                    COUNT(oi.id) as order_count,
                    SUM(oi.quantity) as total_quantity,
                    SUM(oi.quantity * oi.price) as total_revenue
                FROM products p
                INNER JOIN order_items oi ON p.id = oi.product_id
                INNER JOIN orders o ON oi.order_id = o.id
                WHERE o.payment_status = 'paid'
                GROUP BY p.id, p.name, p.image, p.price
                ORDER BY total_quantity DESC
                LIMIT :limit
            ");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get top products error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get sales statistics by category
     * 
     * @return array Category performance data
     */
    public function getCategoryStats()
    {
        try {
            $stmt = $this->pdo->query("
                SELECT 
                    c.id,
                    c.name as category_name,
                    COUNT(DISTINCT oi.order_id) as order_count,
                    SUM(oi.quantity) as total_quantity,
                    SUM(oi.quantity * oi.price) as total_revenue
                FROM categories c
                LEFT JOIN products p ON c.id = p.category_id
                LEFT JOIN order_items oi ON p.id = oi.product_id
                LEFT JOIN orders o ON oi.order_id = o.id AND o.payment_status = 'paid'
                GROUP BY c.id, c.name
                ORDER BY total_revenue DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get category stats error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get user registration statistics
     * 
     * @param int $days Number of days to look back
     * @return array User registration data
     */
    public function getUserStats($days = 30)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    DATE(created_at) as date,
                    COUNT(*) as new_users,
                    SUM(COUNT(*)) OVER (ORDER BY DATE(created_at)) as cumulative_users
                FROM users
                WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                GROUP BY DATE(created_at)
                ORDER BY date ASC
            ");
            $stmt->execute(['days' => $days]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get user stats error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get order statistics by status
     * 
     * @return array Order counts by status
     */
    public function getOrderStats()
    {
        try {
            $stmt = $this->pdo->query("
                SELECT 
                    status AS order_status,
                    COUNT(*) as count,
                    SUM(total) as total_value
                FROM orders
                GROUP BY status
                ORDER BY count DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get order stats error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get comprehensive revenue statistics
     * 
     * @return array Revenue metrics
     */
    public function getRevenueStats()
    {
        try {
            $stmt = $this->pdo->query("
                SELECT 
                    COUNT(*) as total_orders,
                    SUM(total) as total_revenue,
                    AVG(total) as avg_order_value,
                    MAX(total) as highest_order,
                    MIN(total) as lowest_order,
                    SUM(CASE WHEN payment_status = 'paid' THEN total ELSE 0 END) as paid_revenue,
                    SUM(CASE WHEN payment_status = 'pending' THEN total ELSE 0 END) as pending_revenue
                FROM orders
            ");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Get today's sales
            $todayStmt = $this->pdo->query("
                SELECT 
                    COUNT(*) as today_orders,
                    COALESCE(SUM(total), 0) as today_revenue
                FROM orders
                WHERE DATE(created_at) = CURDATE()
                AND payment_status = 'paid'
            ");
            $todayData = $todayStmt->fetch(PDO::FETCH_ASSOC);
            
            // Get this week's sales
            $weekStmt = $this->pdo->query("
                SELECT 
                    COUNT(*) as week_orders,
                    COALESCE(SUM(total), 0) as week_revenue
                FROM orders
                WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)
                AND payment_status = 'paid'
            ");
            $weekData = $weekStmt->fetch(PDO::FETCH_ASSOC);
            
            // Get this month's sales
            $monthStmt = $this->pdo->query("
                SELECT 
                    COUNT(*) as month_orders,
                    COALESCE(SUM(total), 0) as month_revenue
                FROM orders
                WHERE YEAR(created_at) = YEAR(CURDATE())
                AND MONTH(created_at) = MONTH(CURDATE())
                AND payment_status = 'paid'
            ");
            $monthData = $monthStmt->fetch(PDO::FETCH_ASSOC);
            
            return array_merge($result, $todayData, $weekData, $monthData);
        } catch (PDOException $e) {
            error_log("Get revenue stats error: " . $e->getMessage());
            return [
                'total_orders' => 0,
                'total_revenue' => 0,
                'avg_order_value' => 0,
                'today_orders' => 0,
                'today_revenue' => 0,
                'week_orders' => 0,
                'week_revenue' => 0,
                'month_orders' => 0,
                'month_revenue' => 0
            ];
        }
    }
    
    /**
     * Get recent orders
     * 
     * @param int $limit Number of orders to return
     * @return array Recent orders with user info
     */
    public function getRecentOrders($limit = 10)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    o.*,
                    u.name as user_name,
                    u.email as user_email
                FROM orders o
                INNER JOIN users u ON o.user_id = u.id
                ORDER BY o.created_at DESC
                LIMIT :limit
            ");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get recent orders error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get total counts for dashboard cards
     * 
     * @return array Counts for products, users, categories
     */
    public function getDashboardCounts()
    {
        try {
            $stmt = $this->pdo->query("
                SELECT 
                    (SELECT COUNT(*) FROM products) as total_products,
                    (SELECT COUNT(*) FROM users WHERE role = 'customer') as total_customers,
                    (SELECT COUNT(*) FROM categories) as total_categories,
                    (SELECT COUNT(*) FROM vouchers WHERE expires_at IS NULL OR expires_at >= CURDATE()) as active_vouchers,
                    (SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE()) as new_users_today
            ");
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get dashboard counts error: " . $e->getMessage());
            return [
                'total_products' => 0,
                'total_customers' => 0,
                'total_categories' => 0,
                'active_vouchers' => 0,
                'new_users_today' => 0
            ];
        }
    }
    
    /**
     * Export sales data to CSV format
     * 
     * @param string $startDate Start date
     * @param string $endDate End date
     * @return string CSV content
     */
    public function exportSalesCSV($startDate, $endDate)
    {
        $salesData = $this->getDailySales($startDate, $endDate);
        
        $csv = "Date,Orders,Total Sales,Average Order Value\n";
        foreach ($salesData as $row) {
            $csv .= sprintf(
                "%s,%d,%s,%s\n",
                $row['date'],
                $row['order_count'],
                number_format($row['total_sales'], 2),
                number_format($row['avg_order_value'], 2)
            );
        }
        
        return $csv;
    }
    
    /**
     * Export product performance to CSV
     * 
     * @param int $limit Number of products
     * @return string CSV content
     */
    public function exportProductsCSV($limit = 100)
    {
        $products = $this->getTopProducts($limit);
        
        $csv = "Product ID,Product Name,Orders,Quantity Sold,Total Revenue\n";
        foreach ($products as $row) {
            $csv .= sprintf(
                "%d,%s,%d,%d,%s\n",
                $row['id'],
                str_replace(',', ';', $row['name']),
                $row['order_count'],
                $row['total_quantity'],
                number_format($row['total_revenue'], 2)
            );
        }
        
        return $csv;
    }
}
