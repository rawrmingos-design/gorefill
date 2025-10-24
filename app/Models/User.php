<?php
/**
 * User Model for GoRefill Application
 * 
 * Handles all user-related database operations including:
 * - User registration with password hashing
 * - User login with password verification
 * - User lookup operations
 * 
 * Security:
 * - Uses password_hash() with PASSWORD_DEFAULT (bcrypt)
 * - Uses password_verify() for secure password checking
 * - All queries use PDO prepared statements
 * - Input sanitization handled by controller
 */

class User
{
    /**
     * PDO database connection
     * @var PDO
     */
    private $pdo;
    
    /**
     * Constructor
     * @param PDO $pdo Database connection
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    
    /**
     * Register a new user
     * 
     * @param string $name User's full name
     * @param string $email User's email address
     * @param string $password Plain text password (will be hashed)
     * @param string $role User role (user, admin, kurir) - default: user
     * @param string|null $phone User's phone number (optional)
     * @return array|false User data array on success, false on failure
     */
    public function register($name, $email, $password, $role = 'user', $phone = null)
    {
        try {
            // Check if email already exists
            if ($this->findByEmail($email)) {
                return false; // Email already registered
            }
            
            // Hash password using bcrypt (PASSWORD_DEFAULT)
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Prepare SQL statement
            $sql = "INSERT INTO users (name, email, password, phone, role, created_at) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $name,
                $email,
                $hashedPassword,
                $phone,
                $role
            ]);
            
            // Get the newly created user ID
            $userId = $this->pdo->lastInsertId();
            
            // Return the new user data (without password)
            return $this->findById($userId);
            
        } catch (PDOException $e) {
            // Log error for debugging
            error_log("User registration error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Login user with email and password
     * 
     * @param string $email User's email
     * @param string $password Plain text password
     * @return array|false User data on success, false on failure
     */
    public function login($email, $password)
    {
        try {
            // Find user by email
            $user = $this->findByEmail($email);
            
            // Check if user exists
            if (!$user) {
                return false; // Email not found
            }
            
            // Verify password
            if (!password_verify($password, $user['password'])) {
                return false; // Wrong password
            }
            
            // Check if password needs rehashing (algorithm improved)
            if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                // Update password with new hash
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $this->pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$newHash, $user['id']]);
            }
            
            // Remove password from returned data
            unset($user['password']);
            
            return $user;
            
        } catch (PDOException $e) {
            error_log("User login error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get user by email (alias for findByEmail)
     */
    public function getByEmail($email) {
        return $this->findByEmail($email);
    }

    /**
     * Get user by ID (alias for findById)
     */
    public function getById($id) {
        return $this->findById($id);
    }

    /**
     * Find user by email address
     * 
     * @param string $email Email address
     * @return array|false User data or false if not found
     */
    public function findByEmail($email)
    {
        try {
            $sql = "SELECT id, name, email, password, phone, role, created_at 
                    FROM users 
                    WHERE email = ? 
                    LIMIT 1";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $user ?: false;
            
        } catch (PDOException $e) {
            error_log("Find by email error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find user by ID
     * 
     * @param int $id User ID
     * @return array|false User data (without password) or false if not found
     */
    public function findById($id)
    {
        try {
            $sql = "SELECT id, name, email, phone, role, created_at 
                    FROM users 
                    WHERE id = ? 
                    LIMIT 1";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $user ?: false;
            
        } catch (PDOException $e) {
            error_log("Find by ID error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update user profile
     * 
     * @param int $id User ID
     * @param array $data Updated data (name, email, phone)
     * @return bool Success status
     */
    public function update($id, $data)
    {
        try {
            // Build UPDATE query dynamically based on provided data
            $fields = [];
            $values = [];
            
            if (isset($data['name'])) {
                $fields[] = "name = ?";
                $values[] = $data['name'];
            }
            
            if (isset($data['email'])) {
                // Check if new email already exists (exclude current user)
                $existingUser = $this->findByEmail($data['email']);
                if ($existingUser && $existingUser['id'] != $id) {
                    return false; // Email already taken
                }
                $fields[] = "email = ?";
                $values[] = $data['email'];
            }
            
            if (isset($data['phone'])) {
                $fields[] = "phone = ?";
                $values[] = $data['phone'];
            }
            
            // Nothing to update
            if (empty($fields)) {
                return false;
            }
            
            // Add user ID to values array
            $values[] = $id;
            
            $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            
            return $stmt->execute($values);
            
        } catch (PDOException $e) {
            error_log("User update error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Change user password
     * 
     * @param int $id User ID
     * @param string $currentPassword Current password for verification
     * @param string $newPassword New password
     * @return bool Success status
     */
    public function changePassword($id, $currentPassword, $newPassword)
    {
        try {
            // Get user with password
            $sql = "SELECT password FROM users WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                return false;
            }
            
            // Verify current password
            if (!password_verify($currentPassword, $user['password'])) {
                return false; // Current password is wrong
            }
            
            // Hash new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // Update password
            $sql = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            
            return $stmt->execute([$hashedPassword, $id]);
            
        } catch (PDOException $e) {
            error_log("Password change error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all users (for admin)
     * 
     * @param int $limit Number of users per page
     * @param int $offset Offset for pagination
     * @return array List of users
     */
    public function getAll($limit = 20, $offset = 0)
    {
        try {
            $sql = "SELECT id, name, email, phone, role, created_at 
                    FROM users 
                    ORDER BY created_at DESC 
                    LIMIT ? OFFSET ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$limit, $offset]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Get all users error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Count total users
     * 
     * @return int Total user count
     */
    public function count()
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM users";
            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return (int) $result['total'];
            
        } catch (PDOException $e) {
            error_log("Count users error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Delete user (soft delete - can be extended)
     * 
     * @param int $id User ID
     * @return bool Success status
     */
    public function delete($id)
    {
        try {
            // Prevent deleting admin if it's the last admin
            $user = $this->findById($id);
            if ($user && $user['role'] === 'admin') {
                $adminCount = $this->pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'admin'")->fetch();
                if ($adminCount['total'] <= 1) {
                    return false; // Cannot delete last admin
                }
            }
            
            $sql = "DELETE FROM users WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            
            return $stmt->execute([$id]);
            
        } catch (PDOException $e) {
            error_log("User delete error: " . $e->getMessage());
            return false;
        }
    }
}
