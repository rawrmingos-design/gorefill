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
     * Send contact form message to admin
     * 
     * @param array $data Contact form data (name, email, subject, message)
     * @return bool Success status
     */
    public function sendContactFormToAdmin($data)
    {
        try {
            $body = $this->getEmailTemplate();
            
            // Replace placeholders
            $body = str_replace('{{content}}', "
                <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                    <h1 style='color: white; margin: 0; font-size: 28px;'>📧 Pesan Baru dari Contact Form</h1>
                </div>
                
                <div style='padding: 30px;'>
                    <p style='font-size: 16px; color: #333; margin-bottom: 20px;'>
                        Anda menerima pesan baru dari contact form GoRefill:
                    </p>
                    
                    <div style='background: #f7fafc; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                        <table style='width: 100%; border-collapse: collapse;'>
                            <tr>
                                <td style='padding: 10px 0; font-weight: bold; color: #4a5568; width: 120px;'>Nama:</td>
                                <td style='padding: 10px 0; color: #2d3748;'>" . htmlspecialchars($data['name']) . "</td>
                            </tr>
                            <tr>
                                <td style='padding: 10px 0; font-weight: bold; color: #4a5568;'>Email:</td>
                                <td style='padding: 10px 0; color: #2d3748;'>" . htmlspecialchars($data['email']) . "</td>
                            </tr>
                            <tr>
                                <td style='padding: 10px 0; font-weight: bold; color: #4a5568;'>Subjek:</td>
                                <td style='padding: 10px 0; color: #2d3748;'>" . htmlspecialchars($data['subject']) . "</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div style='background: white; border-left: 4px solid #667eea; padding: 20px; margin: 20px 0;'>
                        <p style='font-weight: bold; color: #4a5568; margin-bottom: 10px;'>Pesan:</p>
                        <p style='color: #2d3748; line-height: 1.6; white-space: pre-wrap;'>" . nl2br(htmlspecialchars($data['message'])) . "</p>
                    </div>
                    
                    <div style='background: #edf2f7; padding: 15px; border-radius: 8px; margin-top: 30px;'>
                        <p style='margin: 0; color: #718096; font-size: 14px;'>
                            💡 <strong>Tips:</strong> Balas email ini ke <a href='mailto:" . htmlspecialchars($data['email']) . "' style='color: #667eea;'>" . htmlspecialchars($data['email']) . "</a> untuk merespon customer.
                        </p>
                    </div>
                </div>
            ", $body);
            
            // Send to admin email
            $adminEmail = $this->config['admin_email'] ?? $this->config['from_email'];
            
            return $this->send(
                $adminEmail,
                '📧 Contact Form: ' . $data['subject'],
                $body,
                true,
                $data['email'], // Set reply-to as customer email
                $data['name']
            );
            
        } catch (Exception $e) {
            error_log("Contact form to admin error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send auto-reply to customer after contact form submission
     * 
     * @param array $data Contact form data (name, email, subject)
     * @return bool Success status
     */
    public function sendContactFormAutoReply($data)
    {
        try {
            $body = $this->getEmailTemplate();
            
            // Replace placeholders
            $body = str_replace('{{content}}', "
                <div style='background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                    <h1 style='color: white; margin: 0; font-size: 28px;'>✅ Pesan Anda Telah Diterima!</h1>
                </div>
                
                <div style='padding: 30px;'>
                    <p style='font-size: 16px; color: #333;'>
                        Halo <strong>" . htmlspecialchars($data['name']) . "</strong>,
                    </p>
                    
                    <p style='font-size: 16px; color: #555; line-height: 1.6;'>
                        Terima kasih telah menghubungi <strong>GoRefill</strong>! 🎉
                    </p>
                    
                    <div style='background: #f0fdf4; border-left: 4px solid #48bb78; padding: 20px; margin: 20px 0; border-radius: 4px;'>
                        <p style='margin: 0; color: #166534; line-height: 1.6;'>
                            <strong>📌 Subjek:</strong> " . htmlspecialchars($data['subject']) . "<br><br>
                            Kami telah menerima pesan Anda dan akan merespon dalam <strong>1x24 jam</strong> pada hari kerja.
                        </p>
                    </div>
                    
                    <p style='font-size: 16px; color: #555; line-height: 1.6;'>
                        Tim customer service kami akan segera meninjau pesan Anda dan memberikan solusi terbaik.
                    </p>
                    
                    <div style='background: #edf2f7; padding: 20px; border-radius: 8px; margin: 30px 0; text-align: center;'>
                        <p style='margin: 0 0 15px 0; color: #4a5568; font-size: 14px;'>
                            <strong>Butuh bantuan segera?</strong>
                        </p>
                        <p style='margin: 0; color: #718096; font-size: 14px;'>
                            📧 Email: <a href='mailto:support@gorefill.com' style='color: #667eea;'>support@gorefill.com</a><br>
                            📱 WhatsApp: <a href='https://wa.me/6281234567890' style='color: #48bb78;'>+62 812-3456-7890</a><br>
                            🕐 Jam Operasional: Senin - Jumat, 09:00 - 17:00 WIB
                        </p>
                    </div>
                    
                    <p style='font-size: 14px; color: #888; margin-top: 30px;'>
                        Salam hangat,<br>
                        <strong style='color: #48bb78;'>Tim GoRefill</strong> 💚
                    </p>
                </div>
            ", $body);
            
            return $this->send(
                $data['email'],
                'Konfirmasi: Pesan Anda Telah Diterima - GoRefill',
                $body,
                true
            );
            
        } catch (Exception $e) {
            error_log("Contact form auto-reply error: " . $e->getMessage());
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
