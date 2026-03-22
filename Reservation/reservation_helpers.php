<?php

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function formatPriceFcfa($amount)
{
    return number_format((float)$amount, 0, ',', ' ') . ' FCFA';
}

function generateQrToken($reservationNumber, $telephone, $idVoyage, $seat)
{
    return hash('sha256', $reservationNumber . '|' . $telephone . '|' . $idVoyage . '|' . $seat . '|' . uniqid('', true));
}

function buildTicketQrUrl($qrToken)
{
    // Mets ici l’URL publique de ton site
    // Exemple en local :
    // return 'http://localhost/ton_projet/verify_ticket.php?token=' . urlencode($qrToken);

    return 'http://localhost/poo-en-php/verify_ticket.php?token=' . urlencode($qrToken);
}

function generateInvoicePdf(
    $nom,
    $prenom,
    $telephone,
    $email,
    $reservationNumber,
    $numeroSiege,
    $depart,
    $arrivee,
    $date,
    $idVoyage,
    $prix,
    $qrUrl
) {
    $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator('Easy Travel');
    $pdf->SetAuthor('Easy Travel');
    $pdf->SetTitle('Billet de réservation');
    $pdf->SetSubject('Facture de réservation');
    $pdf->SetMargins(12, 12, 12);
    $pdf->SetAutoPageBreak(true, 12);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->AddPage();

    $nomComplet = e($nom . ' ' . $prenom);
    $email = e($email);
    $telephone = e($telephone);
    $depart = e($depart);
    $arrivee = e($arrivee);
    $date = e($date);
    $reservationNumber = e($reservationNumber);
    $numeroSiege = e($numeroSiege);
    $idVoyage = e($idVoyage);
    $prixFormatte = e(formatPriceFcfa($prix));
    $dateGeneration = date('d/m/Y à H:i');

    $html = '
    <style>
        .title {
            text-align: center;
            color: #16a34a;
            font-size: 24px;
            font-weight: bold;
        }
        .subtitle {
            text-align: center;
            color: #4b5563;
            font-size: 10px;
        }
        .section-title {
            background-color: #dcfce7;
            color: #166534;
            font-weight: bold;
            padding: 8px;
            font-size: 12px;
        }
        .box {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 10px;
        }
        .label {
            color: #374151;
            font-weight: bold;
            width: 40%;
        }
        .value {
            color: #111827;
            width: 60%;
        }
        .table {
            border: 1px solid #e5e7eb;
        }
        .table td {
            border-bottom: 1px solid #e5e7eb;
            padding: 8px;
            font-size: 11px;
        }
        .total-box {
            background-color: #16a34a;
            color: white;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            padding: 10px;
            border-radius: 8px;
        }
        .small {
            font-size: 9px;
            color: #6b7280;
        }
        .footer-note {
            text-align: center;
            color: #374151;
            font-size: 11px;
        }
    </style>

    <div class="title">Facture de Réservation</div>
    <div class="subtitle">Easy Travel - Billet passager</div>
    <br>

    <table width="100%" cellpadding="6">
        <tr>
            <td width="62%">
                <div class="section-title">Informations du passager</div>
                <div class="box">
                    <table width="100%" class="table" cellpadding="5">
                        <tr>
                            <td class="label">Nom complet</td>
                            <td class="value">' . $nomComplet . '</td>
                        </tr>
                        <tr>
                            <td class="label">Email</td>
                            <td class="value">' . $email . '</td>
                        </tr>
                        <tr>
                            <td class="label">Téléphone</td>
                            <td class="value">' . $telephone . '</td>
                        </tr>
                    </table>
                </div>

                <br>

                <div class="section-title">Détails du voyage</div>
                <div class="box">
                    <table width="100%" class="table" cellpadding="5">
                        <tr>
                            <td class="label">Départ</td>
                            <td class="value">' . $depart . '</td>
                        </tr>
                        <tr>
                            <td class="label">Arrivée</td>
                            <td class="value">' . $arrivee . '</td>
                        </tr>
                        <tr>
                            <td class="label">Date du voyage</td>
                            <td class="value">' . $date . '</td>
                        </tr>
                        <tr>
                            <td class="label">ID Voyage</td>
                            <td class="value">' . $idVoyage . '</td>
                        </tr>
                        <tr>
                            <td class="label">N° Réservation</td>
                            <td class="value">' . $reservationNumber . '</td>
                        </tr>
                        <tr>
                            <td class="label">N° Siège</td>
                            <td class="value">' . $numeroSiege . '</td>
                        </tr>
                    </table>
                </div>
            </td>

            <td width="38%" align="center">
                <div class="section-title">Contrôle d’accès</div>
                <div class="box">
                    <div class="small">Présentez ce QR code à l’agence pour vérification.</div>
                    <br><br><br><br><br><br>
                    <div class="small">Réservation : ' . $reservationNumber . '</div>
                    <div class="small">Émis le : ' . e($dateGeneration) . '</div>
                </div>
            </td>
        </tr>
    </table>

    <br><br>

    <div class="total-box">Total payé : ' . $prixFormatte . '</div>

    <br><br>

    <div class="footer-note">
        Merci pour votre confiance.<br>
        Nous vous souhaitons un excellent voyage avec Easy Travel.
    </div>
    ';

    $pdf->writeHTML($html, true, false, true, false, '');

    // Positionnement du QR code dans le bloc de droite
    $style = [
        'border' => false,
        'padding' => 0,
        'fgcolor' => [0, 0, 0],
        'bgcolor' => false
    ];

    $pdf->write2DBarcode($qrUrl, 'QRCODE,H', 145, 72, 38, 38, $style, 'N');

    return $pdf->Output('', 'S');
}

