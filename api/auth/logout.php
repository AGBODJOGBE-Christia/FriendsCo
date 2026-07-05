<?php
require_once '../../config.php';
session_destroy();
sendJson(['status' => 'success', 'message' => 'Déconnecté.']);
?>