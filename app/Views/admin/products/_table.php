<?php require_once __DIR__ . '/../../../Helpers/ImageHelper.php';
?>
<table class="min-w-full">
  <thead class="bg-gray-50">
    <tr>
      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
      <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
    </tr>
  </thead>
  <tbody class="bg-white divide-y divide-gray-200">
    <?php if (!empty($products) && count($products) > 0): ?>
      <?php 
      $no = ($currentPage - 1) * 10 + 1;
      foreach ($products as $product): 
      ?>
      <tr class="hover:bg-gray-50">
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-semibold"><?php echo $no++; ?></td>
        <td class="px-6 py-4">
          <div class="flex items-center">
            <img src="<?php echo e(ImageHelper::getImageUrl($product['image'])); ?>" alt="<?php echo e($product['name']); ?>" class="w-12 h-12 rounded object-cover mr-3">
            <div>
              <div class="font-medium text-gray-900"><?php echo e($product['name']); ?></div>
              <?php if (!empty($product['description'])): ?>
                <div class="text-sm text-gray-500"><?php echo e(substr($product['description'], 0, 50)); ?>...</div>
              <?php endif; ?>
            </div>
          </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
          <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">
            <?php echo e($product['category_name']); ?>
          </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap font-semibold">
          Rp <?php echo number_format($product['price'], 0, ',', '.'); ?>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
          <span class="font-semibold <?php echo $product['stock'] > 10 ? 'text-green-600' : ($product['stock'] > 0 ? 'text-orange-600' : 'text-red-600'); ?>">
            <?php echo e($product['stock']); ?>
          </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
          <a href="?route=admin.products.edit&id=<?php echo e($product['id']); ?>" class="text-blue-600 hover:underline">Edit</a>
          <button onclick="deleteProduct(<?php echo e($product['id']); ?>)" class="text-red-600 hover:underline">Delete</button>
        </td>
      </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr>
        <td colspan="6" class="text-center py-6 text-gray-500">
          Tidak ada produk yang ditemukan.
        </td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>
<?php if ($totalPages > 1): ?>
  <div class="px-6 py-4 flex justify-between items-center border-t bg-gray-50">
    <div class="text-sm text-gray-600">
      Halaman <?php echo $currentPage; ?> dari <?php echo $totalPages; ?>
    </div>

    <div class="flex items-center space-x-2">
      <?php if ($currentPage > 1): ?>
        <button 
          class="px-3 py-1 border rounded hover:bg-gray-100 pagination-btn"
          data-page="<?php echo $currentPage - 1; ?>"
        >
          ‹ Sebelumnya
        </button>
      <?php endif; ?>

      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <button 
          class="px-3 py-1 border rounded <?php echo $i == $currentPage ? 'bg-blue-600 text-white' : 'hover:bg-gray-100'; ?> pagination-btn"
          data-page="<?php echo $i; ?>"
        >
          <?php echo $i; ?>
        </button>
      <?php endfor; ?>

      <?php if ($currentPage < $totalPages): ?>
        <button 
          class="px-3 py-1 border rounded hover:bg-gray-100 pagination-btn"
          data-page="<?php echo $currentPage + 1; ?>"
        >
          Selanjutnya ›
        </button>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>
