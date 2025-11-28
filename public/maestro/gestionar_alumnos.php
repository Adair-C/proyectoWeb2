<?php
require_once "../../app/Config/Database.php";
require_once "../../app/Helpers/Auth.php";
require_once "../../app/Helpers/Middleware.php";
Middleware::requireRole("maestro");

// Validación básica de ID en URL
$idMateria = $_GET['id'] ?? null;
if(!$idMateria) header("Location: materias.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Inscripciones</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/maestro.css">
</head>
<body>
<div class="dashboard">
    <aside class="dashboard-sidebar">
        <div class="sidebar-logo">CONTROL <span>ESCOLAR</span></div>
        <ul class="sidebar-menu">
            <li><a href="menu.php">Inicio</a></li>
            <li><a href="materias.php">Volver a Materias</a></li>
        </ul>
    </aside>

    <main class="dashboard-main">
        <header class="dashboard-header">
            <div class="dashboard-header-title">Alumnos en: <span id="materia-titulo">...</span></div>
        </header>
        
        <input type="hidden" id="page-materia-id" value="<?= htmlspecialchars($idMateria) ?>">

        <div class="card">
            <div class="card-body inscription-container">
                <div class="student-list-box">
                    <h3>Disponibles</h3>
                    <div id="list-available"></div>
                </div>
                <div class="student-list-box">
                    <h3>Inscritos</h3>
                    <div id="list-enrolled"></div>
                </div>
            </div>
        </div>
    </main>
</div>
<script src="../assets/js/maestro-inscripciones.js"></script>
</body>
</html>