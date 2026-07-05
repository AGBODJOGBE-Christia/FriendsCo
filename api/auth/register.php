<?php
require_once '../../config.php';
require_once '../utils/send_email.php'; //pour l'envoi d'email

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['nom']) || empty($data['prenom']) || empty($data['email']) || empty($data['password'])) {
    sendJson(['status' => 'error', 'message' => 'Tous les champs sont obligatoires.']);
}

// Vérifier si l'email existe déjà
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$data['email']]);
if ($stmt->fetch()) {
    sendJson(['status' => 'error', 'message' => 'Cet email est déjà utilisé.']);
}

// Hash du mot de passe et génération du token
$hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
$token = bin2hex(random_bytes(32));

$stmt = $pdo->prepare("INSERT INTO users (nom, prenom, email, password_hash, token_validation, role) VALUES (?, ?, ?, ?, ?, 'user')");
if ($stmt->execute([$data['nom'], $data['prenom'], $data['email'], $hashedPassword, $token])) {
    
    // ENVOI DE L'EMAIL DE VALIDATION 
    $verificationLink = "http://localhost/friendsCo/vues/clients/verify_email.html?token=" . $token;

    $htmlContent = "
        <html>
        <body style='font-family: Arial, sans-serif; background: #f4f7f6; padding: 20px;'>
            <div style='max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);'>
                <h2 style='color: #0f172a;'>Bienvenue sur FriendsCo, {$data['prenom']} !</h2>
                <p>Merci de vous être inscrit. Pour activer votre compte, veuillez cliquer sur le bouton ci-dessous :</p>
                <div style='text-align: center; margin: 20px 0;'>
                    <a href='$verificationLink' style='background: #0f172a; color: white; padding: 12px 24px; text-decoration: none; border-radius: 30px; font-weight: bold; display: inline-block;'>Valider mon compte</a>
                </div>
                <p style='color: #64748b; font-size: 13px;'>Si vous n'êtes pas à l'origine de cette inscription, ignorez simplement cet email.</p>
            </div>
        </body>
        </html>
    ";

    $mailResult = sendHtmlEmail($data['email'], $data['prenom'] . ' ' . $data['nom'], 'Bienvenue sur FriendsCo', $htmlContent);
    //  FIN ENVOI 

    sendJson(['status' => 'success', 'message' => 'Inscription réussie. Un email de validation a été envoyé.']);
} else {
    sendJson(['status' => 'error', 'message' => 'Erreur lors de l\'inscription.']);
}
?>