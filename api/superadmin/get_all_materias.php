<?php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
header("Content-Type: application/json");

if (!Auth::isLoggedIn() || Auth::rol() !== 'superadmin') exit(json_encode([]));

$pdo = Database::pdo();
$stmt = $pdo->query("SELECT * FROM materias ORDER BY nombre ASC");
echo json_encode($stmt->fetchAll());
?>