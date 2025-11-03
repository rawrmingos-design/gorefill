<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f4f4;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f4f4f4;">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); padding: 40px; text-align: center; border-radius: 8px 8px 0 0;">
                            <div style="font-size: 60px; margin-bottom: 10px;">✅</div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: bold;">
                                Pembayaran Berhasil!
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
                                Pembayaran Anda sebesar <strong style="color: #3b82f6;">Rp <?php echo number_format($total, 0, ',', '.'); ?></strong> telah kami terima! 🎉
                            </p>
                            
                            <div style="background-color: #dbeafe; padding: 20px; border-radius: 5px; margin-bottom: 30px; text-align: center;">
                                <div style="font-size: 40px; margin-bottom: 10px;">📦</div>
                                <h2 style="margin: 0 0 10px; font-size: 20px; color: #1e40af;">
                                    Pesanan Sedang Diproses
                                </h2>
                                <p style="margin: 0; font-size: 14px; color: #1e3a8a;">
                                    Kami akan segera memproses dan mengirim pesanan Anda
                                </p>
                            </div>
                            
                            <h3 style="margin: 0 0 15px; font-size: 18px; color: #333333;">
                                Apa yang terjadi selanjutnya?
                            </h3>
                            
                            <ol style="margin: 0 0 30px; padding-left: 20px; font-size: 15px; color: #333333; line-height: 1.8;">
                                <li>✅ <strong>Pembayaran Dikonfirmasi</strong> - Selesai</li>
                                <li>📦 <strong>Pesanan Dikemas</strong> - Dalam proses</li>
                                <li>🚚 <strong>Pengiriman</strong> - Kurir akan mengirim pesanan Anda</li>
                                <li>🏠 <strong>Sampai Tujuan</strong> - Nikmati produk Anda!</li>
                            </ol>
                            
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/index.php?route=profile.orderDetail&order_number=<?php echo urlencode($orderNumber); ?>" 
                                   style="display: inline-block; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: #ffffff; text-decoration: none; padding: 15px 40px; border-radius: 5px; font-size: 16px; font-weight: bold;">
                                    📍 Lacak Pesanan Anda
                                </a>
                            </div>
                            
                            <div style="background-color: #ecfdf5; border-left: 4px solid #10b981; padding: 15px; border-radius: 5px;">
                                <p style="margin: 0; font-size: 14px; color: #065f46;">
                                    💬 <strong>Butuh Bantuan?</strong> Tim customer service kami siap membantu Anda 24/7
                                </p>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 30px; text-align: center; border-radius: 0 0 8px 8px;">
                            <p style="margin: 0 0 10px; font-size: 14px; color: #666666;">
                                <strong>GoRefill</strong> - Terima kasih atas pembayaran Anda
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #999999;">
                                Email konfirmasi untuk Order #<?php echo htmlspecialchars($orderNumber); ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
