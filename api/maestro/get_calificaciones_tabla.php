<?php
require_once "../../src/Database.php";
header("Content-Type: application/json");

$materiaId = $_GET['materia_id'];
$pdo = Database::pdo();

// 1. Datos de la materia
$stmt = $pdo->prepare("SELECT nombre, grupo, unidades FROM materias WHERE id = ?");
$stmt->execute([$materiaId]);
$materia = $stmt->fetch();

// 2. Alumnos inscritos
$stmt = $pdo->prepare("
    SELECT u.id, u.nombre_completo 
    FROM usuarios u 
    JOIN inscripciones i ON u.id = i.alumno_id 
    WHERE i.materia_id = ? AND u.rol = 'alumno'
    ORDER BY u.nombre_completo ASC
");
$stmt->execute([$materiaId]);
$alumnos = $stmt->fetchAll();

// 3. Calificaciones existentes
$stmt = $pdo->prepare("SELECT alumno_id, unidad, calificacion FROM calificaciones WHERE materia_id = ?");
$stmt->execute([$materiaId]);
$notasRaw = $stmt->fetchAll();

// Organizar notas para fácil acceso en JS { alumno_id: { unidad: nota } }
$notas = [];
foreach($notasRaw as $row) {
    $notas[$row['alumno_id']][$row['unidad']] = $row['calificacion'];
}

echo json_encode([
    "materia" => $materia,
    "alumnos" => $alumnos,
    "notas" => $notas
]);