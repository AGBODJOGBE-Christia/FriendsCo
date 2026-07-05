<?php
require_once '../../config.php';

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['token']) || empty($data['new_password'])) {
    sendJson(['status' => 'error', 'message' => 'Données manquantes.']);
}

$stmt = $pdo->prepare("SELECT id FROM users WHERE token_validation = ? AND token_expire > NOW()");
$stmt->execute([$data['token']]);
$user = $stmt->fetch();

if ($user) {
    $newHash = password_hash($data['new_password'], PASSWORD_DEFAULT);
    $update = $pdo->prepare("UPDATE users SET password_hash = ?, token_validation = NULL, token_expire = NULL WHERE id = ?");
    $update->execute([$newHash, $user['id']]);
    sendJson(['status' => 'success', 'message' => 'Mot de passe réinitialisé avec succès.']);
} else {
    sendJson(['status' => 'error', 'message' => 'Lien invalide ou expiré.']);
}
?>