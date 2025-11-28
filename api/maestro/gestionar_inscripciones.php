<?php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
header("Content-Type: application/json");


if (!Auth::isLoggedIn() || Auth::rol() !== 'maestro') {
    http_response_code(403);
    exit(json_encode(["error" => "Acceso denegado"]));
}


$input = json_decode(file_get_contents('php://input'), true);


$materiaId = filter_var($input['materia_id'] ?? null, FILTER_VALIDATE_INT);
$alumnoId  = filter_var($input['alumno_id'] ?? null, FILTER_VALIDATE_INT);
$accion    = $input['accion'] ?? '';


if (!$materiaId || !$alumnoId) {
    http_response_code(400);
    exit(json_encode(["error" => "Datos incompletos o IDs inválidos."]));
}

$pdo = Database::pdo();


$checkOwner = $pdo->prepare("SELECT id FROM asignacion_maestro_materia WHERE maestro_id = ? AND materia_id = ?");
$checkOwner->execute([Auth::userId(), $materiaId]);

if (!$checkOwner->fetch()) {
    http_response_code(403);
    exit(json_encode(["error" => "No tienes permiso para modificar esta materia."]));
}

try {
    
    if ($accion === 'inscribir') {
        
        $stmt = $pdo->prepare("INSERT INTO inscripciones (materia_id, alumno_id) VALUES (?, ?)");
        $stmt->execute([$materiaId, $alumnoId]);
        echo json_encode(["message" => "Alumno inscrito correctamente"]);
        
    } elseif ($accion === 'desinscribir') {
        $stmt = $pdo->prepare("DELETE FROM inscripciones WHERE materia_id = ? AND alumno_id = ?");
        $stmt->execute([$materiaId, $alumnoId]);
        echo json_encode(["message" => "Alumno removido de la clase"]);
        
    } else {
        
        http_response_code(400);
        echo json_encode(["error" => "Acción no válida"]);
    }

} catch (Exception $e) {
    
    if ($e->getCode() == 23000) { 
        echo json_encode(["message" => "El alumno ya está inscrito en esta materia."]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error en base de datos: " . $e->getMessage()]);
    }
}
?>