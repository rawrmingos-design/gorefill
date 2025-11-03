# 🌱 Seed Data: Produk Refill - COMPLETE

## ✅ SQL FILE CREATED

**File:** `migrations/seed_refill_products.sql`

Total: **22 Produk Refill Ramah Lingkungan**

### **Cara Menjalankan:**
```bash
# Via MySQL command line
mysql -u root -p gorefill < migrations/seed_refill_products.sql

# Atau via phpMyAdmin
# 1. Buka phpMyAdmin
# 2. Pilih database 'gorefill'
# 3. Klik tab SQL
# 4. Copy-paste isi file seed_refill_products.sql
# 5. Klik Go
```

---

## 📦 KATEGORI PRODUK

### **1. Personal Care (Perawatan Pribadi) - 6 Produk**
1. ✅ Refill Shampoo Herbal 500ml - Rp 25,000
2. ✅ Refill Sabun Cair Antiseptik 1L - Rp 30,000
3. ✅ Refill Hand Sanitizer 500ml - Rp 20,000
4. ✅ Refill Face Cleanser 250ml - Rp 35,000
5. ✅ Refill Body Lotion 500ml - Rp 40,000
6. ✅ Refill Conditioner 500ml - Rp 28,000

### **2. Home Cleaning (Pembersih Rumah Tangga) - 6 Produk**
1. ✅ Refill Detergen Cair 1L - Rp 35,000
2. ✅ Refill Sabun Cuci Piring 1L - Rp 18,000
3. ✅ Refill Pelicin Pakaian 1L - Rp 22,000
4. ✅ Refill Pewangi Pakaian 500ml - Rp 15,000
5. ✅ Refill Pembersih Lantai 1L - Rp 25,000
6. ✅ Refill Cairan Pembersih Kaca 500ml - Rp 18,000

### **3. Bahan Makanan & Dapur - 8 Produk**
1. ✅ Refill Minyak Goreng 1L - Rp 18,000
2. ✅ Refill Beras Premium 5Kg - Rp 75,000
3. ✅ Refill Tepung Terigu 1Kg - Rp 12,000
4. ✅ Refill Gula Pasir 1Kg - Rp 15,000
5. ✅ Refill Kopi Bubuk 250g - Rp 25,000
6. ✅ Refill Teh Celup 100pcs - Rp 20,000
7. ✅ Refill Kacang Tanah 500g - Rp 18,000
8. ✅ Refill Pasta Macaroni 500g - Rp 22,000

### **4. Air Minum - 2 Produk**
1. ✅ Refill Galon Air Mineral 19L - Rp 12,000
2. ✅ Refill Air RO 19L - Rp 15,000

---

## 🌿 ECO-FRIENDLY BADGE

**Semua produk memiliki `badge_env = 1`** (Ramah Lingkungan)

### **Badge Display:**

**Product Card:**
```
┌─────────────────────────┐
│     Product Image       │
└─────────────────────────┘
🏷️ Category  🍃 Ramah Lingkungan
              ↑ Green badge with pulse animation
Product Name
⭐⭐⭐⭐⭐
Rp 25,000
```

**Product Detail:**
```
🏷️ Category  🍃 Ramah Lingkungan
              ↑ Animated green gradient badge

Product Name
⭐⭐⭐⭐☆ (4.5 • 25 reviews)
```

---

## 🎨 BADGE STYLING

```html
<!-- Green Eco Badge -->
<span class="px-3 py-1 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-full font-semibold animate-pulse">
    <i class="fas fa-leaf mr-1"></i> Ramah Lingkungan
</span>
```

**Features:**
- ✅ Green gradient (from-green-500 to-green-600)
- ✅ Leaf icon (fas fa-leaf)
- ✅ Pulse animation
- ✅ Rounded full
- ✅ White text

---

## 📊 DATABASE CHANGES

### **Actions Performed:**

1. ✅ **DELETE** all existing products
   ```sql
   DELETE FROM products;
   ```

2. ✅ **INSERT** 22 new refill products
   ```sql
   INSERT INTO products (name, slug, category_id, price, stock, rating, badge_env, description, image)
   VALUES (...);
   ```

3. ✅ **All products** have `badge_env = 1`

### **Column: badge_env**
```sql
badge_env TINYINT(1) DEFAULT 0
-- 0 = Regular product
-- 1 = Eco-friendly product (shows green badge)
```

---

## 🔍 VERIFY DATA

```sql
-- Check all products
SELECT id, name, badge_env FROM products;

-- Count eco-friendly products
SELECT COUNT(*) FROM products WHERE badge_env = 1;
-- Result: 22

-- Get products by category
SELECT name, category_id, badge_env FROM products ORDER BY category_id;
```

---

## 💚 ENVIRONMENTAL IMPACT

**Manfaat Produk Refill:**
- ♻️ Mengurangi sampah plastik kemasan
- 🌍 Lebih ramah lingkungan
- 💰 Lebih ekonomis (harga per unit lebih murah)
- 🌱 Mendukung gaya hidup sustainable
- 🔄 Wadah dapat digunakan berulang kali

---

## 🚀 NEXT STEPS

User sekarang bisa:
1. ✅ Browse 22 produk refill
2. ✅ Lihat badge hijau "Ramah Lingkungan"
3. ✅ Filter by kategori
4. ✅ Add to cart & checkout
5. ✅ Give reviews & ratings

---

**Status:** ✅ COMPLETE
**Date:** October 28, 2025
**Total Products:** 22 Refill Products
**Eco Badge:** All products (100%)

🌱 **GoRefill - Belanja Ramah Lingkungan!** 🌱
