<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificat de Réussite</title>
    <style>
        @import  url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@700;900&display=swap');

        body {
            margin: 0;
            padding: 20px;
            background-color: #f5f7fa;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .certificate-container {
            width: 100%;
            max-width: 900px;
            background: white;
            position: relative;
            box-shadow: 0 15px 40px rgba(43, 110, 212, 0.15);
            border-radius: 12px;
            overflow: hidden;
            margin: 30px auto;
        }

        .certificate-inner {
            border: 1px solid rgba(43, 110, 212, 0.08);
            margin: 15px;
            border-radius: 8px;
            padding: 15px;
            position: relative;
            background: #fff;
        }

        .logo-container {
            position: absolute;
            top: 30px;
            left: 40px;
            height: 80px;
            z-index: 10;
        }

        .logo-placeholder {
            height: 80px;
            width: 180px;
            background: rgba(43, 110, 212, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            color: #2B6ED4;
            font-size: 14px;
        }

        .certificate-header {
            background: linear-gradient(135deg, #2B6ED4 0%, #1e5bb3 100%);
            color: white;
            padding: 50px 0 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        .certificate-header::before {
            content: "";
            position: absolute;
            top: -50px;
            left: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
        }

        .certificate-header::after {
            content: "";
            position: absolute;
            bottom: -80px;
            right: -80px;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .certificate-title {
            font-family: 'Playfair Display', serif;
            font-size: 38px;
            font-weight: 900;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
            letter-spacing: 1px;
            padding-top: 10px;
        }

        .certificate-subtitle {
            font-size: 16px;
            opacity: 0.95;
            font-weight: 300;
            position: relative;
            z-index: 1;
            max-width: 80%;
            margin: 0 auto;
        }

        .certificate-body {
            padding: 40px 50px;
            text-align: center;
        }

        .certificate-text {
            font-size: 18px;
            line-height: 1.6;
            color: #555;
            margin-bottom: 20px;
        }

        .certificate-user {
            font-size: 32px;
            font-weight: 700;
            color: #2B6ED4;
            margin: 25px 0;
            padding: 12px 0;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
            letter-spacing: 0.5px;
        }

        .certificate-course {
            font-size: 22px;
            color: #333;
            margin-bottom: 30px;
            font-weight: 600;
            padding: 0 40px;
        }

        .certificate-details {
            display: flex;
            justify-content: space-around;
            margin: 40px 0;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }

        .certificate-details::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(43, 110, 212, 0.05);
            border-radius: 10px;
            z-index: -1;
        }

        .detail-item {
            text-align: center;
            margin: 15px;
            padding: 10px 20px;
        }

        .detail-label {
            font-size: 14px;
            color: #777;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .detail-value {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .certificate-footer {
            background: rgba(43, 110, 212, 0.05);
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
        }

        .signature {
            text-align: center;
            flex: 1;
        }

        .signature-line {
            width: 150px;
            height: 1px;
            background: #999;
            margin: 0 auto 8px;
        }

        .signature p {
            font-size: 14px;
            color: #555;
            margin: 5px 0;
        }

        .certificate-number {
            font-size: 13px;
            color: #777;
            letter-spacing: 1px;
            padding: 10px;
            border-left: 1px solid #ddd;
            text-align: center;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 130px;
            font-weight: bold;
            color: rgba(43, 110, 212, 0.03);
            pointer-events: none;
            user-select: none;
            white-space: nowrap;
            font-family: 'Playfair Display', serif;
        }

        .seal {
            position: absolute;
            bottom: 30px;
            right: 30px;
            width: 90px;
            opacity: 0.8;
            filter: drop-shadow(0 2px 5px rgba(0, 0, 0, 0.1));
        }

        .geometric-shape {
            position: absolute;
            opacity: 0.05;
            z-index: 0;
        }

        .shape-1 {
            top: 20%;
            left: 5%;
            width: 100px;
            height: 100px;
            background: #2B6ED4;
            transform: rotate(45deg);
        }

        .shape-2 {
            bottom: 15%;
            right: 10%;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: #2B6ED4;
        }

        @media  print {
            body {
                background: none;
                padding: 0;
            }
            .certificate-container {
                box-shadow: none;
                border: none;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate-inner">
            <!-- Logo -->
            <div class="logo-container">
                <div class="logo-placeholder">Logo du Centre</div>
            </div>

            <!-- Watermark -->
            <div class="watermark">Certificat</div>

            <!-- Decorative shapes -->
            <div class="geometric-shape shape-1"></div>
            <div class="geometric-shape shape-2"></div>

            <!-- Header -->
            <div class="certificate-header">
                <h1 class="certificate-title">CERTIFICAT DE RÉUSSITE</h1>
                <p class="certificate-subtitle">Ce document certifie l'accomplissement avec succès du programme de formation</p>
            </div>

            <!-- Body -->
            <div class="certificate-body">
                <p class="certificate-text">Décerné à</p>
                <h2 class="certificate-user"><?php echo e($user->name); ?></h2>
                <p class="certificate-text">pour avoir complété avec succès la formation</p>
                <h3 class="certificate-course"><?php echo e($training->title); ?></h3>

                <div class="certificate-details">
                    <div class="detail-item">
                        <div class="detail-label">Date d'obtention</div>
                        <div class="detail-value"><?php echo e($certification->obtained_date->format('d/m/Y')); ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Niveau atteint</div>
                        <div class="detail-value">Excellent</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Durée</div>
                        <div class="detail-value"><?php echo e($training->duration); ?> heures</div>
                    </div>
                </div>

                <p class="certificate-text">
                    Ce certificat reconnaît les efforts et les compétences acquises durant cette formation.
                </p>
            </div>

            <!-- Footer -->
            <div class="certificate-footer">
                <div class="signature">
                    <div class="signature-line"></div>
                    <p>Le Directeur Pédagogique</p>
                </div>
                <div class="signature">
                    <div class="signature-line"></div>
                    <p>Le Formateur</p>
                    <div class="detail-value"><?php echo e($training->user->lastname); ?> <?php echo e($training->user->name); ?></div>
                </div>
                <div class="certificate-number">
                    N° <?php echo e($certification->certificate_number); ?>

                </div>
            </div>

            <!-- Seal -->
            <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1MTIgNTEyIj48Y2lyY2xlIGN4PSIyNTYiIGN5PSIyNTYiIHI9IjIyMCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjMkI2RUQ0IiBzdHJva2Utd2lkdGg9IjE1Ii8+PGNpcmNsZSBjeD0iMjU2IiBjeT0iMjU2IiByPSIxODAiIGZpbGw9Im5vbmUiIHN0cm9rZT0iIzJCNkVENCIgc3Ryb2tlLXdpZHRoPSIzIiBzdHJva2UtZGFzaGFycmF5PSI4IDQiLz48dGV4dCB4PSIyNTYiIHk9IjI2MCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjcwIiBmaWxsPSIjMkI2RUQ0IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkb21pbmFudC1iYXNlbGluZT0ibWlkZGxlIj7inJM8L3RleHQ+PC9zdmc+" alt="Seal" class="seal">
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\msi\Desktop\Centre_Formation-main\resources\views/certificates/certificate.blade.php ENDPATH**/ ?>