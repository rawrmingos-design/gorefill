<?php

require_once __DIR__ . '/../Models/Category.php';
require_once __DIR__ . '/BaseController.php';

class AdminCategoryController extends BaseController {
    private $categoryModel;

    public function __construct() {
        parent::__construct();
        $this->categoryModel = new Category($this->pdo);
        
        // Check if user is admin
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'Akses ditolak. Halaman ini hanya untuk admin.';
            header('Location: index.php?route=home');
            exit;
        }
    }

    /**
     * Display list of categories
     */
    public function index() {
        $categories = $this->categoryModel->getAllWithProductCount();
        
        $data = [
            'title' => 'Kelola Kategori',
            'categories' => $categories
        ];
        
        $this->render('admin/categories/index', $data);
    }

    /**
     * Show create category form
     */
    public function create() {
        $data = [
            'title' => 'Tambah Kategori Baru'
        ];
        
        $this->render('admin/categories/create', $data);
    }

    /**
     * Store new category
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Invalid request method';
            header('Location: index.php?route=admin.categories');
            exit;
        }

        // Validate input
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($name)) {
            $_SESSION['error'] = 'Nama kategori wajib diisi';
            header('Location: index.php?route=admin.categories.create');
            exit;
        }

        // Check if name already exists
        if ($this->categoryModel->nameExists($name)) {
            $_SESSION['error'] = 'Nama kategori sudah ada';
            header('Location: index.php?route=admin.categories.create');
            exit;
        }

        try {
            $this->categoryModel->create([
                'name' => $name,
                'description' => $description
            ]);

            $_SESSION['success'] = 'Kategori berhasil ditambahkan';
            header('Location: index.php?route=admin.categories');
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = 'Gagal menambahkan kategori: ' . $e->getMessage();
            header('Location: index.php?route=admin.categories.create');
            exit;
        }
    }

    /**
     * Show edit category form
     */
    public function edit() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $_SESSION['error'] = 'ID kategori tidak valid';
            header('Location: index.php?route=admin.categories');
            exit;
        }

        $category = $this->categoryModel->getById($id);

        if (!$category) {
            $_SESSION['error'] = 'Kategori tidak ditemukan';
            header('Location: index.php?route=admin.categories');
            exit;
        }

        $data = [
            'title' => 'Edit Kategori',
            'category' => $category
        ];

        $this->render('admin/categories/edit', $data);
    }

    /**
     * Update category
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Invalid request method';
            header('Location: index.php?route=admin.categories');
            exit;
        }

        $id = $_POST['id'] ?? null;
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (!$id || empty($name)) {
            $_SESSION['error'] = 'Data tidak lengkap';
            header('Location: index.php?route=admin.categories.edit&id=' . $id);
            exit;
        }

        // Check if name already exists (excluding current category)
        if ($this->categoryModel->nameExists($name, $id)) {
            $_SESSION['error'] = 'Nama kategori sudah digunakan oleh kategori lain';
            header('Location: index.php?route=admin.categories.edit&id=' . $id);
            exit;
        }

        try {
            $this->categoryModel->update($id, [
                'name' => $name,
                'description' => $description
            ]);

            $_SESSION['success'] = 'Kategori berhasil diupdate';
            header('Location: index.php?route=admin.categories');
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = 'Gagal mengupdate kategori: ' . $e->getMessage();
            header('Location: index.php?route=admin.categories.edit&id=' . $id);
            exit;
        }
    }

    /**
     * Delete category
     */
    public function destroy() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $id = $_POST['id'] ?? null;

        if (!$id) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'ID kategori tidak valid']);
            exit;
        }

        try {
            $this->categoryModel->delete($id);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Kategori berhasil dihapus']);
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }
}
