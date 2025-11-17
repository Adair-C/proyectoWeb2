<?php
require_once "../src/Database.php";

header("Content-Type: application/json");

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

// Validaciones
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

    // Validar username y email únicos
    $query = $pdo->prepare("SELECT id FROM usuarios WHERE username = ? OR email = ?");
    $query->execute([$username, $email]);

    if ($query->fetch()) {
        echo json_encode(["error" => "Usuario o email ya registrado"]);
        exit;
    }

    // Hashear contraseña
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Insertar usuario
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
    echo json_encode(["error" => $e->getMessage()]);
}
