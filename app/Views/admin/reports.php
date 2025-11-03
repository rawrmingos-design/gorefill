<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">
                <i class="fas fa-file-chart-line text-blue-600 mr-3"></i>Sales Reports
            </h1>
            <p class="text-gray-600">Detailed analytics and export functionality</p>
        </div>

        <!-- Date Range Selector & Export -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <form method="GET" action="?route=admin.reports" class="flex flex-wrap items-end gap-4">
                <input type="hidden" name="route" value="admin.reports">
                
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Start Date</label>
                    <input type="date" name="start_date" value="<?php echo $startDate; ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-bold text-gray-700 mb-2">End Date</label>
                    <input type="date" name="end_date" value="<?php echo $endDate; ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-semibold">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                </div>
                
                <div class="flex gap-2">
                    <a href="?route=admin.exportReport&type=sales&start_date=<?php echo $startDate; ?>&end_date=<?php echo $endDate; ?>" 
                       class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 font-semibold">
                        <i class="fas fa-file-csv mr-2"></i>Export Sales CSV
                    </a>
                    <a href="?route=admin.exportReport&type=products" 
                       class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 font-semibold">
                        <i class="fas fa-download mr-2"></i>Export Products CSV
                    </a>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <?php
            $totalSales = array_sum(array_column($dailySales, 'total_sales'));
            $totalOrders = array_sum(array_column($dailySales, 'order_count'));
            $avgOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;
            ?>
            
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                <p class="text-blue-100 text-sm font-semibold uppercase mb-2">Total Sales</p>
                <h3 class="text-3xl font-bold">Rp <?php echo number_format($totalSales, 0, ',', '.'); ?></h3>
                <p class="text-blue-100 text-sm mt-2">Selected period</p>
            </div>
            
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <p class="text-green-100 text-sm font-semibold uppercase mb-2">Total Orders</p>
                <h3 class="text-3xl font-bold"><?php echo number_format($totalOrders, 0, ',', '.'); ?></h3>
                <p class="text-green-100 text-sm mt-2">Transactions</p>
            </div>
            
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                <p class="text-purple-100 text-sm font-semibold uppercase mb-2">Avg Order Value</p>
                <h3 class="text-3xl font-bold">Rp <?php echo number_format($avgOrderValue, 0, ',', '.'); ?></h3>
                <p class="text-purple-100 text-sm mt-2">Per transaction</p>
            </div>
            
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
                <p class="text-orange-100 text-sm font-semibold uppercase mb-2">Active Days</p>
                <h3 class="text-3xl font-bold"><?php echo count($dailySales); ?></h3>
                <p class="text-orange-100 text-sm mt-2">With sales</p>
            </div>
        </div>

        <!-- Sales Trend Chart -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h3 class="text-2xl font-bold text-gray-800 mb-6"><i class="fas fa-chart-area text-blue-600 mr-2"></i>Daily Sales Trend</h3>
            <canvas id="salesTrendChart" height="80"></canvas>
        </div>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Category Performance -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-6"><i class="fas fa-tags text-green-600 mr-2"></i>Category Performance</h3>
                <canvas id="categoryChart" height="120"></canvas>
            </div>

            <!-- Order Status -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-6"><i class="fas fa-tasks text-purple-600 mr-2"></i>Order Status</h3>
                <div class="space-y-4">
                    <?php foreach ($orderStats as $status): ?>
                        <?php
                        $statusColors = [
                            'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'bar' => 'bg-yellow-500'],
                            'packing' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'bar' => 'bg-blue-500'],
                            'shipping' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'bar' => 'bg-purple-500'],
                            'delivered' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'bar' => 'bg-green-500'],
                            'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'bar' => 'bg-red-500']
                        ];
                        $colors = $statusColors[$status['order_status']] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'bar' => 'bg-gray-500'];
                        $percentage = $totalOrders > 0 ? ($status['count'] / $totalOrders * 100) : 0;
                        ?>
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-semibold text-gray-700 capitalize"><?php echo str_replace('_', ' ', $status['order_status']); ?></span>
                                <div class="flex items-center space-x-3">
                                    <span class="text-sm text-gray-600"><?php echo $status['count']; ?> orders</span>
                                    <span class="px-3 py-1 <?php echo $colors['bg']; ?> <?php echo $colors['text']; ?> rounded-full text-sm font-bold">
                                        <?php echo number_format($percentage, 1); ?>%
                                    </span>
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="<?php echo $colors['bar']; ?> h-3 rounded-full transition-all" style="width: <?php echo $percentage; ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Top Products Table -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-2xl font-bold text-gray-800 mb-6"><i class="fas fa-trophy text-yellow-500 mr-2"></i>Top 20 Best Selling Products</h3>
            <?php if (!empty($topProducts)): ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-bold text-gray-700">#</th>
                                <th class="px-4 py-3 text-left text-sm font-bold text-gray-700">Product</th>
                                <th class="px-4 py-3 text-center text-sm font-bold text-gray-700">Orders</th>
                                <th class="px-4 py-3 text-center text-sm font-bold text-gray-700">Qty Sold</th>
                                <th class="px-4 py-3 text-right text-sm font-bold text-gray-700">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($topProducts as $index => $product): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <span class="w-8 h-8 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 flex items-center justify-center text-white font-bold text-sm inline-block">
                                            <?php echo $index + 1; ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-gray-800"><?php echo e($product['name']); ?></td>
                                    <td class="px-4 py-3 text-center text-gray-700"><?php echo $product['order_count']; ?></td>
                                    <td class="px-4 py-3 text-center font-bold text-blue-600"><?php echo $product['total_quantity']; ?></td>
                                    <td class="px-4 py-3 text-right font-bold text-green-600">Rp <?php echo number_format($product['total_revenue'] ?? 0, 0, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-500 text-center py-8">No product sales data available</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Sales Trend Chart
const salesData = <?php echo json_encode($dailySales ?? []); ?>;
const salesCtx = document.getElementById('salesTrendChart').getContext('2d');
new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: salesData.map(d => d.date),
        datasets: [
            {
                label: 'Sales (Rp)',
                data: salesData.map(d => d.total_sales),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                yAxisID: 'y',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Orders',
                data: salesData.map(d => d.order_count),
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                yAxisID: 'y1',
                tension: 0.4,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        interaction: {
            mode: 'index',
            intersect: false
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Sales (Rp)'
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Orders'
                },
                grid: {
                    drawOnChartArea: false
                }
            }
        }
    }
});

// Category Chart
const categoryData = <?php echo json_encode($categoryStats ?? []); ?>;
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
        plugins: {
            legend: {position: 'bottom'},
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const revenue = context.parsed;
                        const quantity = categoryData[context.dataIndex].total_quantity;
                        return context.label + ': Rp ' + revenue.toLocaleString('id-ID') + ' (' + quantity + ' sold)';
                    }
                }
            }
        }
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
