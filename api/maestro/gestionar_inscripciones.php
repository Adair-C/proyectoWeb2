<?php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
header("Content-Type: application/json");

if (!Auth::isLoggedIn() || Auth::rol() !== 'maestro') exit(json_encode(["error"=>"Acceso denegado"]));

$data = json_decode(file_get_contents('php://input'), true);
$materiaId = $data['materia_id'];
$alumnoId = $data['alumno_id'];
$accion = $data['accion']; 

$pdo = Database::pdo();

try {
    if ($accion === 'inscribir') {
        $stmt = $pdo->prepare("INSERT INTO inscripciones (materia_id, alumno_id) VALUES (?, ?)");
        $stmt->execute([$materiaId, $alumnoId]);
        echo json_encode(["message" => "Alumno inscrito correctamente"]);
    } else {
        $stmt = $pdo->prepare("DELETE FROM inscripciones WHERE materia_id = ? AND alumno_id = ?");
        $stmt->execute([$materiaId, $alumnoId]);
        echo json_encode(["message" => "Alumno removido de la clase"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error en base de datos: " . $e->getMessage()]);
}