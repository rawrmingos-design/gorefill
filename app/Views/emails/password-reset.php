<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f4f4;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f4f4f4;">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); padding: 40px; text-align: center; border-radius: 8px 8px 0 0;">
                            <div style="font-size: 60px; margin-bottom: 10px;">🔐</div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: bold;">
                                Reset Password
                            </h1>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="margin: 0 0 20px; font-size: 16px; color: #333333; line-height: 1.6;">
                                Halo <strong><?php echo htmlspecialchars($userName); ?></strong>,
                            </p>
                            
                            <p style="margin: 0 0 30px; font-size: 16px; color: #333333; line-height: 1.6;">
                                Kami menerima permintaan untuk mereset password akun GoRefill Anda. Klik tombol di bawah ini untuk membuat password baru:
                            </p>
                            
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="<?php echo htmlspecialchars($resetLink); ?>" 
                                   style="display: inline-block; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: #ffffff; text-decoration: none; padding: 15px 40px; border-radius: 5px; font-size: 16px; font-weight: bold;">
                                    🔑 Reset Password Saya
                                </a>
                            </div>
                            
                            <p style="margin: 30px 0; font-size: 14px; color: #666666; line-height: 1.6;">
                                Atau copy & paste link berikut ke browser Anda:
                            </p>
                            <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; word-break: break-all; font-size: 12px; color: #374151; margin-bottom: 30px;">
                                <?php echo htmlspecialchars($resetLink); ?>
                            </div>
                            
                            <div style="background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                                <p style="margin: 0 0 10px; font-size: 14px; color: #991b1b; font-weight: bold;">
                                    ⏰ Link ini hanya berlaku selama 1 jam
                                </p>
                                <p style="margin: 0; font-size: 14px; color: #991b1b;">
                                    Jika Anda tidak meminta reset password, abaikan email ini. Password Anda tidak akan berubah.
                                </p>
                            </div>
                            
                            <div style="background-color: #f9fafb; padding: 20px; border-radius: 5px; border: 1px solid #e5e7eb;">
                                <h3 style="margin: 0 0 10px; font-size: 16px; color: #374151;">
                                    🛡️ Tips Keamanan:
                                </h3>
                                <ul style="margin: 0; padding-left: 20px; font-size: 14px; color: #4b5563; line-height: 1.8;">
                                    <li>Gunakan password yang kuat (min. 8 karakter)</li>
                                    <li>Kombinasikan huruf besar, kecil, angka, dan simbol</li>
                                    <li>Jangan gunakan password yang sama di akun lain</li>
                                    <li>Jangan bagikan password Anda kepada siapapun</li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 30px; text-align: center; border-radius: 0 0 8px 8px;">
                            <p style="margin: 0 0 10px; font-size: 14px; color: #666666;">
                                <strong>GoRefill</strong> - Keamanan Akun Anda adalah Prioritas Kami
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #999999;">
                                Jika Anda tidak meminta email ini, hubungi kami segera di support@gorefill.com
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
