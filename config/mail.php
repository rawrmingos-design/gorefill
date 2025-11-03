<?php
/**
 * Email Configuration
 * 
 * Configure SMTP settings for sending emails
 * For Gmail: Use App Password (not regular password)
 * Enable 2FA and generate App Password at: https://myaccount.google.com/apppasswords
 */

return [
    // SMTP Server Settings
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587, // 587 for TLS, 465 for SSL
    'smtp_secure' => 'tls', // 'tls' or 'ssl'
    
    // SMTP Authentication
    'smtp_user' => 'your-email@gmail.com', // Change this to your Gmail
    'smtp_pass' => 'your-app-password', // Change this to your App Password
    
    // Email From
    'from_email' => 'noreply@gorefill.com',
    'from_name' => 'GoRefill',
    
    // Email Reply To (optional)
    'reply_to_email' => 'support@gorefill.com',
    'reply_to_name' => 'GoRefill Support',
    
    // Debug mode (0 = off, 1 = client messages, 2 = client and server messages)
    'debug' => 0,
    
    // Character set
    'charset' => 'UTF-8',
    
    // Timeout (seconds)
    'timeout' => 30,
];
