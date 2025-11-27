<?php
/**
 * Base Controller for GoRefill Application
 * 
 * This abstract class provides common functionality for all controllers.
 * All controllers should extend this class to inherit its methods.
 * 
 * Methods:
 * - render() - Load and display views with data
 * - redirect() - Redirect to another route
 * - json() - Return JSON responses
 * - requireAuth() - Require user authentication
 * - back() - Redirect back to previous page
 * - flash() - Set flash messages
 * - validate() - Input validation helper
 */

abstract class BaseController
{
    /**
     * Global configuration
     * @var array
     */
    protected $config;
    
    /**
     * PDO database connection
     * @var PDO
     */
    protected $pdo;
    
    /**
     * Constructor
     * Initialize config and database connection
     */
    public function __construct()
    {
        global $config, $pdo;
        $this->config = $config;
        $this->pdo = $pdo;
    }
    
    /**
     * Render a view with data
     * 
     * @param string $view View file path (relative to app/Views/)
     * @param array $data Data to pass to view
     * @return void
     */
    protected function render($view, $data = [])
    {
        // Extract data array to variables
        extract($data);
        
        // Make config and pdo available in views
        $config = $this->config;
        $pdo = $this->pdo;
        
        // Build view file path
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';
        
        // Check if view exists
        if (!file_exists($viewFile)) {
            throw new Exception("View not found: $view");
        }
        
        // Include view file
        require_once $viewFile;
    }
    
    /**
     * Redirect to a route
     * 
     * @param string $route Route path (e.g., 'auth.login', 'products')
     * @param array $params Query parameters
     * @return void
     */
    protected function redirect($route, $params = [])
    {
        $url = 'index.php?route=' . $route;
        
        // Add query parameters
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $url .= '&' . urlencode($key) . '=' . urlencode($value);
            }
        }
        
        header('Location: ' . $url);
        exit;
    }
    
    /**
     * Return JSON response
     * 
     * @param mixed $data Data to encode as JSON
     * @param int $status HTTP status code
     * @return void
     */
    protected function json($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Require user authentication
     * Redirects to login if not authenticated
     * Optionally check for specific role
     * 
     * @param string|null $role Required role (admin, user, kurir)
     * @return void
     */
    protected function requireAuth($role = null)
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            $this->redirect('auth.login');
        }
        
        // Check role if specified
        if ($role !== null) {
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
                $this->json([
                    'error' => 'Access Denied',
                    'message' => 'You do not have permission to access this resource.'
                ], 403);
            }
        }
    }
    
    /**
     * Redirect back to previous page
     * 
     * @param string $default Default route if no referer
     * @return void
     */
    protected function back($default = 'home')
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? null;
        
        if ($referer) {
            header('Location: ' . $referer);
        } else {
            $this->redirect($default);
        }
        exit;
    }
    
    /**
     * Set flash message for next request
     * 
     * @param string $key Message key (success, error, warning, info)
     * @param string $message Message content
     * @return void
     */
    protected function flash($key, $message)
    {
        $_SESSION['flash'][$key] = $message;
    }
    
    /**
     * Get flash message and remove it
     * 
     * @param string $key Message key
     * @return string|null Message or null if not found
     */
    protected function getFlash($key)
    {
        $message = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    
    /**
     * Validate input data
     * 
     * @param array $data Data to validate
     * @param array $rules Validation rules
     * @return array Errors array (empty if valid)
     */
    protected function validate($data, $rules)
    {
        $errors = [];
        
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            $rulesArray = explode('|', $fieldRules);
            
            foreach ($rulesArray as $rule) {
                // Required field
                if ($rule === 'required' && empty($value)) {
                    $errors[$field] = ucfirst($field) . ' is required';
                    break;
                }
                
                // Email validation
                if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = 'Invalid email format';
                    break;
                }
                
                // Minimum length
                if (strpos($rule, 'min:') === 0) {
                    $min = (int) substr($rule, 4);
                    if (strlen($value) < $min) {
                        $errors[$field] = ucfirst($field) . " must be at least $min characters";
                        break;
                    }
                }
                
                // Maximum length
                if (strpos($rule, 'max:') === 0) {
                    $max = (int) substr($rule, 4);
                    if (strlen($value) > $max) {
                        $errors[$field] = ucfirst($field) . " must not exceed $max characters";
                        break;
                    }
                }
                
                // Numeric validation
                if ($rule === 'numeric' && !is_numeric($value)) {
                    $errors[$field] = ucfirst($field) . ' must be a number';
                    break;
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Get current logged in user
     * 
     * @return array|null User data or null
     */
    protected function currentUser()
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        
        $stmt = $this->pdo->prepare("SELECT id, name, email, phone, role, created_at FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
    
    /**
     * Check if user has specific role
     * 
     * @param string $role Role to check
     * @return bool
     */
    protected function hasRole($role)
    {
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }
    
    /**
     * Verify CSRF token
     * 
     * @param string|null $token Token to verify
     * @return bool
     */
    protected function verifyCsrf($token = null)
    {
        $token = $token ?? ($_POST['csrf_token'] ?? null);
        
        if (!$token || !isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Upload file
     * 
     * @param array $file $_FILES array element
     * @param string $destination Upload directory
     * @param array $allowedTypes Allowed MIME types
     * @return string|false Filename or false on error
     */
    protected function uploadFile($file, $destination, $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'])
    {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }
        
        // Validate file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            return false;
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        
        // Create destination directory if not exists
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $destination . $filename)) {
            return $filename;
        }
        
        return false;
    }
    
    protected function logToFile($channel, $message, array $context = [])
    {
        $date = date('Y-m-d H:i:s');
        $logDir = __DIR__ . '/../../storage/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        $logFile = $logDir . '/midtrans.log';
        $line = '[' . $date . '] ' . $channel . ' - ' . $message;
        if (!empty($context)) {
            $line .= ' ' . json_encode($context);
        }
        var_dump($message);
        $line .= PHP_EOL;
        @file_put_contents($logFile, $line, FILE_APPEND);
    }
}
