<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once __DIR__ . '/partials/header.php'; ?>
</head>
<body>
    <?php require_once __DIR__ . '/partials/navbar.php'; ?>
    


<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">
                <i class="fas fa-chart-line text-blue-600 mr-3"></i>Analytics Dashboard
            </h1>
            <p class="text-gray-600">Comprehensive overview of your business performance</p>
        </div>

        <!-- Revenue Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Today's Revenue -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-blue-100 text-sm font-semibold uppercase">Today's Revenue</p>
                        <h3 class="text-3xl font-bold mt-2">Rp <?php echo number_format($revenueStats['today_revenue'] ?? 0, 0, ',', '.'); ?></h3>
                        <p class="text-blue-100 text-sm mt-1"><?php echo $revenueStats['today_orders'] ?? 0; ?> orders</p>
                    </div>
                    <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                        <i class="fas fa-calendar-day text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- This Week -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-green-100 text-sm font-semibold uppercase">This Week</p>
                        <h3 class="text-3xl font-bold mt-2">Rp <?php echo number_format($revenueStats['week_revenue'] ?? 0, 0, ',', '.'); ?></h3>
                        <p class="text-green-100 text-sm mt-1"><?php echo $revenueStats['week_orders'] ?? 0; ?> orders</p>
                    </div>
                    <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                        <i class="fas fa-calendar-week text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- This Month -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-purple-100 text-sm font-semibold uppercase">This Month</p>
                        <h3 class="text-3xl font-bold mt-2">Rp <?php echo number_format($revenueStats['month_revenue'] ?? 0, 0, ',', '.'); ?></h3>
                        <p class="text-purple-100 text-sm mt-1"><?php echo $revenueStats['month_orders'] ?? 0; ?> orders</p>
                    </div>
                    <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                        <i class="fas fa-calendar-alt text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Average Order Value -->
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-orange-100 text-sm font-semibold uppercase">Avg Order Value</p>
                        <h3 class="text-3xl font-bold mt-2">Rp <?php echo number_format($revenueStats['avg_order_value'] ?? 0, 0, ',', '.'); ?></h3>
                        <p class="text-orange-100 text-sm mt-1">Per transaction</p>
                    </div>
                    <div class="bg-orange-400 bg-opacity-30 rounded-full p-3">
                        <i class="fas fa-receipt text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secondary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-semibold">Products</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1"><?php echo $dashboardCounts['total_products'] ?? 0; ?></p>
                    </div>
                    <i class="fas fa-box text-blue-500 text-3xl"></i>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-semibold">Customers</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1"><?php echo $dashboardCounts['total_customers'] ?? 0; ?></p>
                    </div>
                    <i class="fas fa-users text-green-500 text-3xl"></i>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-semibold">Categories</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1"><?php echo $dashboardCounts['total_categories'] ?? 0; ?></p>
                    </div>
                    <i class="fas fa-tags text-purple-500 text-3xl"></i>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-semibold">Active Vouchers</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1"><?php echo $dashboardCounts['active_vouchers'] ?? 0; ?></p>
                    </div>
                    <i class="fas fa-ticket-alt text-orange-500 text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Sales Trend Chart -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-800"><i class="fas fa-chart-line text-blue-600 mr-2"></i>Sales Trend (Last 7 Days)</h3>
                </div>
                <canvas id="salesChart" height="100"></canvas>
            </div>

            <!-- Category Performance Chart -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4"><i class="fas fa-chart-pie text-green-600 mr-2"></i>Category Performance</h3>
                <canvas id="categoryChart" height="100"></canvas>
            </div>
        </div>

        <!-- Order Status Chart -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-4"><i class="fas fa-chart-bar text-purple-600 mr-2"></i>Order Status Distribution</h3>
            <canvas id="orderStatusChart" height="80"></canvas>
        </div>

        <!-- Tables Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top Products -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4"><i class="fas fa-trophy text-yellow-500 mr-2"></i>Top 5 Products</h3>
                <?php if (!empty($topProducts)): ?>
                    <div class="space-y-3">
                        <?php foreach ($topProducts as $index => $product): ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 flex items-center justify-center text-white font-bold text-sm">
                                        <?php echo $index + 1; ?>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800"><?php echo e($product['name'] ?? 'Product'); ?></p>
                                        <p class="text-sm text-gray-500"><?php echo $product['total_quantity'] ?? 0; ?> sold</p>
                                    </div>
                                </div>
                                <p class="font-bold text-green-600">Rp <?php echo number_format($product['total_revenue'] ?? 0, 0, ',', '.'); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 text-center py-8">No sales data yet</p>
                <?php endif; ?>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-800"><i class="fas fa-shopping-bag text-blue-600 mr-2"></i>Recent Orders</h3>
                    <a href="?route=admin.orders" class="text-blue-600 text-sm hover:underline">View All</a>
                </div>
                <?php if (!empty($recentOrders)): ?>
                    <div class="space-y-3">
                        <?php foreach (array_slice($recentOrders, 0, 5) as $order): ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div>
                                    <p class="font-semibold text-gray-800"><?php echo e($order['order_number'] ?? 'N/A'); ?></p>
                                    <p class="text-sm text-gray-500"><?php echo e($order['user_name'] ?? 'Unknown'); ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-800">Rp <?php echo number_format($order['total_price'] ?? 0, 0, ',', '.'); ?></p>
                                    <span class="text-xs px-2 py-1 rounded-full 
                                        <?php 
                                        $orderStatus = $order['order_status'] ?? 'pending';
                                        echo $orderStatus === 'delivered' ? 'bg-green-100 text-green-700' : 
                                             ($orderStatus === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700');
                                        ?>">
                                        <?php echo ucfirst($orderStatus); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 text-center py-8">No orders yet</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>

<script>
// Prepare data for charts
const dailySalesData = <?php echo json_encode($dailySales ?? []); ?>;
const categoryData = <?php echo json_encode($categoryStats ?? []); ?>;
const orderStatsData = <?php echo json_encode($orderStats ?? []); ?>;

// Sales Trend Line Chart
const salesCtx = document.getElementById('salesChart').getContext('2d');
new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: dailySalesData.map(d => d.date),
        datasets: [{
            label: 'Sales (Rp)',
            data: dailySalesData.map(d => d.total_sales),
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {display: false},
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + (value / 1000) + 'k';
                    }
                }
            }
        }
    }
});

// Category Performance Pie Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: categoryData.map(c => c.category_name),
        datasets: [{
            data: categoryData.map(c => c.total_revenue || 0),
            backgroundColor: [
                'rgba(59, 130, 246, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(251, 191, 36, 0.8)',
                'rgba(239, 68, 68, 0.8)',
                'rgba(139, 92, 246, 0.8)',
                'rgba(236, 72, 153, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': Rp ' + context.parsed.toLocaleString('id-ID');
                    }
                }
            }
        }
    }
});

// Order Status Bar Chart
const orderCtx = document.getElementById('orderStatusChart').getContext('2d');
new Chart(orderCtx, {
    type: 'bar',
    data: {
        labels: orderStatsData.map(o => o.order_status.replace('_', ' ').toUpperCase()),
        datasets: [{
            label: 'Orders',
            data: orderStatsData.map(o => o.count),
            backgroundColor: [
                'rgba(251, 191, 36, 0.8)',
                'rgba(59, 130, 246, 0.8)',
                'rgba(139, 92, 246, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(239, 68, 68, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {display: false}
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>

</body>
</html>