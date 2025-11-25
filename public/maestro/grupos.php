<?php
require_once "../../src/Auth.php";
require_once "../../src/Middleware.php";
Middleware::requireRole("maestro");

$nombre_maestro = Auth::nombreCompleto();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Grupos</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/maestro.css">
</head>
<body>
<div class="dashboard">
    <aside class="dashboard-sidebar">
        <div>
            <div class="sidebar-logo">CONTROL <span>ESCOLAR</span></div>
            <div class="sidebar-user">
                <span>Maestro:</span>
                <strong><?php echo htmlspecialchars($nombre_maestro); ?></strong>
            </div>
            <ul class="sidebar-menu">
                <li><a href="menu.php">Inicio</a></li>
                <li><a href="materias.php">Gestión de Materias</a></li>
                <li><a href="grupos.php" class="active">Mis Grupos</a></li>
            </ul>
        </div>
        <div class="sidebar-footer">
            <form action="../logout.php" method="post"><button class="sidebar-logout">Cerrar sesión</button></form>
        </div>
    </aside>

    <main class="dashboard-main">
        <header class="dashboard-header">
            <div class="dashboard-header-title">👥 Mis Grupos</div>
        </header>

        <div id="grupos-container" class="dashboard-grid" style="grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
            </div>
    </main>
</div>

<script src="../assets/js/maestro-grupos.js"></script>
</body>
</html>