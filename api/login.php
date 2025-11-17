<?php
// api/login.php
require_once "../src/Database.php";
require_once "../src/Auth.php";

header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
if (!is_array($data)) {
    echo json_encode(["error" => "Datos inválidos"]);
    exit;
}

$usernameOrEmail = trim($data["username"] ?? "");
$password        = trim($data["password"] ?? "");

if ($usernameOrEmail === "" || $password === "") {
    echo json_encode(["error" => "Usuario y contraseña son obligatorios"]);
    exit;
}

try {
    $pdo = Database::pdo();

    // Buscar por username O email
    $stmt = $pdo->prepare("
        SELECT id, username, password, rol, activo
        FROM usuarios
        WHERE username = ? OR email = ?
        LIMIT 1
    ");
    $stmt->execute([$usernameOrEmail, $usernameOrEmail]);

    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(["error" => "Usuario o contraseña incorrectos"]);
        exit;
    }

    // Cuenta desactivada
    if ((int)$user["activo"] !== 1) {
        echo json_encode([
            "error" => "Tu cuenta está desactivada. Contacta al administrador."
        ]);
        exit;
    }

    // Verificar contraseña
    if (!password_verify($password, $user["password"])) {
        echo json_encode(["error" => "Usuario o contraseña incorrectos"]);
        exit;
    }

    // Login correcto: crear sesión
    Auth::login($user);

    // Redirección según rol
    $redirect = "";
    switch ($user["rol"]) {
        case "alumno":
            $redirect = "/proyectoWeb2/public/alumno/menu.php";
            break;
        case "maestro":
            $redirect = "/proyectoWeb2/public/maestro/menu.php";
            break;
        case "superadmin":
            $redirect = "/proyectoWeb2/public/superadmin/menu.php";
            break;
    }

    echo json_encode([
        "ok"       => true,
        "rol"      => $user["rol"],
        "redirect" => $redirect
    ]);

} catch (Exception $e) {
    // Para producción mejor no mostrar el mensaje interno:
    // echo json_encode(["error" => "Error en el servidor"]);
    echo json_encode(["error" => "Error en el servidor: " . $e->getMessage()]);
}
