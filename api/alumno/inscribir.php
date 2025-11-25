<?php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
header("Content-Type: application/json");

// 1. Configuración del Límite
$LIMITE_MATERIAS = 7; 

if (!Auth::isLoggedIn() || Auth::rol() !== 'alumno') exit(json_encode(["error" => "Acceso denegado"]));

$data = json_decode(file_get_contents('php://input'), true);
$materiaId = $data['materia_id'] ?? 0;
$alumnoId = Auth::userId();

if(!$materiaId) exit(json_encode(["error" => "Materia inválida"]));

$pdo = Database::pdo();

try {
    // 2. VERIFICAR EL LÍMITE ANTES DE HACER NADA
    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM inscripciones WHERE alumno_id = ?");
    $stmtCount->execute([$alumnoId]);
    $totalInscritas = $stmtCount->fetchColumn();

    if ($totalInscritas >= $LIMITE_MATERIAS) {
        // Aquí detenemos todo si ya llegó al máximo
        echo json_encode(["error" => "Has alcanzado el límite de $LIMITE_MATERIAS materias permitidas."]);
        exit;
    }

    // 3. Verificar si ya está inscrito en esa materia específica (Doble seguridad)
    $check = $pdo->prepare("SELECT id FROM inscripciones WHERE alumno_id = ? AND materia_id = ?");
    $check->execute([$alumnoId, $materiaId]);
    
    if($check->fetch()) {
        echo json_encode(["error" => "Ya estás inscrito en esta materia"]);
        exit;
    }

    // 4. Inscribir
    $stmt = $pdo->prepare("INSERT INTO inscripciones (alumno_id, materia_id) VALUES (?, ?)");
    $stmt->execute([$alumnoId, $materiaId]);
    
    echo json_encode(["message" => "¡Inscripción exitosa!"]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error: " . $e->getMessage()]);
}