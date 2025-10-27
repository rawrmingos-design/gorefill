<?php

class Category {
    private $pdo;
    private $table = 'categories';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get all categories
     */
    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT * FROM {$this->table} 
            ORDER BY name ASC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Get category by ID
     */
    public function getById($id) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM {$this->table} 
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Get category by slug
     */
    public function getBySlug($slug) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM {$this->table} 
            WHERE slug = ?
        ");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }

    /**
     * Create new category
     */
    public function create($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO {$this->table} (name, slug, description) 
            VALUES (?, ?, ?)
        ");
        
        $slug = $this->generateSlug($data['name']);
        
        $stmt->execute([
            $data['name'],
            $slug,
            $data['description'] ?? null
        ]);
        
        return $this->pdo->lastInsertId();
    }

    /**
     * Update category
     */
    public function update($id, $data) {
        $stmt = $this->pdo->prepare("
            UPDATE {$this->table} 
            SET name = ?, 
                slug = ?, 
                description = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        
        $slug = $this->generateSlug($data['name'], $id);
        
        return $stmt->execute([
            $data['name'],
            $slug,
            $data['description'] ?? null,
            $id
        ]);
    }

    /**
     * Delete category
     */
    public function delete($id) {
        // Check if category has products
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as count FROM products 
            WHERE category_id = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        
        if ($result['count'] > 0) {
            throw new Exception("Tidak dapat menghapus kategori yang masih memiliki {$result['count']} produk");
        }
        
        $stmt = $this->pdo->prepare("
            DELETE FROM {$this->table} 
            WHERE id = ?
        ");
        return $stmt->execute([$id]);
    }

    /**
     * Check if category name exists (for validation)
     */
    public function nameExists($name, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count FROM {$this->table} 
                WHERE name = ? AND id != ?
            ");
            $stmt->execute([$name, $excludeId]);
        } else {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count FROM {$this->table} 
                WHERE name = ?
            ");
            $stmt->execute([$name]);
        }
        
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    /**
     * Get category with product count
     */
    public function getAllWithProductCount() {
        $stmt = $this->pdo->query("
            SELECT c.*, 
                   COUNT(p.id) as product_count 
            FROM {$this->table} c
            LEFT JOIN products p ON c.id = p.category_id
            GROUP BY c.id
            ORDER BY c.name ASC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Generate unique slug from name
     */
    private function generateSlug($name, $excludeId = null) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        
        // Check if slug exists
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    /**
     * Check if slug exists
     */
    private function slugExists($slug, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count FROM {$this->table} 
                WHERE slug = ? AND id != ?
            ");
            $stmt->execute([$slug, $excludeId]);
        } else {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count FROM {$this->table} 
                WHERE slug = ?
            ");
            $stmt->execute([$slug]);
        }
        
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
}
