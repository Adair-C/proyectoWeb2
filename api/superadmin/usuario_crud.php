<?php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
header("Content-Type: application/json");

if (!Auth::isLoggedIn() || Auth::rol() !== 'superadmin') exit(json_encode(["error" => "No autorizado"]));

$data = json_decode(file_get_contents('php://input'), true);
$method = $_SERVER['REQUEST_METHOD'];
$pdo = Database::pdo();

try {
    if ($method === 'POST') {
        // CREAR USUARIO
        $passHash = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO usuarios (username, password, nombre_completo, email, rol, activo) VALUES (?, ?, ?, ?, ?, 1)");
        $stmt->execute([$data['username'], $passHash, $data['nombre'], $data['email'], $data['rol']]);
        echo json_encode(["message" => "Usuario creado exitosamente"]);

    } elseif ($method === 'PUT') {
        // EDITAR USUARIO
        $id = $data['id'];
        $sql = "UPDATE usuarios SET username=?, nombre_completo=?, email=?, rol=?, activo=? WHERE id=?";
        $params = [$data['username'], $data['nombre'], $data['email'], $data['rol'], $data['activo'], $id];

        // Si mandaron password, lo actualizamos también
        if (!empty($data['password'])) {
            $sql = "UPDATE usuarios SET username=?, nombre_completo=?, email=?, rol=?, activo=?, password=? WHERE id=?";
            $params = [$data['username'], $data['nombre'], $data['email'], $data['rol'], $data['activo'], password_hash($data['password'], PASSWORD_DEFAULT), $id];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo json_encode(["message" => "Usuario actualizado"]);

    } elseif ($method === 'DELETE') {
        // ELIMINAR USUARIO
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$data['id']]);
        echo json_encode(["message" => "Usuario eliminado"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error en BD: " . $e->getMessage()]);
}
?>