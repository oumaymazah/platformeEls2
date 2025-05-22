<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rappel de Formation</title>
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
        .info-box p {
            margin: 8px 0;
        }
        .training-item {
            border-bottom: 1px solid #eaeaea;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .training-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
            margin-bottom: 0;
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
                            <td style="vertical-align: middle;"><h2 style="margin: 0;">EMPOWERMENT LEARNING CENTER</h2></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="content">
                <h1><strong>Rappel : Vos formations commencent bientôt</strong></h1>

                <p>Bonjour <strong>{{ $professor->name }} {{ $professor->lastname }}</strong>,</p>
                
                <p>Nous vous rappelons que vous êtes assigné(e) à <strong>{{ count($trainings) }} formation(s)</strong> qui débutent dans <strong>2 jours</strong>, le <strong>{{ $startDate }}</strong> :</p>
                
                <div class="info-box">
                    @foreach($trainings as $index => $training)
                    <div class="training-item">
                        <p><strong>Formation {{ $index + 1 }}: </strong>{{ $training->title }} </p>
                        <p><strong>Date de début :</strong> {{ \Carbon\Carbon::parse($training->start_date)->format('d/m/Y') }}</p>
                        <p><strong>Date de fin :</strong> {{ \Carbon\Carbon::parse($training->end_date)->format('d/m/Y') }}</p>
                    </div>
                    @endforeach
                </div>
                
                <p>{{ $reminderMessage }}</p>
                
                <p>Pour toute information complémentaire concernant vos formations, vous pouvez accéder à votre espace personnel.</p>
                
                <center>
                    <a href="/login" class="btn">Accéder à mon espace personnel</a>
                </center>
                
                <p>Bien cordialement,<br>
                L'équipe de formation ELS</p>
            </div>
            <div class="footer">
                <p>Ceci est un email automatique, merci de ne pas y répondre.</p>
                <div class="social-links">
                    <p>Rue farabi trocadéro, immeuble kraiem 1 étage</p>
                    <p>52450193 / 21272129</p>
                </div>
                <p>© {{ date('Y') }} ELS Formation. Tous droits réservés.</p>
            </div>
        </div>
    </div>
</body>
</html>