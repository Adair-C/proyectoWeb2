<?php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
header("Content-Type: application/json");

if (!Auth::isLoggedIn() || Auth::rol() !== 'maestro') exit(json_encode(["error"=>"Acceso denegado"]));

$materiaId = $_GET['materia_id'] ?? 0;
$pdo = Database::pdo();

// 1. Obtener Inscritos
$sqlInscritos = "SELECT u.id, u.nombre_completo as nombre FROM usuarios u 
                 JOIN inscripciones i ON u.id = i.alumno_id 
                 WHERE i.materia_id = ? AND u.rol = 'alumno'";
$stmt = $pdo->prepare($sqlInscritos);
$stmt->execute([$materiaId]);
$inscritos = $stmt->fetchAll();

// 2. Obtener Disponibles (Todos los alumnos - Inscritos)
// Usamos una subconsulta para excluir los que ya están
$sqlDisponibles = "SELECT id, nombre_completo as nombre FROM usuarios 
                   WHERE rol = 'alumno' AND activo = 1 
                   AND id NOT IN (SELECT alumno_id FROM inscripciones WHERE materia_id = ?)";
$stmt = $pdo->prepare($sqlDisponibles);
$stmt->execute([$materiaId]);
$disponibles = $stmt->fetchAll();

echo json_encode(["inscritos" => $inscritos, "disponibles" => $disponibles]);