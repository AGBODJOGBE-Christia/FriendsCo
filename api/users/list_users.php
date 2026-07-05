<?php
require_once '../../config.php';

if (!isset($_SESSION['user_id'])) sendJson(['status' => 'error', 'message' => 'Non connecté.']);
$current_id = $_SESSION['user_id'];

// On récupère tous les utilisateurs sauf soi-même, avec le statut de la relation
$sql = "SELECT 
            u.id, u.nom, u.prenom, u.avatar,
            f.status as friendship_status,
            CASE 
                WHEN f.sender_id = ? THEN 'sent'
                WHEN f.receiver_id = ? THEN 'received'
                ELSE 'none'
            END as direction
        FROM users u
        LEFT JOIN friendships f ON (u.id = f.sender_id OR u.id = f.receiver_id) 
            AND (f.sender_id = ? OR f.receiver_id = ?)
        WHERE u.id != ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$current_id, $current_id, $current_id, $current_id, $current_id]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

sendJson(['status' => 'success', 'data' => $users]);
?>