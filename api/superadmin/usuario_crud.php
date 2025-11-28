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
        
        
        $username = trim($data['username'] ?? '');
        $nombre   = trim($data['nombre'] ?? '');
        $email    = trim($data['email'] ?? '');
        $rol      = trim($data['rol'] ?? '');
        $activo   = $data['activo'] ?? 1;
        $password = trim($data['password'] ?? '');

        
        
        if (empty($username) || empty($nombre) || empty($email)) {
            http_response_code(400); throw new Exception("Campos obligatorios vacíos.");
        }

        
        if (!preg_match("/^[a-zA-Z0-9ñÑ]+$/u", $username)) {
            http_response_code(400); throw new Exception("Usuario inválido (Solo letras y números).");
        }

        
        if (!preg_match("/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u", $nombre)) {
            http_response_code(400); throw new Exception("Nombre inválido.");
        }

        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400); throw new Exception("Email inválido.");
        }

        
        if ($method === 'POST') {
            if (empty($password)) {
                http_response_code(400); throw new Exception("La contraseña es obligatoria al crear.");
            }
            
            $check = $pdo->prepare("SELECT id FROM usuarios WHERE username=? OR email=?");
            $check->execute([$username, $email]);
            if($check->fetch()) { http_response_code(400); throw new Exception("Usuario o email ya existe."); }

            $passHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (username, password, nombre_completo, email, rol, activo) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$username, $passHash, $nombre, $email, $rol, $activo]);
            echo json_encode(["message" => "Usuario creado"]);

        
        } elseif ($method === 'PUT') {
            $id = $data['id'];
            
            
            $sql = "UPDATE usuarios SET username=?, nombre_completo=?, email=?, rol=?, activo=? WHERE id=?";
            $params = [$username, $nombre, $email, $rol, $activo, $id];

            
            if (!empty($password)) {
                $sql = "UPDATE usuarios SET username=?, nombre_completo=?, email=?, rol=?, activo=?, password=? WHERE id=?";
                $params = [$username, $nombre, $email, $rol, $activo, password_hash($password, PASSWORD_DEFAULT), $id];
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            echo json_encode(["message" => "Usuario actualizado"]);
        }

    } elseif ($method === 'DELETE') {
        $id = $data['id'];
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(["message" => "Usuario eliminado"]);
    }

} catch (Exception $e) {
    if (http_response_code() == 200) http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>