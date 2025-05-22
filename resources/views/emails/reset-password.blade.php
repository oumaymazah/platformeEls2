<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de mot de passe - Empowerment Learning Success</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
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
            background-color: #4361ee;
            height: 10px;
            width: 100%;
        }
        .header {
            background-color: #f8f9fa;
            padding: 25px;
            text-align: center;
            border-bottom: 1px solid #eaeaea;
        }
        .logo {
            margin-bottom: 15px;
        }
        .logo img {
            height: 50px;
        }
        .content {
            padding: 30px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #eaeaea;
        }
        h1 {
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
        .btn {
            display: inline-block;
            background-color: #4361ee;
            color: #ffffff !important;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .btn:hover {
            background-color: #3a56d4;
        }
        .social-links {
            margin-top: 15px;
        }
        .social-links a {
            margin: 0 10px;
            text-decoration: none;
            color: #777;
        }
        .lastname {
            font-weight: bold;
        }
        .important {
            font-weight: bold;
            color: #4361ee;
        }
        .code {
            font-size: 24px;
            letter-spacing: 3px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background-color:#F5F5F5;
            border-radius: 6px;
            color: #333;
        }
        @media only screen and (max-width: 620px) {
            .container {
                width: 100%;
                border-radius: 0;
            }
            .content {
                padding: 20px;
            }
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
                            <td style="vertical-align: middle;"><img src="{{ $message->embed(public_path('img/logo.png')) }}" alt="Logo ELS" style="height: 40px; margin-right: 10px;"></td>
                            <td style="vertical-align: middle;"><h2 style="margin: 0;">EMPOWERMENT LEARNING SUCCESS</h2></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="content">
                <h1><strong>Réinitialisation de votre mot de passe</strong></h1>

                <p>Bonjour <span class="lastname">{{ $lastname }}</span>,</p>
                
                <p>Vous avez demandé à réinitialiser votre mot de passe. Veuillez utiliser le code ci-dessous :</p>
                
                <div class="info-box">
                    <h3 style="margin-top: 0; color: #0f1010; font-size: 18px;">Votre code de réinitialisation</h3>
                    <div class="code">{{ $code }}</div>
                </div>
                
                <center>
                    <a href="verify/code" class="btn">Accéder à la page de réinitialisation</a>
                </center>
                
                <p>Cordialement,<br>
                <strong>L'équipe Empowerment Learning Success</strong></p>
            </div>
            <div class="footer">
                <p>Ceci est un message automatique, merci de ne pas y répondre directement.</p>
                <div class="social-links">
                    <p>Rue Farabi Trocadéro, Immeuble Kraiem 1<sup>er</sup> étage</p>
                    <p>Tél. : 52 450 193 / 21 272 129</p>
                </div>
                <p>© {{ date('Y') }} Empowerment Learning Success. Tous droits réservés.</p>
            </div>
        </div>
    </div>
</body>
</html>