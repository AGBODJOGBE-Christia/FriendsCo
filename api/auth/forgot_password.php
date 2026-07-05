<?php
require_once '../../config.php';
require_once '../utils/send_email.php'; //pour l'envoi d'email

$data = json_decode(file_get_contents("php://input"), true);

$stmt = $pdo->prepare("SELECT id, prenom FROM users WHERE email = ?");
$stmt->execute([$data['email']]);
$user = $stmt->fetch();

if ($user) {
    $token = bin2hex(random_bytes(16));
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    $update = $pdo->prepare("UPDATE users SET token_validation = ?, token_expire = ? WHERE id = ?");
    $update->execute([$token, $expiry, $user['id']]);

    //ENVOI DE L'EMAIL DE RÉINITIALISATION 
    $resetLink = "http://localhost/friendsCo/vues/clients/reset_password.html?token=" . $token;

    $htmlContent = "
        <html>
        <body style='font-family: Arial, sans-serif; background: #f4f7f6; padding: 20px;'>
            <div style='max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 10px;'>
                <h2 style='color: #0f172a;'>Réinitialisation de mot de passe</h2>
                <p>Vous avez demandé à réinitialiser votre mot de passe. Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe :</p>
                <div style='text-align: center; margin: 20px 0;'>
                    <a href='$resetLink' style='background: #0f172a; color: white; padding: 12px 24px; text-decoration: none; border-radius: 30px; font-weight: bold; display: inline-block;'>Réinitialiser mon mot de passe</a>
                </div>
                <p style='color: #64748b; font-size: 13px;'>Ce lien est valable pendant 1 heure. Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.</p>
            </div>
        </body>
        </html>
    ";

    sendHtmlEmail($data['email'], $user['prenom'] . ' Utilisateur', 'Réinitialisation mot de passe - FriendsCo', $htmlContent);
    // FIN ENVOI 

    sendJson(['status' => 'success', 'message' => 'Lien de réinitialisation envoyé par email.']);
} else {
    sendJson(['status' => 'error', 'message' => 'Email introuvable.']);
}
?>