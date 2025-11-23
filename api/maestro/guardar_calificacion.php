<?php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
header("Content-Type: application/json");

if (!Auth::isLoggedIn() || Auth::rol() !== 'maestro') exit;

$data = json_decode(file_get_contents('php://input'), true);
$maestroId = Auth::userId();

$pdo = Database::pdo();
// UPSERT: Insertar, si ya existe (clave única alumno-materia), actualizar.
$sql = "INSERT INTO calificaciones (alumno_id, materia_id, maestro_id, calificacion) 
        VALUES (?, ?, ?, ?) 
        ON DUPLICATE KEY UPDATE calificacion = VALUES(calificacion), maestro_id = VALUES(maestro_id)";

$stmt = $pdo->prepare($sql);
try {
    $stmt->execute([$data['alumno_id'], $data['materia_id'], $maestroId, $data['calificacion']]);
    echo json_encode(["message" => "Calificación guardada"]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error al guardar"]);
}