<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Sedang Dikirim</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f4f4;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f4f4f4;">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); padding: 40px; text-align: center; border-radius: 8px 8px 0 0;">
                            <div style="font-size: 60px; margin-bottom: 10px;">🚚</div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: bold;">
                                Pesanan Sedang Dikirim!
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
                                Kabar gembira! Pesanan Anda sudah dikemas dan sedang dalam perjalanan menuju alamat Anda! 🎉
                            </p>
                            
                            <!-- Courier Info -->
                            <?php if (!empty($courier)): ?>
                                <div style="background-color: #f3e8ff; padding: 20px; border-radius: 5px; margin-bottom: 30px;">
                                    <h2 style="margin: 0 0 15px; font-size: 18px; color: #6d28d9;">
                                        👤 Informasi Kurir
                                    </h2>
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <tr>
                                            <td style="padding: 5px 0; color: #666666; font-size: 14px;">Nama Kurir:</td>
                                            <td style="padding: 5px 0; color: #333333; font-size: 14px; font-weight: bold;" align="right">
                                                <?php echo htmlspecialchars($courierName); ?>
                                            </td>
                                        </tr>
                                        <?php if (!empty($courierPhone)): ?>
                                            <tr>
                                                <td style="padding: 5px 0; color: #666666; font-size: 14px;">No. Telepon:</td>
                                                <td style="padding: 5px 0; color: #333333; font-size: 14px; font-weight: bold;" align="right">
                                                    <?php echo htmlspecialchars($courierPhone); ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                            <?php endif; ?>
                            
                            <div style="background-color: #dbeafe; padding: 20px; border-radius: 5px; margin-bottom: 30px;">
                                <h3 style="margin: 0 0 15px; font-size: 16px; color: #1e40af;">
                                    📍 Status Pengiriman
                                </h3>
                                <div style="position: relative;">
                                    <div style="padding-left: 30px; margin-bottom: 15px;">
                                        <div style="position: absolute; left: 0; width: 20px; height: 20px; background-color: #10b981; border-radius: 50%; border: 3px solid #ffffff;"></div>
                                        <strong style="color: #333333;">Pesanan Dikemas</strong>
                                        <div style="font-size: 12px; color: #666666;">Sudah selesai</div>
                                    </div>
                                    <div style="padding-left: 30px; margin-bottom: 15px;">
                                        <div style="position: absolute; left: 0; width: 20px; height: 20px; background-color: #3b82f6; border-radius: 50%; border: 3px solid #ffffff;"></div>
                                        <strong style="color: #333333;">Dalam Pengiriman</strong>
                                        <div style="font-size: 12px; color: #666666;">Saat ini</div>
                                    </div>
                                    <div style="padding-left: 30px;">
                                        <div style="position: absolute; left: 0; width: 20px; height: 20px; background-color: #d1d5db; border-radius: 50%; border: 3px solid #ffffff;"></div>
                                        <strong style="color: #666666;">Sampai Tujuan</strong>
                                        <div style="font-size: 12px; color: #999999;">Segera</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/index.php?route=profile.orderDetail&order_number=<?php echo urlencode($orderNumber); ?>" 
                                   style="display: inline-block; background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); color: #ffffff; text-decoration: none; padding: 15px 40px; border-radius: 5px; font-size: 16px; font-weight: bold;">
                                    📍 Lacak Kurir Real-Time
                                </a>
                            </div>
                            
                            <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; border-radius: 5px;">
                                <p style="margin: 0; font-size: 14px; color: #92400e;">
                                    💡 <strong>Tips:</strong> Pastikan ada yang menerima di alamat pengiriman. Hubungi kurir jika ada kendala.
                                </p>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 30px; text-align: center; border-radius: 0 0 8px 8px;">
                            <p style="margin: 0 0 10px; font-size: 14px; color: #666666;">
                                <strong>GoRefill</strong> - Pengiriman Cepat & Aman
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #999999;">
                                Pesanan Anda akan segera sampai!
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
