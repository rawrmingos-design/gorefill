<?php
/**
 * Midtrans Payment Gateway Configuration
 * 
 * Copy this file to midtrans.php and update with your Midtrans credentials
 * Get your credentials from: https://dashboard.midtrans.com/settings/config_info
 */

return [
    // Set to true for production, false for sandbox/testing
    'is_production' => false,
    
    // Server Key (for backend API calls)
    // Sandbox: Get from https://dashboard.sandbox.midtrans.com/settings/config_info
    // Production: Get from https://dashboard.midtrans.com/settings/config_info
    'server_key' => 'SB-Mid-server-YOUR_SERVER_KEY_HERE',
    
    // Client Key (for frontend Snap.js)
    // Sandbox: Get from https://dashboard.sandbox.midtrans.com/settings/config_info
    // Production: Get from https://dashboard.midtrans.com/settings/config_info
    'client_key' => 'SB-Mid-client-YOUR_CLIENT_KEY_HERE',
    
    // Merchant ID (optional, for reference)
    'merchant_id' => 'YOUR_MERCHANT_ID',
    
    // 3DS Authentication
    // Set to true to enable 3D Secure for credit card transactions
    'is_3ds' => true,
    
    // Snap API URL
    'snap_url' => [
        'sandbox' => 'https://app.sandbox.midtrans.com/snap/snap.js',
        'production' => 'https://app.midtrans.com/snap/snap.js'
    ],

    'is_sanitized' => true,
    
    // Enabled Payment Methods
    // Customize which payment methods to show in Snap popup
    'enabled_payments' => [
        'credit_card',
        'gopay',
        'shopeepay', 
        'bca_va',
        'bni_va',
        'bri_va',
        'permata_va',
        'alfamart',
        'indomaret'
    ],
    
    // Credit Card Settings
    'credit_card' => [
        'secure' => true,
        'bank' => 'bca', // Default acquiring bank
        'installment' => [
            'required' => false,
            'terms' => [
                'bni' => [3, 6, 12],
                'mandiri' => [3, 6, 12],
                'cimb' => [3],
                'bca' => [3, 6, 12],
                'offline' => [6, 12]
            ]
        ]
    ],
    
    // Callback URLs
    'callbacks' => [
        'finish' => 'index.php?route=payment.finish',
        'unfinish' => 'index.php?route=payment.unfinish',
        'error' => 'index.php?route=payment.error'
    ]
];
