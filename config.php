<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Configuration BDD 
define('DB_HOST', 'localhost');
define('DB_NAME', 'friendsCo');
define('DB_USER', 'root');
define('DB_PASS', ''); 

try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Connexion BDD échouée: ' . $e->getMessage()]);
    exit();
}

// Fonction utilitaire pour répondre en JSON
function sendJson($data) {
    echo json_encode($data);
    exit();
}
?>