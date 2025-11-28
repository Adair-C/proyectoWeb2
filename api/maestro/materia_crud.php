<?php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";

header("Content-Type: application/json; charset=utf-8");


if (!Auth::isLoggedIn() || Auth::rol() !== 'maestro') {
    http_response_code(403);
    echo json_encode(["error" => "Acceso denegado o no autorizado"]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$pdo = Database::pdo();
$maestroId = Auth::userId();


$input = json_decode(file_get_contents('php://input'), true);

try {
    
    if ($method === 'POST' || $method === 'PUT') {
        
        
        $nombre   = trim($input['nombre'] ?? '');
        $codigo   = trim($input['codigo'] ?? '');
        $grupo    = trim($input['grupo'] ?? '');
        $unidades = $input['unidades'] ?? 0;

        
        
        
        if (empty($nombre) || empty($codigo) || empty($grupo) || empty($unidades)) {
            http_response_code(400);
            throw new Exception("Todos los campos son obligatorios.");
        }

        
        if (!preg_match("/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]+$/", $nombre)) {
            http_response_code(400);
            throw new Exception("El nombre contiene caracteres inválidos.");
        }

        
        if (!preg_match("/^[a-zA-Z0-9]+$/", $codigo)) {
            http_response_code(400);
            throw new Exception("El código solo puede contener letras y números.");
        }

        
        if (!preg_match("/^[a-zA-Z0-9]+$/", $grupo)) {
            http_response_code(400);
            throw new Exception("El grupo solo puede contener letras y números.");
        }

        
        if (!is_numeric($unidades) || $unidades < 1 || $unidades > 10) {
            http_response_code(400);
            throw new Exception("Las unidades deben ser un número entre 1 y 10.");
        }

        

        if ($method === 'POST') {
            
            $pdo->beginTransaction();
            
            
            $stmt = $pdo->prepare("INSERT INTO materias (nombre, codigo, grupo, unidades) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nombre, $codigo, $grupo, $unidades]);
            $materiaId = $pdo->lastInsertId();

            
            $stmt = $pdo->prepare("INSERT INTO asignacion_maestro_materia (maestro_id, materia_id) VALUES (?, ?)");
            $stmt->execute([$maestroId, $materiaId]);
            
            $pdo->commit();
            echo json_encode(["message" => "Materia creada exitosamente"]);

        } elseif ($method === 'PUT') {
            
            $id = $input['id'] ?? null;
            if (!$id) throw new Exception("ID de materia no proporcionado.");

            
            $check = $pdo->prepare("SELECT id FROM asignacion_maestro_materia WHERE maestro_id = ? AND materia_id = ?");
            $check->execute([$maestroId, $id]);
            if (!$check->fetch()) {
                http_response_code(403);
                throw new Exception("No tienes permiso para editar esta materia.");
            }

            $stmt = $pdo->prepare("UPDATE materias SET nombre=?, codigo=?, grupo=?, unidades=? WHERE id=?");
            $stmt->execute([$nombre, $codigo, $grupo, $unidades, $id]);
            echo json_encode(["message" => "Materia actualizada correctamente"]);
        }

    } elseif ($method === 'DELETE') {
        
        $id = $input['id'] ?? null;
        if (!$id) throw new Exception("ID inválido.");

        
        $check = $pdo->prepare("SELECT id FROM asignacion_maestro_materia WHERE maestro_id = ? AND materia_id = ?");
        $check->execute([$maestroId, $id]);
        if (!$check->fetch()) {
            http_response_code(403);
            throw new Exception("No tienes permiso para eliminar esta materia.");
        }

        
        $stmt = $pdo->prepare("DELETE FROM materias WHERE id=?");
        $stmt->execute([$id]);
        echo json_encode(["message" => "Materia eliminada"]);
    }

} catch (Exception $e) {
    
    if ($pdo->inTransaction()) $pdo->rollBack();
    
    
    if (http_response_code() == 200) http_response_code(500);
    
    echo json_encode(["error" => $e->getMessage()]);
}
?>