<?php
// api/maestro/materia_crud.php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";

header("Content-Type: application/json");

// Verificar sesión
if (!Auth::isLoggedIn() || Auth::rol() !== 'maestro') {
    http_response_code(403);
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$pdo = Database::pdo();
$maestroId = Auth::userId();
$input = json_decode(file_get_contents('php://input'), true);

try {
    switch ($method) {
        case 'POST': // CREAR
            $nombre = trim($input['nombre'] ?? '');
            $codigo = trim($input['codigo'] ?? '');
            
            if (!$nombre || !$codigo) throw new Exception("Datos incompletos");

            $pdo->beginTransaction();
            // 1. Crear materia
            $stmt = $pdo->prepare("INSERT INTO materias (nombre, codigo) VALUES (?, ?)");
            $stmt->execute([$nombre, $codigo]);
            $materiaId = $pdo->lastInsertId();

            // 2. Asignar al maestro automáticamente
            $stmt = $pdo->prepare("INSERT INTO asignacion_maestro_materia (maestro_id, materia_id) VALUES (?, ?)");
            $stmt->execute([$maestroId, $materiaId]);
            
            $pdo->commit();
            echo json_encode(["message" => "Materia creada exitosamente"]);
            break;

        case 'PUT': // EDITAR
            $id = $input['id'] ?? null;
            $nombre = trim($input['nombre'] ?? '');
            $codigo = trim($input['codigo'] ?? '');

            // Verificar que la materia pertenezca al maestro
            $check = $pdo->prepare("SELECT id FROM asignacion_maestro_materia WHERE maestro_id = ? AND materia_id = ?");
            $check->execute([$maestroId, $id]);
            if (!$check->fetch()) throw new Exception("No tienes permiso sobre esta materia");

            $stmt = $pdo->prepare("UPDATE materias SET nombre = ?, codigo = ? WHERE id = ?");
            $stmt->execute([$nombre, $codigo, $id]);
            echo json_encode(["message" => "Materia actualizada"]);
            break;

        case 'DELETE': // ELIMINAR
            $id = $input['id'] ?? null;
            
            // Verificar permiso
            $check = $pdo->prepare("SELECT id FROM asignacion_maestro_materia WHERE maestro_id = ? AND materia_id = ?");
            $check->execute([$maestroId, $id]);
            if (!$check->fetch()) throw new Exception("No tienes permiso");

            // Al borrar materia, las FK con ON DELETE CASCADE borrarán inscripciones y asignaciones
            $stmt = $pdo->prepare("DELETE FROM materias WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(["message" => "Materia eliminada"]);
            break;
    }
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}