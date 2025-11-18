<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Services/MailService.php';

class ContactController extends BaseController
{
    private $mailService;
    
    public function __construct()
    {
        parent::__construct();
        $this->mailService = new MailService();
    }

    /**
     * Display contact form
     */
    public function index()
    {
        $this->render('contact/index', [
            'title' => 'Hubungi Kami - Contact'
        ]);
    }

    /**
     * Handle contact form submission (AJAX)
     */
    public function submit()
    {
        header('Content-Type: application/json');
        
        try {
            // Validate input
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $subject = $_POST['subject'] ?? '';
            $message = $_POST['message'] ?? '';
            
            // Validation
            $errors = [];
            
            if (empty(trim($name))) {
                $errors[] = 'Nama harus diisi';
            }
            
            if (empty(trim($email))) {
                $errors[] = 'Email harus diisi';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Format email tidak valid';
            }
            
            if (empty(trim($subject))) {
                $errors[] = 'Subjek harus diisi';
            }
            
            if (empty(trim($message))) {
                $errors[] = 'Pesan harus diisi';
            } elseif (strlen(trim($message)) < 10) {
                $errors[] = 'Pesan minimal 10 karakter';
            }
            
            // Return errors if any
            if (!empty($errors)) {
                echo json_encode([
                    'success' => false,
                    'message' => implode(', ', $errors)
                ]);
                exit;
            }
            
            // Send email to admin
            $adminSent = $this->mailService->sendContactFormToAdmin([
                'name' => htmlspecialchars(trim($name)),
                'email' => htmlspecialchars(trim($email)),
                'subject' => htmlspecialchars(trim($subject)),
                'message' => htmlspecialchars(trim($message))
            ]);
            
            // Send auto-reply to customer
            $customerSent = $this->mailService->sendContactFormAutoReply([
                'name' => htmlspecialchars(trim($name)),
                'email' => htmlspecialchars(trim($email)),
                'subject' => htmlspecialchars(trim($subject))
            ]);
            
            if ($adminSent && $customerSent) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Terima kasih! Pesan Anda telah terkirim. Kami akan merespon dalam 1x24 jam.'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Gagal mengirim pesan. Silakan coba lagi atau hubungi kami melalui email langsung.'
                ]);
            }
            
        } catch (Exception $e) {
            error_log('Contact form error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi nanti.'
            ]);
        }
    }
}
