<?php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";

header("Content-Type: application/json; charset=utf-8");


if (!Auth::isLoggedIn() || Auth::rol() !== 'superadmin') {
    http_response_code(403);
    exit(json_encode(["error" => "No autorizado"]));
}

$data = json_decode(file_get_contents('php://input'), true);
$method = $_SERVER['REQUEST_METHOD'];
$pdo = Database::pdo();

try {
    if ($method === 'POST' || $method === 'PUT') {
        
        
        $nombre   = trim($data['nombre'] ?? '');
        $codigo   = trim($data['codigo'] ?? '');
        $grupo    = trim($data['grupo'] ?? '');
        $unidades = $data['unidades'] ?? 0;
        $activo   = $data['activo'] ?? 1;

        
        
        
        if (empty($nombre) || empty($codigo) || empty($grupo) || empty($unidades)) {
            http_response_code(400);
            throw new Exception("Todos los campos son obligatorios.");
        }

        
        if (!preg_match("/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s]+$/u", $nombre)) {
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
            
            $check = $pdo->prepare("SELECT id FROM materias WHERE codigo = ? AND grupo = ?");
            $check->execute([$codigo, $grupo]);
            if ($check->fetch()) {
                http_response_code(400);
                throw new Exception("Ya existe una materia con ese Código y Grupo.");
            }

            $stmt = $pdo->prepare("INSERT INTO materias (nombre, codigo, grupo, unidades, activo) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $codigo, $grupo, $unidades, $activo]);
            echo json_encode(["message" => "Materia creada exitosamente"]);

        } elseif ($method === 'PUT') {
            $id = filter_var($data['id'] ?? null, FILTER_VALIDATE_INT);
            if (!$id) throw new Exception("ID de materia no válido.");

            $stmt = $pdo->prepare("UPDATE materias SET nombre=?, codigo=?, grupo=?, unidades=?, activo=? WHERE id=?");
            $stmt->execute([$nombre, $codigo, $grupo, $unidades, $activo, $id]);
            echo json_encode(["message" => "Materia actualizada correctamente"]);
        }

    } elseif ($method === 'DELETE') {
        $id = filter_var($data['id'] ?? null, FILTER_VALIDATE_INT);
        if (!$id) {
            http_response_code(400);
            throw new Exception("ID inválido.");
        }

        $stmt = $pdo->prepare("DELETE FROM materias WHERE id=?");
        $stmt->execute([$id]);
        echo json_encode(["message" => "Materia eliminada"]);
    }

} catch (Exception $e) {
    
    if (http_response_code() == 200) http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>