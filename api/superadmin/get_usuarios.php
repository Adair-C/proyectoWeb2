<?php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
header("Content-Type: application/json");

// Solo superadmin puede ver esto
if (!Auth::isLoggedIn() || Auth::rol() !== 'superadmin') exit(json_encode([]));

$pdo = Database::pdo();
$stmt = $pdo->query("SELECT id, username, nombre_completo, email, rol, activo FROM usuarios ORDER BY id DESC");
echo json_encode($stmt->fetchAll());
?>