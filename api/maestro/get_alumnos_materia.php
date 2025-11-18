<?php
// api/maestro/get_alumnos_materia.php
header('Content-Type: application/json; charset=utf-8');
require_once '../../src/Database.php';
require_once '../../src/Auth.php';

if (Auth::rol() !== 'maestro' || !Auth::isLoggedIn()) {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso denegado.']);
    exit;
}

$materia_id = $_GET['materia_id'] ?? null;
if (!$materia_id || !is_numeric($materia_id)) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de materia inválido.']);
    exit;
}

$pdo = Database::pdo();

try {
    // 1. Obtener nombre de la materia (para el título de la vista)
    $stmt_materia = $pdo->prepare('SELECT nombre FROM materias WHERE id = ?');
    $stmt_materia->execute([$materia_id]);
    $materia = $stmt_materia->fetch();

    // 2. Obtener alumnos y su calificación
    $stmt_alumnos = $pdo->prepare('
        SELECT 
            u.id, 
            u.nombre_completo AS nombre, 
            c.calificacion
        FROM usuarios u
        JOIN inscripciones i ON u.id = i.alumno_id
        LEFT JOIN calificaciones c ON u.id = c.alumno_id AND i.materia_id = c.materia_id
        WHERE i.materia_id = ? AND u.rol = "alumno" AND u.activo = 1
        ORDER BY u.nombre_completo
    ');
    $stmt_alumnos->execute([$materia_id]);
    
    $alumnos = $stmt_alumnos->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'nombre_materia' => $materia['nombre'] ?? 'Materia Desconocida',
        'alumnos' => $alumnos
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>