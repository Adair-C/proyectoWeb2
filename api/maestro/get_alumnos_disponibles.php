<?php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
header("Content-Type: application/json");

if (!Auth::isLoggedIn() || Auth::rol() !== 'maestro') {
    http_response_code(403);
    exit(json_encode(["error"=>"Acceso denegado"]));
}

$materiaId = $_GET['materia_id'] ?? 0;
$pdo = Database::pdo();

try {
    // 1. Obtener Nombre de la Materia (NUEVO)
    $stmtMat = $pdo->prepare("SELECT nombre FROM materias WHERE id = ?");
    $stmtMat->execute([$materiaId]);
    $nombreMateria = $stmtMat->fetchColumn() ?: "Materia Desconocida";

    // 2. Obtener Inscritos
    $sqlInscritos = "SELECT u.id, u.nombre_completo as nombre FROM usuarios u 
                     JOIN inscripciones i ON u.id = i.alumno_id 
                     WHERE i.materia_id = ? AND u.rol = 'alumno'";
    $stmt = $pdo->prepare($sqlInscritos);
    $stmt->execute([$materiaId]);
    $inscritos = $stmt->fetchAll();

    // 3. Obtener Disponibles
    $sqlDisponibles = "SELECT id, nombre_completo as nombre FROM usuarios 
                       WHERE rol = 'alumno' AND activo = 1 
                       AND id NOT IN (SELECT alumno_id FROM inscripciones WHERE materia_id = ?)";
    $stmt = $pdo->prepare($sqlDisponibles);
    $stmt->execute([$materiaId]);
    $disponibles = $stmt->fetchAll();

    echo json_encode([
        "materia" => $nombreMateria, // Enviamos el nombre aquí
        "inscritos" => $inscritos, 
        "disponibles" => $disponibles
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}