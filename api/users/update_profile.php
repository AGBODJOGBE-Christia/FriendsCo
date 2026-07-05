<?php
require_once '../../config.php';

if (!isset($_SESSION['user_id'])) {
    sendJson(['status' => 'error', 'message' => 'Non connecté.']);
}

$user_id = $_SESSION['user_id'];
$nom = $_POST['nom'] ?? '';
$prenom = $_POST['prenom'] ?? '';
$bio = $_POST['bio'] ?? '';
$avatar_path = null;

// Gestion upload avatar
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $targetDir = '../../assets/images/';
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
    
    $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
        $newName = 'user_' . $user_id . '_' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetDir . $newName)) {
            $avatar_path = 'assets/images/' . $newName;
        }
    }
}

// Construction de la requête SQL dynamique
$sql = "UPDATE users SET nom = ?, prenom = ?, bio = ?";
$params = [$nom, $prenom, $bio];

if ($avatar_path) {
    $sql .= ", avatar = ?";
    $params[] = $avatar_path;
}
$sql .= " WHERE id = ?";
$params[] = $user_id;

$stmt = $pdo->prepare($sql);
if ($stmt->execute($params)) {
    sendJson(['status' => 'success', 'message' => 'Profil mis à jour.']);
} else {
    sendJson(['status' => 'error', 'message' => 'Erreur lors de la mise à jour.']);
}
?>