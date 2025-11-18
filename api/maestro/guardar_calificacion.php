<?php
// api/maestro/guardar_calificacion.php
header('Content-Type: application/json; charset=utf-8');
require_once '../../src/Database.php';
require_once '../../src/Auth.php';

if (Auth::rol() !== 'maestro' || !Auth::isLoggedIn() || $_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado.']);
    exit;
}

$maestro_id = Auth::userId();
$data = json_decode(file_get_contents('php://input'), true);

$materia_id = $data['materia_id'] ?? null;
$alumno_id = $data['alumno_id'] ?? null;
$calificacion = $data['calificacion'] ?? null;

if (!is_numeric($materia_id) || !is_numeric($alumno_id) || !is_numeric($calificacion) || $calificacion < 0 || $calificacion > 100) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos de calificación inválidos.']);
    exit;
}

$pdo = Database::pdo();

try {
    // Usamos INSERT...ON DUPLICATE KEY UPDATE para insertar o actualizar en una sola consulta
    $stmt = $pdo->prepare('
        INSERT INTO calificaciones (alumno_id, materia_id, maestro_id, calificacion)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
        maestro_id = VALUES(maestro_id),
        calificacion = VALUES(calificacion),
        fecha_registro = CURRENT_TIMESTAMP
    ');
    
    $stmt->execute([$alumno_id, $materia_id, $maestro_id, $calificacion]);
    
    echo json_encode(['success' => true, 'message' => 'Calificación guardada exitosamente.']);
    
} catch (Exception $e) {
    http_response_code(500);
    // Nota: Es mejor no mostrar $e->getMessage() en producción por seguridad
    echo json_encode(['success' => false, 'message' => 'Error al procesar la calificación.']);
}
?>