<?php
require_once "../../src/Auth.php";
require_once "../../src/Middleware.php";
Middleware::requireRole("maestro");
$nombre = Auth::nombreCompleto();
$username = Auth::username();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Materias</title>
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
                <strong><?php echo htmlspecialchars($nombre); ?></strong>
            </div>
            <div class="sidebar-section-title">Navegación</div>
            <ul class="sidebar-menu">
                <li><a href="menu.php">Inicio</a></li>
                <li><a href="materias.php" class="active">Gestión de Materias</a></li>
            </ul>
        </div>
        <div class="sidebar-footer">
            <form action="../logout.php" method="post"><button class="sidebar-logout">Cerrar sesión</button></form>
        </div>
    </aside>

    <main class="dashboard-main">
        <header class="dashboard-header">
            <div class="dashboard-header-title">📂 Gestión de Materias</div>
            <div class="dashboard-clock" id="dashboard-clock">--:--:--</div>
        </header>

        <section class="dashboard-grid" style="grid-template-columns: 1fr;">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Mis Asignaturas</div>
                    <button class="btn-action btn-grade" onclick="openModal(false)">+ Nueva Materia</button>
                </div>
                <div class="card-body">
                    <table class="data-table" width="100%">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Acciones (CRUD)</th>
                                <th>Gestión Académica</th>
                            </tr>
                        </thead>
                        <tbody id="materias-body">
                            </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <div id="modal-materia" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3 id="modal-title">Nueva Materia</h3>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            <form id="form-materia">
                <input type="hidden" name="id" id="materia_id">
                
                <div class="form-row">
                    <label>Nombre de la Materia</label>
                    <input type="text" name="nombre" id="nombre" class="form-input" required>
                </div>
                <div class="form-row">
                    <label>Código</label>
                    <input type="text" name="codigo" id="codigo" class="form-input" required>
                </div>
                
                <div id="modal-feedback" class="feedback"></div>
                
                <button type="submit" class="btn-primary" style="margin-top:15px; width:100%">Guardar</button>
            </form>
        </div>
    </div>

</div>

<script src="../assets/js/maestro-materias.js"></script>

</body>
</html>