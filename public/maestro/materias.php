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
    <title>Gestión de Materias</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/maestro.css">
</head>
<body>
<div class="dashboard">
    <aside class="dashboard-sidebar">
        <div class="sidebar-logo">CONTROL <span>ESCOLAR</span></div>
        <div class="sidebar-user">
            <span>Maestro:</span>
            <strong><?php echo htmlspecialchars($nombre_maestro); ?></strong>
        </div>
        <ul class="sidebar-menu">
            <li><a href="menu.php">Inicio</a></li>
            <li><a href="materias.php" class="active">Materias</a></li>
            <li><a href="grupos.php">Mis Grupos</a></li>
        </ul>
    </aside>

    <main class="dashboard-main">
        <div class="card">
            <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
                <h3>Mis Asignaturas</h3>
                <button class="btn btn-success" onclick="openMateriaModal(false)">+ Nueva Materia</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Materia</th>
                                <th>Grupo</th>
                                <th>Unds.</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="materias-body"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div id="modal-materia" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <span id="modal-title">Materia</span> 
                <button class="close-modal" onclick="closeMateriaModal()">&times;</button>
            </div>
            <div style="padding:20px">
                <form id="form-materia" onsubmit="saveMateria(event)">
                    <input type="hidden" name="id" id="materia_id">
                    
                    <div style="margin-bottom:10px;">
                        <label>Nombre:</label>
                        <input type="text" name="nombre" id="nombre" class="form-input" required>
                    </div>
                    
                    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px">
                        <div>
                            <label>Código:</label>
                            <input type="text" name="codigo" id="codigo" class="form-input" required>
                        </div>
                        <div>
                            <label>Grupo:</label>
                            <input type="text" name="grupo" id="grupo" class="form-input" placeholder="Ej: A" required>
                        </div>
                        <div>
                            <label>Unidades:</label>
                            <input type="number" name="unidades" id="unidades" class="form-input" min="1" max="10" value="3" required>
                        </div>
                    </div>
                    
                    <div id="modal-feedback" class="feedback"></div>
                    
                    <button class="btn btn-primary" style="width:100%; margin-top:15px">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/maestro-materias.js"></script>

</body>
</html>