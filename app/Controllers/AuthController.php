<?php
/**
 * Authentication Controller for GoRefill Application
 * 
 * Handles user authentication operations:
 * - User registration
 * - User login
 * - User logout
 * - Session management
 */

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/BaseController.php';

class AuthController extends BaseController
{
    /**
     * User model instance
     * @var User
     */
    private $userModel;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User($this->pdo);
    }
    
    /**
     * Show registration form (GET)
     */
    public function showRegisterForm()
    {
        // If already logged in, redirect to home
        if (isset($_SESSION['user_id'])) {
            $this->redirect('home');
        }
        
        $this->render('auth/register', [
            'title' => 'Register - GoRefill'
        ]);
    }
    
    /**
     * Show login form (GET)
     */
    public function showLoginForm()
    {
        // If already logged in, redirect to home
        if (isset($_SESSION['user_id'])) {
            $this->redirect('home');
        }
        
        $this->render('auth/login', [
            'title' => 'Login - GoRefill'
        ]);
    }
    
    /**
     * Handle user registration (POST)
     */
    public function register()
    {
        // Only accept POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
        }
        
        // Get input data
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $phone = trim($_POST['phone'] ?? '');
        
        // Validate input
        $errors = $this->validate($_POST, [
            'name' => 'required|min:3|max:150',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);
        
        // Check password confirmation
        if ($password !== $passwordConfirm) {
            $errors['password_confirm'] = 'Passwords do not match';
        }
        
        // If validation fails
        if (!empty($errors)) {
            $this->json(['errors' => $errors], 400);
        }
        
        // Attempt to register user
        $user = $this->userModel->register($name, $email, $password, 'user', $phone);
        
        if (!$user) {
            $this->json([
                'error' => 'Registration failed',
                'message' => 'Email already registered or database error'
            ], 400);
        }
        
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['phone'] = $user['phone'] ?? null;
        $_SESSION['created_at'] = $user['created_at'] ?? null;
        
        // Set flash message
        $this->flash('success', 'Registration successful! Welcome to GoRefill.');
        
        // Return success response
        $this->json([
            'success' => true,
            'message' => 'Registration successful',
            'user' => $user,
            'redirect' => 'index.php?route=home'
        ], 201);
    }
    
    /**
     * Handle user login (POST)
     */
    public function login()
    {
        // Only accept POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
        }
        
        // Get input data
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        // Validate input
        $errors = $this->validate($_POST, [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        if (!empty($errors)) {
            $this->json(['errors' => $errors], 400);
        }
        
        // Attempt to login
        $user = $this->userModel->login($email, $password);
        
        if (!$user) {
            $this->json([
                'error' => 'Login failed',
                'message' => 'Invalid email or password'
            ], 401);
        }
        
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['phone'] = $user['phone'] ?? null;
        $_SESSION['created_at'] = $user['created_at'] ?? null;
        
        // Remember me functionality
        if ($remember) {
            // Set cookie for 30 days
            $token = bin2hex(random_bytes(32));
            setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
            
            // Store token in session
            $_SESSION['remember_token'] = $token;
        }
        
        // Set flash message
        $this->flash('success', 'Login successful! Welcome back, ' . $user['name']);
        
        // Check if there's a redirect URL
        $redirectUrl = $_SESSION['redirect_after_login'] ?? 'index.php?route=home';
        unset($_SESSION['redirect_after_login']);
        
        // Redirect based on role
        if ($user['role'] === 'admin') {
            $redirectUrl = 'index.php?route=admin.dashboard';
        } elseif ($user['role'] === 'kurir') {
            $redirectUrl = 'index.php?route=courier.dashboard';
        }
        
        // Return success response
        $this->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => $user,
            'redirect' => $redirectUrl
        ], 200);
    }
    
    /**
     * Handle user logout
     */
    public function logout()
    {
        // Clear remember me cookie
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        }
        
        // Destroy session
        session_destroy();
        
        // Start new session for flash message
        session_start();
        $this->flash('success', 'You have been logged out successfully');
        
        // Redirect to login page
        $this->redirect('auth.login');
    }
    
    /**
     * Check authentication status (AJAX endpoint)
     */
    public function checkAuth()
    {
        if (isset($_SESSION['user_id'])) {
            $user = $this->userModel->findById($_SESSION['user_id']);
            $this->json([
                'authenticated' => true,
                'user' => $user
            ]);
        } else {
            $this->json([
                'authenticated' => false
            ]);
        }
    }
    
    /**
     * Edit Profile (AJAX endpoint)
     */
    public function editProfile()
    {
        // Require authentication
        $this->requireAuth();
        
        // Only accept POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
        }
        
        // Get current user ID
        $userId = $_SESSION['user_id'];
        
        // Validate input
        $errors = $this->validate($_POST, [
            'name' => 'required|min:3|max:100'
        ]);
        
        if (!empty($errors)) {
            $this->json(['errors' => $errors], 400);
        }
        
        // Prepare update data
        $updateData = [
            'name' => trim($_POST['name'])
        ];
        
        // Add phone if provided
        if (isset($_POST['phone']) && !empty($_POST['phone'])) {
            $updateData['phone'] = trim($_POST['phone']);
        }
        
        // Update user
        $success = $this->userModel->update($userId, $updateData);
        
        if (!$success) {
            $this->json(['error' => 'Failed to update profile'], 500);
        }
        
        // Update session
        $_SESSION['name'] = $updateData['name'];
        if (isset($updateData['phone'])) {
            $_SESSION['phone'] = $updateData['phone'];
        }
        
        $this->flash('success', 'Profile updated successfully!');
        $this->json([
            'success' => true,
            'message' => 'Profile updated successfully'
        ]);
    }
    
    /**
     * Change Password (AJAX endpoint)
     */
    public function changePassword()
    {
        // Require authentication
        $this->requireAuth();
        
        // Only accept POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
        }
        
        // Get current user ID
        $userId = $_SESSION['user_id'];
        
        // Validate input
        $errors = $this->validate($_POST, [
            'current_password' => 'required',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required'
        ]);
        
        if (!empty($errors)) {
            $this->json(['errors' => $errors], 400);
        }
        
        // Check if new passwords match
        if ($_POST['new_password'] !== $_POST['confirm_password']) {
            $this->json(['error' => 'New passwords do not match'], 400);
        }
        
        // Change password
        $success = $this->userModel->changePassword(
            $userId,
            $_POST['current_password'],
            $_POST['new_password']
        );
        
        if (!$success) {
            $this->json(['error' => 'Current password is incorrect'], 401);
        }
        
        $this->flash('success', 'Password changed successfully!');
        $this->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    }
    
    /**
     * Delete Account (AJAX endpoint)
     */
    public function deleteAccount()
    {
        // Require authentication
        $this->requireAuth();
        
        // Only accept POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
        }
        
        // Get current user ID
        $userId = $_SESSION['user_id'];
        
        // Validate email confirmation
        if (!isset($_POST['email']) || $_POST['email'] !== $_SESSION['email']) {
            $this->json(['error' => 'Email confirmation does not match'], 400);
        }
        
        // Delete user
        $success = $this->userModel->delete($userId);
        
        if (!$success) {
            $this->json(['error' => 'Failed to delete account. You may be the last admin.'], 500);
        }
        
        // Destroy session
        session_destroy();
        
        // Start new session for flash message
        session_start();
        $this->flash('success', 'Your account has been deleted successfully');
        
        $this->json([
            'success' => true,
            'message' => 'Account deleted successfully',
            'redirect' => 'index.php?route=home'
        ]);
    }
}
