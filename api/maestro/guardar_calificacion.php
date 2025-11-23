<?php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
header("Content-Type: application/json");

if (!Auth::isLoggedIn() || Auth::rol() !== 'maestro') exit;

$data = json_decode(file_get_contents('php://input'), true);
$maestroId = Auth::userId();
$pdo = Database::pdo();

// UPSERT con unidad
$sql = "INSERT INTO calificaciones (alumno_id, materia_id, maestro_id, unidad, calificacion) 
        VALUES (?, ?, ?, ?, ?) 
        ON DUPLICATE KEY UPDATE calificacion = VALUES(calificacion), maestro_id = VALUES(maestro_id)";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $data['alumno_id'], 
        $data['materia_id'], 
        $maestroId, 
        $data['unidad'], 
        $data['calificacion']
    ]);
    echo json_encode(["message" => "Guardado"]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error: " . $e->getMessage()]);
}