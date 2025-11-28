<?php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
header("Content-Type: application/json");

if (!Auth::isLoggedIn() || Auth::rol() !== 'superadmin') exit(json_encode([]));

$pdo = Database::pdo();
// Obtenemos solo ID y Nombre de los que son maestros activos
$stmt = $pdo->query("SELECT id, nombre_completo FROM usuarios WHERE rol = 'maestro' AND activo = 1 ORDER BY nombre_completo ASC");
echo json_encode($stmt->fetchAll());
?>