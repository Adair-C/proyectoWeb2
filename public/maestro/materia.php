<?php 
require_once "../../src/Auth.php"; require_once "../../src/Middleware.php"; 
Middleware::requireRole("maestro"); 
$id=$_GET['id']; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"> <title>Calificar</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/maestro.css">
</head>
<body>
<div class="dashboard">
    <aside class="dashboard-sidebar">
        <div class="sidebar-logo">CONTROL <span>ESCOLAR</span></div>
        <ul class="sidebar-menu"><li><a href="materias.php">Volver</a></li></ul>
    </aside>
    <main class="dashboard-main">
        <input type="hidden" id="page-materia-id" value="<?= $id ?>">

        <div class="card">
            <div class="card-header"><h3>Calificando: <span id="materia-titulo">...</span></h3></div>
            <div class="card-body table-responsive">
                <table class="custom-table">
                    <thead><tr id="tabla-head"><th>Alumno</th></tr></thead>
                    <tbody id="tabla-body"></tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<script src="../assets/js/maestro-calificaciones.js"></script>
</body>
</html>