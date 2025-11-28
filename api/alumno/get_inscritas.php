<?php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
header("Content-Type: application/json");

if (!Auth::isLoggedIn() || Auth::rol() !== 'alumno') {
    exit(json_encode(["error" => "No autorizado"]));
}

$alumnoId = Auth::userId();
$pdo = Database::pdo();

try {
    // Obtenemos materia + nombre del maestro asignado
    $sql = "
        SELECT 
            m.id, m.nombre, m.codigo, m.grupo, m.unidades,
            COALESCE(u.nombre_completo, 'Sin asignar') as maestro
        FROM inscripciones i
        JOIN materias m ON i.materia_id = m.id
        LEFT JOIN asignacion_maestro_materia amm ON m.id = amm.materia_id
        LEFT JOIN usuarios u ON amm.maestro_id = u.id
        WHERE i.alumno_id = ?
        ORDER BY m.nombre ASC
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$alumnoId]);
    
    echo json_encode($stmt->fetchAll());

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}