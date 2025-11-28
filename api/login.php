<?php

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


if (!preg_match("/^[a-zA-Z0-9ñÑ]+$/", $usernameOrEmail)) {
    echo json_encode(["error" => "El usuario contiene caracteres no permitidos."]);
    exit;
}

try {
    $pdo = Database::pdo();

    
    $stmt = $pdo->prepare("
        SELECT id, username, password, rol, activo, nombre_completo
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

    
    if ((int)$user["activo"] !== 1) {
        echo json_encode([
            "error" => "Tu cuenta está desactivada. Contacta al administrador."
        ]);
        exit;
    }

    
    if (!password_verify($password, $user["password"])) {
        echo json_encode(["error" => "Usuario o contraseña incorrectos"]);
        exit;
    }

    
    Auth::login($user);

    
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
    
    http_response_code(500);
    echo json_encode(["error" => "Error interno en el servidor."]);
}
?>