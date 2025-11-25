<?php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
header("Content-Type: application/json");

if (!Auth::isLoggedIn() || Auth::rol() !== 'alumno') exit(json_encode(["error" => "No autorizado"]));

$alumnoId = Auth::userId();
$pdo = Database::pdo();

try {
    // Seleccionar materias donde el ID NO esté en la lista de inscripciones de este alumno
    $sql = "
        SELECT 
            m.id, m.nombre, m.codigo, m.grupo, m.unidades,
            COALESCE(u.nombre_completo, 'Sin asignar') as maestro
        FROM materias m
        LEFT JOIN asignacion_maestro_materia amm ON m.id = amm.materia_id
        LEFT JOIN usuarios u ON amm.maestro_id = u.id
        WHERE m.activo = 1 
        AND m.id NOT IN (
            SELECT materia_id FROM inscripciones WHERE alumno_id = ?
        )
        ORDER BY m.nombre ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$alumnoId]);
    echo json_encode($stmt->fetchAll());

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}