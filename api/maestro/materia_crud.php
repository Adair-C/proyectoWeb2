<?php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
header("Content-Type: application/json");

if (!Auth::isLoggedIn() || Auth::rol() !== 'maestro') exit(json_encode(["error" => "No autorizado"]));

$method = $_SERVER['REQUEST_METHOD'];
$pdo = Database::pdo();
$maestroId = Auth::userId();
$input = json_decode(file_get_contents('php://input'), true);

try {
    if ($method === 'POST') {
        $nombre = $input['nombre'];
        $codigo = $input['codigo'];
        $grupo = $input['grupo'];
        $unidades = $input['unidades'];

        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO materias (nombre, codigo, grupo, unidades) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nombre, $codigo, $grupo, $unidades]);
        $materiaId = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO asignacion_maestro_materia (maestro_id, materia_id) VALUES (?, ?)");
        $stmt->execute([$maestroId, $materiaId]);
        $pdo->commit();
        echo json_encode(["message" => "Materia creada"]);

    } elseif ($method === 'PUT') {
        $id = $input['id'];
        $stmt = $pdo->prepare("UPDATE materias SET nombre=?, codigo=?, grupo=?, unidades=? WHERE id=?");
        $stmt->execute([$input['nombre'], $input['codigo'], $input['grupo'], $input['unidades'], $id]);
        echo json_encode(["message" => "Materia actualizada"]);

    } elseif ($method === 'DELETE') {
        $stmt = $pdo->prepare("DELETE FROM materias WHERE id=?");
        $stmt->execute([$input['id']]);
        echo json_encode(["message" => "Materia eliminada"]);
    }
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}