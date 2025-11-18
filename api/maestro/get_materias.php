<?php
// api/maestro/get_materias.php
header('Content-Type: application/json; charset=utf-8');
require_once '../../src/Database.php';
require_once '../../src/Auth.php';

// Asegura que el usuario esté logueado como maestro
if (Auth::rol() !== 'maestro' || !Auth::isLoggedIn()) {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso denegado.']);
    exit;
}

$maestro_id = Auth::userId();
$pdo = Database::pdo();

try {
    // Consulta para obtener las materias asignadas al maestro_id
    $stmt = $pdo->prepare('
        SELECT 
            m.id, 
            m.nombre, 
            m.codigo
        FROM materias m
        JOIN asignacion_maestro_materia amm ON m.id = amm.materia_id
        WHERE amm.maestro_id = ? AND m.activo = 1
        ORDER BY m.nombre
    ');
    $stmt->execute([$maestro_id]);
    
    $materias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($materias);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>