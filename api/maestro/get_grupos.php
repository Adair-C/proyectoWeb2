<?php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
header("Content-Type: application/json");

if (!Auth::isLoggedIn() || Auth::rol() !== 'maestro') {
    http_response_code(403);
    exit(json_encode(["error" => "No autorizado"]));
}

$pdo = Database::pdo();
$id = Auth::userId();

try {
    // Obtener grupos y conteo de materias
    $sql = "SELECT m.grupo, COUNT(*) as total 
            FROM materias m 
            JOIN asignacion_maestro_materia a ON m.id = a.materia_id 
            WHERE a.maestro_id = ? 
            GROUP BY m.grupo ORDER BY m.grupo";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $grupos = $stmt->fetchAll();
    
    echo json_encode($grupos);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error del servidor: " . $e->getMessage()]);
}
?>