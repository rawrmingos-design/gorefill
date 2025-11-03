<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di GoRefill</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f4f4;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f4f4f4;">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px; text-align: center; border-radius: 8px 8px 0 0;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 32px; font-weight: bold;">
                                🎉 Selamat Datang di GoRefill!
                            </h1>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="margin: 0 0 20px; font-size: 16px; color: #333333; line-height: 1.6;">
                                Halo <strong><?php echo htmlspecialchars($userName); ?></strong>,
                            </p>
                            
                            <p style="margin: 0 0 20px; font-size: 16px; color: #333333; line-height: 1.6;">
                                Terima kasih telah bergabung dengan <strong>GoRefill</strong>! Kami senang Anda menjadi bagian dari keluarga kami. 🚀
                            </p>
                            
                            <p style="margin: 0 0 20px; font-size: 16px; color: #333333; line-height: 1.6;">
                                Dengan GoRefill, Anda dapat:
                            </p>
                            
                            <ul style="margin: 0 0 30px; padding-left: 20px; font-size: 16px; color: #333333; line-height: 1.8;">
                                <li>🚰 Pesan air galon berkualitas dengan mudah</li>
                                <li>🔥 Beli LPG langsung ke rumah Anda</li>
                                <li>🛒 Belanja kebutuhan rumah tangga lainnya</li>
                                <li>📍 Lacak pesanan secara real-time</li>
                                <li>💳 Pembayaran aman dan terpercaya</li>
                            </ul>
                            
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/index.php?route=products" 
                                   style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; text-decoration: none; padding: 15px 40px; border-radius: 5px; font-size: 16px; font-weight: bold;">
                                    🛍️ Mulai Belanja Sekarang
                                </a>
                            </div>
                            
                            <p style="margin: 30px 0 0; font-size: 14px; color: #666666; line-height: 1.6;">
                                Jika Anda memiliki pertanyaan, jangan ragu untuk menghubungi kami di <a href="mailto:support@gorefill.com" style="color: #667eea;">support@gorefill.com</a>
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 30px; text-align: center; border-radius: 0 0 8px 8px;">
                            <p style="margin: 0 0 10px; font-size: 14px; color: #666666;">
                                <strong>GoRefill</strong> - Solusi Mudah untuk Kebutuhan Rumah Tangga
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #999999;">
                                Email ini dikirim ke <?php echo htmlspecialchars($userEmail); ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
