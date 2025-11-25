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
        $stmt = $pdo->prepare("INSERT INTO materias (nombre, codigo, grupo, unidades, activo) VALUES (?, ?, ?, ?, 1)");
        $stmt->execute([$data['nombre'], $data['codigo'], $data['grupo'], $data['unidades']]);
        echo json_encode(["message" => "Materia creada"]);

    } elseif ($method === 'PUT') {
        $stmt = $pdo->prepare("UPDATE materias SET nombre=?, codigo=?, grupo=?, unidades=?, activo=? WHERE id=?");
        $stmt->execute([$data['nombre'], $data['codigo'], $data['grupo'], $data['unidades'], $data['activo'], $data['id']]);
        echo json_encode(["message" => "Materia actualizada"]);

    } elseif ($method === 'DELETE') {
        $stmt = $pdo->prepare("DELETE FROM materias WHERE id=?");
        $stmt->execute([$data['id']]);
        echo json_encode(["message" => "Materia eliminada"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>