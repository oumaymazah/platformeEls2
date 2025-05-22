<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificat de Réussite</title>
    <style>
        /* Définition de la taille de la page pour PDF */
        @page  {
            size: A4;
            margin: 0;
        }

        /* Réinitialisation des marges pour le corps */
        body {
            margin: 0;
            padding: 20px;
            background: none; /* Supprime le fond pour impression */
            font-family: 'Poppins', sans-serif;
            display: block; /* Simplifie le rendu */
        }

        /* Conteneur principal avec hauteur minimale */
        .certificate-container {
            width: 100%;
            max-width: 900px;
            min-height: 1200px; /* Hauteur minimale pour éviter le débordement */
            background: white;
            box-shadow: 0 15px 40px rgba(43, 110, 212, 0.15);
            border-radius: 12px;
            margin: 0 auto;
            position: relative;
            overflow: hidden; /* Contient les éléments absolus */
        }

        .certificate-inner {
            padding: 15px;
            border: 1px solid rgba(43, 110, 212, 0.08);
            border-radius: 8px;
            position: relative;
        }

        /* Logo simplifié */
        .logo-container {
            position: absolute;
            top: 30px;
            left: 40px;
            height: 80px;
            width: 180px; /* Largeur explicite */
            z-index: 10;
        }

        .logo-placeholder {
            height: 100%;
            width: 100%;
            background: rgba(43, 110, 212, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            color: #2B6ED4;
            font-size: 14px;
        }

        /* En-tête simplifié */
        .certificate-header {
            background: #2B6ED4; /* Couleur solide au lieu de dégradé */
            color: white;
            padding: 50px 0 40px;
            text-align: center;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        .certificate-title {
            font-family: 'Playfair Display', serif;
            font-size: 38px;
            font-weight: 900;
            margin-bottom: 10px;
            letter-spacing: 1px;
            padding-top: 10px;
        }

        .certificate-subtitle {
            font-size: 16px;
            opacity: 0.95;
            font-weight: 300;
            max-width: 80%;
            margin: 0 auto;
        }

        /* Corps du certificat */
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

        /* Pied de page simplifié */
        .certificate-footer {
            background: rgba(43, 110, 212, 0.05);
            padding: 30px;
            text-align: center;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
        }

        .signature {
            margin: 0 15px;
            display: inline-block;
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
            margin-top: 15px;
        }

        /* Sceau avec taille fixe */
        .seal {
            position: absolute;
            bottom: 30px;
            right: 30px;
            width: 90px;
            height: 90px; /* Hauteur explicite */
            opacity: 0.8;
            filter: drop-shadow(0 2px 5px rgba(0, 0, 0, 0.1));
        }

        /* Police Google Fonts */
        @import  url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@700;900&display=swap');

        /* Styles d'impression */
        @media  print {
            body {
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

            <!-- En-tête -->
            <div class="certificate-header">
                <h1 class="certificate-title">CERTIFICAT DE RÉUSSITE</h1>
                <p class="certificate-subtitle">Ce document certifie l'accomplissement avec succès du programme de formation</p>
            </div>

            <!-- Corps -->
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

            <!-- Pied de page -->
            <div class="certificate-footer">
                <div class="signature">
                    <div class="signature-line"></div>
                    <p>Le Directeur Pédagogique</p>
                </div>
                <div class="signature">
                    <div class="signature-line"></div>
                    <p>Le Formateur</p>
                </div>
                <div class="certificate-number">
                    N° <?php echo e($certification->certificate_number); ?>

                </div>
            </div>

            <!-- Sceau -->
            
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\msi\Desktop\Centre_Formation-main\resources\views/certificates/certificate.blade.php ENDPATH**/ ?>