<?php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
header("Content-Type: application/json; charset=utf-8");

if (!Auth::isLoggedIn() || Auth::rol() !== 'maestro') {
    http_response_code(403);
    exit(json_encode(['error' => 'Acceso denegado.']));
}

$maestro_id = Auth::userId();
$pdo = Database::pdo();

try {
    // Seleccionamos TODOS los campos (m.*) para tener grupo y unidades
    $stmt = $pdo->prepare('
        SELECT m.* FROM materias m
        JOIN asignacion_maestro_materia amm ON m.id = amm.materia_id
        WHERE amm.maestro_id = ? AND m.activo = 1
        ORDER BY m.nombre ASC
    ');
    $stmt->execute([$maestro_id]);
    
    $materias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($materias);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>