function buildReservationEmailHtml(
    $nom,
    $prenom,
    $telephone,
    $reservationNumber,
    $numeroSiege,
    $depart,
    $arrivee,
    $date,
    $idVoyage,
    $prix
) {
    $nomComplet = e($nom . ' ' . $prenom);
    $telephone = e($telephone);
    $reservationNumber = e($reservationNumber);
    $numeroSiege = e($numeroSiege);
    $depart = e($depart);
    $arrivee = e($arrivee);
    $date = e($date);
    $idVoyage = e($idVoyage);
    $prix = e(formatPriceFcfa($prix));

    return '
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Confirmation de réservation</title>
    </head>
    <body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial,Helvetica,sans-serif;">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f3f4f6;padding:30px 0;">
            <tr>
                <td align="center">
                    <table role="presentation" width="700" cellspacing="0" cellpadding="0" style="background:#ffffff;border-radius:12px;overflow:hidden;">
                        <tr>
                            <td style="background:#16a34a;padding:28px 24px;text-align:center;color:#ffffff;">
                                <h1 style="margin:0;font-size:28px;">Easy Travel</h1>
                                <p style="margin:8px 0 0 0;font-size:14px;">Confirmation de votre réservation</p>
                            </td>
                        </tr>

                        <tr>
                            <td style="padding:30px 28px;">
                                <h2 style="margin:0 0 12px 0;color:#111827;font-size:24px;">Bonjour ' . $nomComplet . ',</h2>
                                <p style="margin:0 0 20px 0;color:#4b5563;font-size:15px;line-height:1.6;">
                                    Votre réservation a bien été enregistrée. Vous trouverez votre facture en pièce jointe.
                                </p>

                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
                                    <tr>
                                        <td colspan="2" style="background:#dcfce7;color:#166534;font-weight:bold;padding:12px;font-size:15px;">
                                            Détails de la réservation
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px;border-bottom:1px solid #e5e7eb;color:#374151;font-weight:bold;">N° Voyage</td>
                                        <td style="padding:12px;border-bottom:1px solid #e5e7eb;color:#111827;">' . $idVoyage . '</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px;border-bottom:1px solid #e5e7eb;color:#374151;font-weight:bold;">Passager</td>
                                        <td style="padding:12px;border-bottom:1px solid #e5e7eb;color:#111827;">' . $nomComplet . '</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px;border-bottom:1px solid #e5e7eb;color:#374151;font-weight:bold;">Téléphone</td>
                                        <td style="padding:12px;border-bottom:1px solid #e5e7eb;color:#111827;">' . $telephone . '</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px;border-bottom:1px solid #e5e7eb;color:#374151;font-weight:bold;">Référence</td>
                                        <td style="padding:12px;border-bottom:1px solid #e5e7eb;color:#111827;">' . $reservationNumber . '</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px;border-bottom:1px solid #e5e7eb;color:#374151;font-weight:bold;">Siège</td>
                                        <td style="padding:12px;border-bottom:1px solid #e5e7eb;color:#111827;">' . $numeroSiege . '</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px;border-bottom:1px solid #e5e7eb;color:#374151;font-weight:bold;">Départ</td>
                                        <td style="padding:12px;border-bottom:1px solid #e5e7eb;color:#111827;">' . $depart . '</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px;border-bottom:1px solid #e5e7eb;color:#374151;font-weight:bold;">Arrivée</td>
                                        <td style="padding:12px;border-bottom:1px solid #e5e7eb;color:#111827;">' . $arrivee . '</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px;border-bottom:1px solid #e5e7eb;color:#374151;font-weight:bold;">Date</td>
                                        <td style="padding:12px;border-bottom:1px solid #e5e7eb;color:#111827;">' . $date . '</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px;color:#374151;font-weight:bold;">Montant payé</td>
                                        <td style="padding:12px;color:#16a34a;font-weight:bold;">' . $prix . '</td>
                                    </tr>
                                </table>

                                <p style="margin:24px 0 0 0;color:#4b5563;font-size:14px;line-height:1.6;">
                                    Merci pour votre confiance. Conservez bien cet email ainsi que votre facture PDF.
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <td style="background:#f9fafb;padding:18px 28px;text-align:center;color:#6b7280;font-size:13px;">
                                © Easy Travel - Bon voyage
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>
    ';
}