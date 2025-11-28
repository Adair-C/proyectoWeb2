<?php
require_once "../../src/Auth.php";
require_once "../../src/Middleware.php";
Middleware::requireRole("superadmin");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Materias | Admin</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/maestro.css"> </head>
<body>
<div class="dashboard">

    <aside class="dashboard-sidebar">
        <div>
            <div class="sidebar-brand">
            <img src="../assets/img/logo.png" alt="Logo"> 
            <div class="sidebar-brand-text">CONTROL<br><span style="color: #a78bfa;">ESCOLAR</span></div>
        </div>
            <div class="sidebar-user"><span>Sesión:</span><strong>Super Admin</strong></div>
            <div class="sidebar-section-title">Administración</div>
            <ul class="sidebar-menu">
                <li><a href="menu.php">Inicio</a></li>
                <li><a href="usuarios.php">Usuarios</a></li>
                <li><a href="materias.php" class="active">Materias</a></li>
                <li><a href="reportes.php" style="color: #FBBF24;">📊 Reportes</a></li>
            </ul>
        </div>
        <div class="sidebar-footer">
            <form action="../logout.php" method="post"><button class="sidebar-logout">Cerrar sesión</button></form>
        </div>
    </aside>

    <main class="dashboard-main">
        <div class="dashboard-header">
            <div class="dashboard-header-title">📚 Catálogo de Materias</div>
            <button class="btn btn-success" onclick="openModal()">+ Nueva Materia</button>
        </div>

        <div class="card">
            <div class="card-body table-responsive" style="max-height: none !important;">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Grupo</th>
                            <th>Unidades</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-materias">
                        <tr><td colspan="6" style="text-align:center">Cargando...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<div id="modal-materia" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <span id="modal-title">Materia</span>
            <button class="close-modal" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="form-materia">
                <input type="hidden" name="id" id="materiaId">
                
                <label>Nombre Materia</label>
                <input type="text" name="nombre" id="nombre" class="form-input" required style="width:100%; margin-bottom:10px;">

                <div style="display:flex; gap:10px;">
                    <div style="flex:1">
                        <label>Código</label>
                        <input type="text" name="codigo" id="codigo" class="form-input" required style="width:100%;">
                    </div>
                    <div style="flex:1">
                        <label>Grupo</label>
                        <input type="text" name="grupo" id="grupo" class="form-input" required style="width:100%;">
                    </div>
                </div>

                <div style="display:flex; gap:10px; margin-top:10px;">
                    <div style="flex:1">
                        <label>Unidades</label>
                        <input type="number" name="unidades" id="unidades" class="form-input" value="3" required style="width:100%;">
                    </div>
                    <div style="flex:1">
                        <label>Estado</label>
                        <select name="activo" id="activo" class="form-select" style="width:100%;">
                            <option value="1">Activa</option>
                            <option value="0">Inactiva</option>
                        </select>
                    </div>
                </div>
                
                <div id="feedback" class="feedback"></div>
                <button type="submit" class="btn btn-primary" style="width:100%; margin-top:20px;">Guardar</button>
            </form>
        </div>
    </div>
</div>

<script src="../assets/js/superadmin-materias.js"></script>

</body>
</html>