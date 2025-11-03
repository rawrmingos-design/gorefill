<?php
/**
 * Mail Service
 * 
 * Handles all email sending functionality using PHPMailer
 * Week 4 Day 19: Email Notifications
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    private $mailer;
    private $config;
    
    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/mail.php';
        $this->mailer = new PHPMailer(true);
        $this->setupSMTP();
    }
    
    /**
     * Setup SMTP configuration
     */
    private function setupSMTP()
    {
        try {
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->config['smtp_host'];
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->config['smtp_user'];
            $this->mailer->Password = $this->config['smtp_pass'];
            $this->mailer->SMTPSecure = $this->config['smtp_secure'];
            $this->mailer->Port = $this->config['smtp_port'];
            $this->mailer->CharSet = $this->config['charset'];
            $this->mailer->Timeout = $this->config['timeout'];
            
            // Debug mode
            $this->mailer->SMTPDebug = $this->config['debug'];
            
            // Default from
            $this->mailer->setFrom($this->config['from_email'], $this->config['from_name']);
            
            // Reply to (if set)
            if (!empty($this->config['reply_to_email'])) {
                $this->mailer->addReplyTo($this->config['reply_to_email'], $this->config['reply_to_name']);
            }
        } catch (Exception $e) {
            error_log("SMTP Setup Error: " . $e->getMessage());
        }
    }
    
    /**
     * Send email
     * 
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $body Email body (HTML or plain text)
     * @param bool $isHtml Whether body is HTML
     * @param string $recipientName Recipient name (optional)
     * @return bool Success status
     */
    public function send($to, $subject, $body, $isHtml = true, $recipientName = '')
    {
        try {
            // Clear previous recipients
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            
            // Recipients
            $this->mailer->addAddress($to, $recipientName);
            
            // Content
            $this->mailer->isHTML($isHtml);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            
            // Alt body for non-HTML clients
            if ($isHtml) {
                $this->mailer->AltBody = strip_tags($body);
            }
            
            // Send
            $result = $this->mailer->send();
            
            if ($result) {
                error_log("Email sent successfully to: {$to}");
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Email send error: " . $this->mailer->ErrorInfo);
            return false;
        }
    }
    
    /**
     * Load email template
     * 
     * @param string $template Template name
     * @param array $data Data to pass to template
     * @return string Rendered template
     */
    private function loadTemplate($template, $data = [])
    {
        $templatePath = __DIR__ . '/../Views/emails/' . $template . '.php';
        
        if (!file_exists($templatePath)) {
            throw new Exception("Email template not found: {$template}");
        }
        
        // Extract data to variables
        extract($data);
        
        // Start output buffering
        ob_start();
        include $templatePath;
        $content = ob_get_clean();
        
        return $content;
    }
    
    /**
     * Send welcome email to new user
     * 
     * @param array $user User data
     * @return bool Success status
     */
    public function sendWelcomeEmail($user)
    {
        try {
            $template = $this->loadTemplate('welcome', [
                'userName' => $user['name'] ?? 'User',
                'userEmail' => $user['email'] ?? ''
            ]);
            
            return $this->send(
                $user['email'],
                'Selamat Datang di GoRefill! 🎉',
                $template,
                true,
                $user['name'] ?? ''
            );
        } catch (Exception $e) {
            error_log("Welcome email error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send order confirmation email
     * 
     * @param array $order Order data with items
     * @return bool Success status
     */
    public function sendOrderConfirmation($order)
    {
        try {
            $template = $this->loadTemplate('order-confirmation', [
                'order' => $order,
                'orderNumber' => $order['order_number'] ?? 'N/A',
                'customerName' => $order['customer_name'] ?? 'Customer',
                'total' => $order['total_price'] ?? 0,
                'items' => $order['items'] ?? []
            ]);
            
            return $this->send(
                $order['customer_email'] ?? '',
                'Pesanan Anda Berhasil Dibuat #' . ($order['order_number'] ?? ''),
                $template,
                true,
                $order['customer_name'] ?? ''
            );
        } catch (Exception $e) {
            error_log("Order confirmation email error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send payment success email
     * 
     * @param array $order Order data
     * @return bool Success status
     */
    public function sendPaymentSuccess($order)
    {
        try {
            $template = $this->loadTemplate('payment-success', [
                'order' => $order,
                'orderNumber' => $order['order_number'] ?? 'N/A',
                'customerName' => $order['customer_name'] ?? 'Customer',
                'total' => $order['total_price'] ?? 0
            ]);
            
            return $this->send(
                $order['customer_email'] ?? '',
                'Pembayaran Berhasil! Pesanan #' . ($order['order_number'] ?? '') . ' Sedang Diproses',
                $template,
                true,
                $order['customer_name'] ?? ''
            );
        } catch (Exception $e) {
            error_log("Payment success email error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send shipping notification email
     * 
     * @param array $order Order data
     * @param array $courier Courier data
     * @return bool Success status
     */
    public function sendShippingNotification($order, $courier = [])
    {
        try {
            $template = $this->loadTemplate('shipping', [
                'order' => $order,
                'orderNumber' => $order['order_number'] ?? 'N/A',
                'customerName' => $order['customer_name'] ?? 'Customer',
                'courier' => $courier,
                'courierName' => $courier['name'] ?? 'Kurir',
                'courierPhone' => $courier['phone'] ?? ''
            ]);
            
            return $this->send(
                $order['customer_email'] ?? '',
                'Pesanan Anda Sedang Dikirim! 🚚 #' . ($order['order_number'] ?? ''),
                $template,
                true,
                $order['customer_name'] ?? ''
            );
        } catch (Exception $e) {
            error_log("Shipping notification email error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send delivery confirmation email
     * 
     * @param array $order Order data
     * @return bool Success status
     */
    public function sendDeliveryConfirmation($order)
    {
        try {
            $template = $this->loadTemplate('delivered', [
                'order' => $order,
                'orderNumber' => $order['order_number'] ?? 'N/A',
                'customerName' => $order['customer_name'] ?? 'Customer'
            ]);
            
            return $this->send(
                $order['customer_email'] ?? '',
                'Pesanan Anda Telah Sampai! ✅ #' . ($order['order_number'] ?? ''),
                $template,
                true,
                $order['customer_name'] ?? ''
            );
        } catch (Exception $e) {
            error_log("Delivery confirmation email error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send password reset email
     * 
     * @param array $user User data
     * @param string $token Reset token
     * @return bool Success status
     */
    public function sendPasswordReset($user, $token)
    {
        try {
            $resetLink = 'http://' . $_SERVER['HTTP_HOST'] . '/index.php?route=auth.resetPassword&token=' . $token;
            
            $template = $this->loadTemplate('password-reset', [
                'userName' => $user['name'] ?? 'User',
                'resetLink' => $resetLink,
                'token' => $token
            ]);
            
            return $this->send(
                $user['email'],
                'Reset Password GoRefill 🔐',
                $template,
                true,
                $user['name'] ?? ''
            );
        } catch (Exception $e) {
            error_log("Password reset email error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send test email
     * 
     * @param string $to Recipient email
     * @return bool Success status
     */
    public function sendTestEmail($to)
    {
        try {
            $body = '<h1>Test Email from GoRefill</h1>';
            $body .= '<p>This is a test email to verify email configuration.</p>';
            $body .= '<p>If you receive this, your email setup is working correctly! ✅</p>';
            
            return $this->send(
                $to,
                'GoRefill Test Email',
                $body,
                true
            );
        } catch (Exception $e) {
            error_log("Test email error: " . $e->getMessage());
            return false;
        }
    }
}
