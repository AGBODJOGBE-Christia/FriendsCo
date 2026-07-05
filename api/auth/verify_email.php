<?php
require_once '../../config.php';

if (isset($_GET['token'])) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE token_validation = ?");
    $stmt->execute([$_GET['token']]);
    $user = $stmt->fetch();

    if ($user) {
        // Supprimer le token pour marquer comme validé
        $update = $pdo->prepare("UPDATE users SET token_validation = NULL WHERE id = ?");
        $update->execute([$user['id']]);
        echo "<h1>Email validé avec succès ! Vous pouvez vous connecter.</h1>";
    } else {
        echo "<h1>Lien invalide ou expiré.</h1>";
    }
} else {
    echo "<h1>Token manquant.</h1>";
}
?>