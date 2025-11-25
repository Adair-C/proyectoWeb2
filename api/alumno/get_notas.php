<?php
// api/alumno/get_notas.php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
header("Content-Type: application/json");

if (!Auth::isLoggedIn() || Auth::rol() !== 'alumno') exit;

$alumnoId = Auth::userId();
$pdo = Database::pdo();

// 1. Materias inscritas
$sqlMaterias = "SELECT m.id, m.nombre, m.grupo, m.unidades 
                FROM inscripciones i
                JOIN materias m ON i.materia_id = m.id
                WHERE i.alumno_id = ?
                ORDER BY m.nombre";
$stmt = $pdo->prepare($sqlMaterias);
$stmt->execute([$alumnoId]);
$materias = $stmt->fetchAll();

// 2. Calificaciones
$sqlNotas = "SELECT materia_id, unidad, calificacion 
             FROM calificaciones 
             WHERE alumno_id = ?";
$stmt = $pdo->prepare($sqlNotas);
$stmt->execute([$alumnoId]);
$notasRaw = $stmt->fetchAll();

// Organizar notas por [materia_id][unidad] -> calificacion
$notas = [];
foreach($notasRaw as $row) {
    $notas[$row['materia_id']][$row['unidad']] = $row['calificacion'];
}

echo json_encode([
    "materias" => $materias,
    "notas" => $notas
]);