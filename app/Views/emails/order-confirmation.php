<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pesanan</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f4f4;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f4f4f4;">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 40px; text-align: center; border-radius: 8px 8px 0 0;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: bold;">
                                ✅ Pesanan Berhasil Dibuat!
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
                                Terima kasih telah berbelanja di GoRefill! Pesanan Anda telah berhasil dibuat dan menunggu pembayaran.
                            </p>
                            
                            <!-- Order Details -->
                            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 30px;">
                                <h2 style="margin: 0 0 15px; font-size: 18px; color: #333333;">
                                    📦 Detail Pesanan
                                </h2>
                                
                                <?php if (!empty($items)): ?>
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <?php foreach ($items as $item): ?>
                                            <tr>
                                                <td style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                                                    <strong style="color: #333333;"><?php echo htmlspecialchars($item['product_name'] ?? 'Product'); ?></strong>
                                                    <br>
                                                    <span style="color: #666666; font-size: 14px;">
                                                        Rp <?php echo number_format($item['price'] ?? 0, 0, ',', '.'); ?> × <?php echo $item['quantity'] ?? 0; ?>
                                                    </span>
                                                </td>
                                                <td align="right" style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                                                    <strong style="color: #10b981;">
                                                        Rp <?php echo number_format($item['subtotal'] ?? 0, 0, ',', '.'); ?>
                                                    </strong>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        
                                        <tr>
                                            <td style="padding: 15px 0; font-size: 18px;">
                                                <strong>Total</strong>
                                            </td>
                                            <td align="right" style="padding: 15px 0; font-size: 18px;">
                                                <strong style="color: #10b981;">
                                                    Rp <?php echo number_format($total, 0, ',', '.'); ?>
                                                </strong>
                                            </td>
                                        </tr>
                                    </table>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Action Button -->
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/index.php?route=profile.orderDetail&order_number=<?php echo urlencode($orderNumber); ?>" 
                                   style="display: inline-block; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; text-decoration: none; padding: 15px 40px; border-radius: 5px; font-size: 16px; font-weight: bold;">
                                    💳 Lanjutkan Pembayaran
                                </a>
                            </div>
                            
                            <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; border-radius: 5px; margin-top: 20px;">
                                <p style="margin: 0; font-size: 14px; color: #92400e;">
                                    ⏰ <strong>Penting:</strong> Silakan selesaikan pembayaran dalam 24 jam agar pesanan Anda tidak dibatalkan secara otomatis.
                                </p>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 30px; text-align: center; border-radius: 0 0 8px 8px;">
                            <p style="margin: 0 0 10px; font-size: 14px; color: #666666;">
                                <strong>GoRefill</strong> - Terima kasih atas kepercayaan Anda
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #999999;">
                                Butuh bantuan? Hubungi kami di support@gorefill.com
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
