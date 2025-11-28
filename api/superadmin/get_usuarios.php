<?php
require_once "../../src/Database.php"; require_once "../../src/Auth.php";
header("Content-Type: application/json");
if (!Auth::isLoggedIn() || Auth::rol() !== 'superadmin') { http_response_code(403); exit; }

$pdo = Database::pdo();
$stmt = $pdo->query("SELECT id, username, nombre_completo, email, rol, activo FROM usuarios ORDER BY id DESC");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>