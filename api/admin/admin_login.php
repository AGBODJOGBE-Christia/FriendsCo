<?php
require_once '../../config.php';

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['email']) || empty($data['password'])) {
    sendJson(['status' => 'error', 'message' => 'Identifiants requis.']);
}

$stmt = $pdo->prepare("SELECT id, nom, prenom, password_hash, role FROM users WHERE email = ? AND (role = 'admin' OR role = 'moderator')");
$stmt->execute([$data['email']]);
$user = $stmt->fetch();

if ($user && password_verify($data['password'], $user['password_hash'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    sendJson([
        'status' => 'success',
        'user' => [
            'id' => $user['id'],
            'nom' => $user['nom'],
            'prenom' => $user['prenom'],
            'role' => $user['role']
        ]
    ]);
} else {
    sendJson(['status' => 'error', 'message' => 'Accès refusé ou identifiants incorrects.']);
}
?>