<?php
require_once '../../config.php';

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['email']) || empty($data['password'])) {
    sendJson(['status' => 'error', 'message' => 'Email et mot de passe requis.']);
}

$stmt = $pdo->prepare("SELECT id, nom, prenom, password_hash, role FROM users WHERE email = ?");
$stmt->execute([$data['email']]);
$user = $stmt->fetch();

if ($user && password_verify($data['password'], $user['password_hash'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    // les données sont renvoyées au front pour le sessionStorage
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
    sendJson(['status' => 'error', 'message' => 'Identifiants incorrects.']);
}
?>