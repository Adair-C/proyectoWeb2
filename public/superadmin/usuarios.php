<?php
require_once "../../app/Config/Database.php";
require_once "../../app/Helpers/Auth.php";
require_once "../../app/Helpers/Middleware.php";
Middleware::requireRole("superadmin");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios | Admin</title>
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
                <li><a href="usuarios.php" class="active">Usuarios</a></li>
                <li><a href="materias.php">Materias</a></li>
                <li><a href="reportes.php" style="color: #FBBF24;">📊 Reportes</a></li>
            </ul>
        </div>
        <div class="sidebar-footer">
            <form action="../logout.php" method="post"><button class="sidebar-logout">Cerrar sesión</button></form>
        </div>
    </aside>

    <main class="dashboard-main">
        <div class="dashboard-header">
            <div class="dashboard-header-title">👥 Usuarios del Sistema</div>
            <button class="btn btn-success" onclick="openUserModal()">+ Nuevo Usuario</button>
        </div>

        <div class="card">
            <div class="card-body table-responsive" style="max-height: none !important;">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Nombre</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-usuarios">
                        <tr><td colspan="6" style="text-align:center">Cargando...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<div id="modal-user" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <span id="modal-title">Usuario</span>
            <button class="close-modal" onclick="closeUserModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="form-user">
                <input type="hidden" name="id" id="userId">
                
                <label>Nombre Completo</label>
                <input type="text" name="nombre" id="nombre" class="form-input" required style="width:100%; margin-bottom:10px;">

                <label>Usuario (Login)</label>
                <input type="text" name="username" id="username" class="form-input" required style="width:100%; margin-bottom:10px;">

                <label>Email</label>
                <input type="email" name="email" id="email" class="form-input" required style="width:100%; margin-bottom:10px;">

                <label>Rol</label>
                <select name="rol" id="rol" class="form-select" style="width:100%; margin-bottom:10px;">
                    <option value="alumno">Alumno</option>
                    <option value="maestro">Maestro</option>
                    <option value="superadmin">Superadmin</option>
                </select>

                <label>Contraseña <small style="color:#666">(Dejar vacía para no cambiar)</small></label>
                <input type="password" name="password" id="password" class="form-input" style="width:100%; margin-bottom:10px;">

                <label>Estado</label>
                <select name="activo" id="activo" class="form-select" style="width:100%; margin-bottom:15px;">
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </select>
                
                <div id="feedback" class="feedback"></div>
                <button type="submit" class="btn btn-primary" style="width:100%">Guardar</button>
            </form>
        </div>
    </div>
</div>

<script src="../assets/js/superadmin-usuarios.js"></script>

</body>
</html>