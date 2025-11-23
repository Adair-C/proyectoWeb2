<?php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
header("Content-Type: application/json");

$materiaId = $_GET['materia_id'];
$pdo = Database::pdo();

// 1. Info Materia
$stmt = $pdo->prepare("SELECT nombre FROM materias WHERE id = ?");
$stmt->execute([$materiaId]);
$materia = $stmt->fetch();

// 2. Alumnos y sus notas (LEFT JOIN con calificaciones)
$sql = "SELECT u.id, u.nombre_completo as nombre, c.calificacion 
        FROM usuarios u 
        JOIN inscripciones i ON u.id = i.alumno_id
        LEFT JOIN calificaciones c ON (c.alumno_id = u.id AND c.materia_id = i.materia_id)
        WHERE i.materia_id = ? AND u.rol = 'alumno'";
$stmt = $pdo->prepare($sql);
$stmt->execute([$materiaId]);

echo json_encode([
    "nombre_materia" => $materia['nombre'],
    "alumnos" => $stmt->fetchAll()
]);