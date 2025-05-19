<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de réservation</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .lastname {
            font-weight: bold;
        }
        .wrapper {
            width: 100%;
            background-color: #f9f9f9;
            padding: 20px 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header-band {
            background-color: #2B6ED4;
            height: 10px;
            width: 100%;
        }
        .header {
            background-color: #f8f9fa;
            padding: 25px;
            text-align: center;
            border-bottom: 1px solid #eaeaea;
        }
        .content {
            padding: 30px;
            max-width: 600px;
            margin: 0 auto;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #eaeaea;
        }
        h1, h2 {
            color: #101011;
            margin-top: 0;
            font-size: 20px;
        }
        .info-box {
            background-color:#F5F5F5;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .code {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin: 30px 0;
            letter-spacing: 1px;
        }
        .btn {
            display: inline-block;
            background-color: #2B6ED4;
            color: #ffffff !important;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .btn:hover {
            background-color: #2159ac;
        }
        .total {
            font-size: 18px;
            font-weight: bold;
            padding: 15px;
            background-color: #2B6ED4;
            color: white;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
        }
        .contact-info {
            margin: 10px 0 8px;
            line-height: 1.3;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header-band"></div>
            <div class="header">
                <div class="logo">
                    <table style="margin: 0 auto;">
                        <tr>
                             <td style="vertical-align: middle;"><img src="<?php echo e($message->embed(public_path('img/logo.png'))); ?>" alt="Logo ELS" style="height: 40px; margin-right: 10px;"></td>
                            <td style="vertical-align: middle;"><h2 style="margin: 0;">EMPOWERMENT LEARNING CENTER</h2></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="content">
                <h2 style="color: #333; margin-top: 0; margin-bottom: 20px; font-size: 24px;"><strong>Confirmation de votre réservation</strong></h2>

                <p>Bonjour <span class="lastname"><?php echo e($reservation->user->lastname ?? 'Étudiant'); ?></span>,</p>
                
                <div class="info-box">
                    <p>Nous confirmons avoir reçu votre paiement de <strong><?php echo e(number_format($totalPrice, 2, ',', ' ')); ?> Dt</strong>  effectué le 
                    <strong><?php echo e(\Carbon\Carbon::parse($reservation->payment_date)->format('d/m/Y à H:i')); ?></strong>
                    pour la réservation n° <strong><?php echo e($reservation->id); ?></strong>.</p>
                    <p>Votre réservation est désormais validée. Merci pour votre confiance !</p>
                </div>

            </div>
            
            <div class="footer">
                <p>Ce message est automatique, merci de ne pas y répondre.</p>
                <p>Rue farabi trocadéro, immeuble kraiem 1 étage</p>
                <p><i class="fas fa-phone-alt me-1"></i> 52450193 / 21272129</p>
                <p>© <?php echo e(date('Y')); ?> EMPOWERMENT LEARNING SUCCESS. Tous droits réservés.</p>
            </div>
        </div>
    </div>
</body>
</html><?php /**PATH C:\Users\msi\Desktop\Centre_Formation-main\resources\views/emails/reservation-confirmation.blade.php ENDPATH**/ ?>