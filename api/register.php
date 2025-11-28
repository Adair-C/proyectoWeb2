<?php
require_once "../src/Database.php";

header("Content-Type: application/json; charset=utf-8");


if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}


$data = json_decode(file_get_contents("php://input"), true);

$username   = trim($data["username"] ?? "");
$password   = trim($data["password"] ?? "");
$password2  = trim($data["password2"] ?? "");
$nombre     = trim($data["nombre"] ?? "");
$email      = trim($data["email"] ?? "");
$rol        = trim($data["rol"] ?? "");


if (empty($username) || empty($password) || empty($nombre) || empty($email) || empty($rol)) {
    echo json_encode(["error" => "Todos los campos son obligatorios"]);
    exit;
}


if (!preg_match("/^[a-zA-Z0-9ñÑ]+$/", $username)) {
    echo json_encode(["error" => "El nombre de usuario solo puede contener letras y números."]);
    exit;
}


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["error" => "El formato del correo electrónico no es válido"]);
    exit;
}


if ($password !== $password2) {
    echo json_encode(["error" => "Las contraseñas no coinciden"]);
    exit;
}


if (!in_array($rol, ["alumno", "maestro"])) {
    echo json_encode(["error" => "Rol no permitido"]);
    exit;
}

try {
    $pdo = Database::pdo();

    
    $query = $pdo->prepare("SELECT id FROM usuarios WHERE username = ? OR email = ?");
    $query->execute([$username, $email]);

    if ($query->fetch()) {
        echo json_encode(["error" => "Usuario o email ya registrado"]);
        exit;
    }

    
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO usuarios (username, password, nombre_completo, email, rol)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $username,
        $hash,
        $nombre,
        $email,
        $rol
    ]);

    echo json_encode(["ok" => true, "msg" => "Usuario registrado correctamente"]);

} catch (Exception $e) {
    
    http_response_code(500);
    echo json_encode(["error" => "Error al registrar: " . $e->getMessage()]);
}
?>