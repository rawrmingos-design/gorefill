<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Telah Sampai</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f4f4;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f4f4f4;">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 40px; text-align: center; border-radius: 8px 8px 0 0;">
                            <div style="font-size: 60px; margin-bottom: 10px;">🎉</div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: bold;">
                                Pesanan Telah Sampai!
                            </h1>
                            <p style="margin: 10px 0 0; color: #ffffff; font-size: 18px;">
                                Order #<?php echo htmlspecialchars($orderNumber); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="margin: 0 0 20px; font-size: 16px; color: #333333; line-height: 1.6;">
                                Halo <strong><?php echo htmlspecialchars($customerName); ?></strong>,
                            </p>
                            
                            <p style="margin: 0 0 30px; font-size: 16px; color: #333333; line-height: 1.6;">
                                Yeay! Pesanan Anda sudah sampai dengan selamat! 🎊 Terima kasih telah berbelanja di GoRefill.
                            </p>
                            
                            <div style="background-color: #ecfdf5; padding: 25px; border-radius: 8px; margin-bottom: 30px; text-align: center;">
                                <div style="font-size: 50px; margin-bottom: 15px;">✅</div>
                                <h2 style="margin: 0 0 10px; font-size: 22px; color: #065f46;">
                                    Pesanan Berhasil Diterima
                                </h2>
                                <p style="margin: 0; font-size: 14px; color: #047857;">
                                    Status: <strong>DELIVERED</strong>
                                </p>
                            </div>
                            
                            <div style="background-color: #fef3c7; padding: 20px; border-radius: 5px; margin-bottom: 30px;">
                                <h3 style="margin: 0 0 15px; font-size: 16px; color: #92400e;">
                                    ⭐ Bagaimana Pengalaman Anda?
                                </h3>
                                <p style="margin: 0 0 15px; font-size: 14px; color: #78350f;">
                                    Kami sangat menghargai feedback Anda! Bagikan pengalaman belanja dan produk yang Anda terima.
                                </p>
                                <div style="text-align: center;">
                                    <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/index.php?route=profile.orderDetail&order_number=<?php echo urlencode($orderNumber); ?>" 
                                       style="display: inline-block; background-color: #f59e0b; color: #ffffff; text-decoration: none; padding: 12px 30px; border-radius: 5px; font-size: 14px; font-weight: bold;">
                                        ⭐ Beri Rating & Ulasan
                                    </a>
                                </div>
                            </div>
                            
                            <div style="background-color: #f3f4f6; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
                                <h3 style="margin: 0 0 15px; font-size: 16px; color: #374151;">
                                    💡 Ada Masalah dengan Pesanan?
                                </h3>
                                <p style="margin: 0 0 10px; font-size: 14px; color: #4b5563;">
                                    Jika ada produk yang rusak, salah kirim, atau masalah lainnya, silakan hubungi kami segera:
                                </p>
                                <ul style="margin: 0; padding-left: 20px; font-size: 14px; color: #4b5563;">
                                    <li>📧 Email: support@gorefill.com</li>
                                    <li>📞 WhatsApp: +62 812-3456-7890</li>
                                    <li>⏰ Jam Operasional: 08:00 - 20:00 WIB</li>
                                </ul>
                            </div>
                            
                            <div style="text-align: center; margin: 30px 0;">
                                <p style="margin: 0 0 15px; font-size: 16px; color: #333333;">
                                    Belanja lagi di GoRefill!
                                </p>
                                <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/index.php?route=products" 
                                   style="display: inline-block; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; text-decoration: none; padding: 15px 40px; border-radius: 5px; font-size: 16px; font-weight: bold;">
                                    🛍️ Lihat Produk Lainnya
                                </a>
                            </div>
                            
                            <p style="margin: 20px 0 0; font-size: 14px; color: #666666; text-align: center; font-style: italic;">
                                "Terima kasih telah mempercayai GoRefill untuk kebutuhan rumah tangga Anda!" 💚
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
                                Kami senang melayani Anda! ❤️
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
