    
    // ==================== ANALYTICS & REPORTS (Week 4 Day 18) ====================
    
    /**
     * Detailed reports page with date range selector
     * GET /admin/reports
     */
    public function reports()
    {
        $this->requireAuth('admin');
        
        // Get date range from query params or default to last 30 days
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        
        // Get analytics data
        $dailySales = $this->analyticsModel->getDailySales($startDate, $endDate);
        $topProducts = $this->analyticsModel->getTopProducts(20);
        $categoryStats = $this->analyticsModel->getCategoryStats();
        $revenueStats = $this->analyticsModel->getRevenueStats();
        $orderStats = $this->analyticsModel->getOrderStats();
        
        $this->render('admin/reports', [
            'title' => 'Sales Reports - Admin',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dailySales' => $dailySales,
            'topProducts' => $topProducts,
            'categoryStats' => $categoryStats,
            'revenueStats' => $revenueStats,
            'orderStats' => $orderStats
        ]);
    }
    
    /**
     * Export sales report to CSV
     * GET /admin/reports/export?type=sales&start_date=X&end_date=Y
     */
    public function exportReport()
    {
        $this->requireAuth('admin');
        
        $type = $_GET['type'] ?? 'sales';
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        
        if ($type === 'sales') {
            $csv = $this->analyticsModel->exportSalesCSV($startDate, $endDate);
            $filename = "sales_report_{$startDate}_to_{$endDate}.csv";
        } elseif ($type === 'products') {
            $csv = $this->analyticsModel->exportProductsCSV(100);
            $filename = "products_report_" . date('Y-m-d') . ".csv";
        } else {
            $_SESSION['error'] = 'Invalid export type';
            header('Location: ?route=admin.reports');
            exit;
        }
        
        // Send CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        echo $csv;
        exit;
    }
}
