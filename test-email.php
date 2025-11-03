<?php
/**
 * Email Testing Script
 * 
 * Test email configuration and sending
 * Run: php test-email.php
 */

require_once __DIR__ . '/app/Services/MailService.php';

echo "==============================================\n";
echo "   GoRefill Email Testing Script\n";
echo "==============================================\n\n";

// Get test email from command line or use default
$testEmail = $argv[1] ?? 'your-email@gmail.com';

echo "Testing email to: {$testEmail}\n\n";

try {
    $mailService = new MailService();
    
    echo "[1/1] Sending test email...\n";
    $result = $mailService->sendTestEmail($testEmail);
    
    if ($result) {
        echo "✅ SUCCESS! Test email sent successfully!\n";
        echo "   Check your inbox: {$testEmail}\n";
        echo "   If not in inbox, check spam folder.\n";
    } else {
        echo "❌ FAILED! Could not send test email.\n";
        echo "   Check error log for details.\n";
        echo "   Verify config/mail.php settings.\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "   Check your mail configuration in config/mail.php\n";
}

echo "\n==============================================\n";
echo "Configuration Tips:\n";
echo "==============================================\n";
echo "1. For Gmail:\n";
echo "   - Enable 2FA\n";
echo "   - Generate App Password\n";
echo "   - Use App Password in config\n\n";
echo "2. Check config/mail.php:\n";
echo "   - smtp_user = your email\n";
echo "   - smtp_pass = App Password\n";
echo "   - smtp_host = smtp.gmail.com\n";
echo "   - smtp_port = 587\n\n";
echo "3. Test command:\n";
echo "   php test-email.php your-email@example.com\n";
echo "==============================================\n\n";
