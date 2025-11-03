# 📊 Product Rating Implementation Notes

## ✅ IMPLEMENTASI LENGKAP

### **Konsep:**
Table `products` memiliki kolom `rating` (float) yang menyimpan average rating untuk performa lebih baik.

---

## 🔧 CARA KERJA:

### **1. Auto-Update Rating**

Setiap kali user submit review baru, rating di table `products` otomatis di-update:

```php
// Flow:
User submit review
    ↓
ProductReview->create() - Insert ke product_reviews
    ↓
Product->updateRating() - Calculate AVG & update products.rating
    ↓
Rating tersimpan di products.rating
```

### **2. Product Model Method**

**File:** `app/Models/Product.php`

```php
/**
 * Update product rating based on reviews
 * Auto-calculates average from product_reviews table
 */
public function updateRating($productId)
{
    // Calculate AVG(rating) from product_reviews
    $stmt = $this->pdo->prepare("
        SELECT ROUND(AVG(rating), 1) as avg_rating
        FROM product_reviews
        WHERE product_id = :product_id
    ");
    $stmt->execute(['product_id' => $productId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $avgRating = $result['avg_rating'] ?? 0;
    
    // Update products.rating
    $updateStmt = $this->pdo->prepare("
        UPDATE products 
        SET rating = :rating 
        WHERE id = :id
    ");
    
    return $updateStmt->execute([
        'rating' => $avgRating,
        'id' => $productId
    ]);
}
```

### **3. Controller Integration**

**File:** `app/Controllers/ProductController.php`

```php
// Create review
$reviewId = $this->reviewModel->create($productId, $userId, $rating, $review);

if ($reviewId) {
    // ✅ Auto-update products.rating
    $this->productModel->updateRating($productId);
    
    // Get updated data
    $reviewData = $this->reviewModel->getAverageRating($productId);
    
    return json(['success' => true, ...]);
}
```

---

## 📊 DATABASE STRUKTUR:

### **Table: products**
```sql
CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `rating` float DEFAULT '0',  ← Pre-calculated average rating
  ...
) ENGINE=InnoDB;
```

### **Table: product_reviews**
```sql
CREATE TABLE `product_reviews` (
  `id` int NOT NULL,
  `product_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `rating` int NOT NULL,  ← Individual review rating (1-5)
  `review` text NOT NULL,
  ...
) ENGINE=InnoDB;
```

---

## 🚀 KEUNTUNGAN:

### **1. Performance Optimization**
```php
// ❌ SLOW: Calculate rating untuk setiap product
foreach ($products as $product) {
    $rating = AVG(product_reviews.rating WHERE product_id = $product['id']);
}

// ✅ FAST: Langsung baca dari products.rating
foreach ($products as $product) {
    $rating = $product['rating']; // Sudah pre-calculated!
}
```

### **2. Sorting Products by Rating**
```sql
-- Bisa langsung sort by rating tanpa JOIN atau subquery
SELECT * FROM products 
ORDER BY rating DESC 
LIMIT 10;

-- Top rated products:
SELECT * FROM products 
WHERE rating >= 4.5 
ORDER BY rating DESC;
```

### **3. Fast Filtering**
```sql
-- Filter products dengan rating tinggi
SELECT * FROM products 
WHERE rating >= 4.0 AND stock > 0
ORDER BY rating DESC;
```

---

## 📝 EXAMPLE QUERIES:

### **Get Top Rated Products:**
```php
public function getTopRated($limit = 10)
{
    $stmt = $this->pdo->prepare("
        SELECT p.*, c.name as category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.rating >= 4.0
        ORDER BY p.rating DESC, p.id DESC
        LIMIT :limit
    ");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
```

### **Sort Products by Rating:**
```php
// In ProductController->index()
if ($sort === 'rating') {
    $products = $this->productModel->getAll(
        $category, 
        $limit, 
        $offset, 
        'rating',  // Sort by rating column
        'desc'     // Highest first
    );
}
```

---

## 🔄 MANUAL UPDATE (Jika Diperlukan):

Jika data rating sudah ada sebelumnya dan perlu update manual:

```sql
-- Update semua product ratings dari review data
UPDATE products p
SET rating = (
    SELECT ROUND(AVG(rating), 1)
    FROM product_reviews pr
    WHERE pr.product_id = p.id
)
WHERE EXISTS (
    SELECT 1 FROM product_reviews pr2
    WHERE pr2.product_id = p.id
);

-- Set rating = 0 untuk products tanpa review
UPDATE products 
SET rating = 0 
WHERE id NOT IN (SELECT DISTINCT product_id FROM product_reviews);
```

---

## ✅ TESTING:

### **Test 1: Auto-Update Rating**
```
1. Product A rating = 0 (no reviews)
2. User 1 submit review: 5 stars
3. ✅ products.rating = 5.0
4. User 2 submit review: 4 stars
5. ✅ products.rating = 4.5 (average of 5 and 4)
6. User 3 submit review: 3 stars
7. ✅ products.rating = 4.0 (average of 5, 4, 3)
```

### **Test 2: Display Rating**
```sql
-- Check product rating
SELECT id, name, rating FROM products WHERE id = 1;
-- Result: id=1, name='Product A', rating=4.0
```

### **Test 3: Sort by Rating**
```php
// Get products sorted by rating
$products = $productModel->getAll(null, 10, 0, 'rating', 'desc');

// Expected: Products with highest rating first
// Product B (4.8) → Product A (4.5) → Product C (4.2) → etc.
```

---

## 🎯 SUMMARY:

**Sebelum:**
- ❌ Rating dihitung realtime dari product_reviews setiap kali dibutuhkan
- ❌ Slow query (JOIN + AVG calculation)
- ❌ Sulit untuk sort/filter by rating

**Sesudah:**
- ✅ Rating pre-calculated di products.rating
- ✅ Fast query (langsung SELECT rating)
- ✅ Auto-update setiap ada review baru
- ✅ Mudah sort/filter by rating
- ✅ Performance optimal

---

**Status:** ✅ IMPLEMENTED & WORKING
**Date:** October 28, 2025

Sekarang kolom `rating` di table `products` akan otomatis ter-update setiap kali ada review baru! 🚀
