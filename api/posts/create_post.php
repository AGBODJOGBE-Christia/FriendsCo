<?php
require_once '../../config.php';

// Vérification de la session utilisateur
if (!isset($_SESSION['user_id'])) {
    sendJson(['status' => 'error', 'message' => 'Non connecté.']);
}

$user_id = $_SESSION['user_id'];
$content = $_POST['content'] ?? '';
$image_path = NULL;

// Gestion de l'upload d'image
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $targetDir = '../../assets/images/';
    if (!is_dir($targetDir)) { mkdir($targetDir, 0777, true); }
    
    $fileInfo = pathinfo($_FILES['image']['name']);
    $extension = strtolower($fileInfo['extension']);
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($extension, $allowedExtensions)) {
        $newFileName = uniqid('post_', true) . '.' . $extension;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetDir . $newFileName)) {
            $image_path = 'assets/images/' . $newFileName;
        }
    }
}

// Insertion du post dans la BDD
$stmt = $pdo->prepare("INSERT INTO posts (user_id, content, image_path) VALUES (?, ?, ?)");
if ($stmt->execute([$user_id, $content, $image_path])) {
    sendJson(['status' => 'success', 'message' => 'Publication créée avec succès.']);
} else {
    sendJson(['status' => 'error', 'message' => 'Erreur lors de la création du post.']);
}
?>