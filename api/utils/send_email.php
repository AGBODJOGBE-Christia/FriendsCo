<?php
// On inclut l'autoload de Composer (PHPMailer)
require_once '../../vendor/autoload.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendHtmlEmail($toEmail, $toName, $subject, $htmlContent) {
    $mail = new PHPMailer(true);

    try {
        // Configuration du serveur SMTP (Utilisation de Mailtrap pour le test)
        $mail->isSMTP();
        $mail->Host       = 'smtp.mailtrap.io';  // Serveur Mailtrap
        $mail->SMTPAuth   = true;
        $mail->Username   = ''; 
        $mail->Password   = ''; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 2525; // Port donné par Mailtrap 

        // Expéditeur et Destinataire
        $mail->setFrom('no-reply@friendsco.com', 'FriendsCo Network');
        $mail->addAddress($toEmail, $toName);

        // Contenu de l'email (HTML)
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlContent;

        $mail->send();
        return ['status' => true, 'message' => 'Email envoyé avec succès.'];
    } catch (Exception $e) {
        return ['status' => false, 'message' => "Erreur d'envoi : {$mail->ErrorInfo}"];
    }
}
?>