<?php 
require_once "../../src/Auth.php"; 
require_once "../../src/Middleware.php"; 
Middleware::requireRole("maestro"); 

$id = $_GET['id'] ?? 0; 
if(!$id) header("Location: materias.php");

$nombreMaestro = Auth::nombreCompleto();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"> 
    <title>Calificar Materia</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/maestro.css">
</head>
<body>
<div class="dashboard">
    <aside class="dashboard-sidebar">
        <div>
            <div class="sidebar-brand">
                <img src="../assets/img/logo.png" alt="Logo"> 
                <div class="sidebar-brand-text">
                    CONTROL<br>
                <span style="color: #a78bfa;">ESCOLAR</span>
            </div>
        </div>
            <div class="sidebar-user">
                <span>Maestro:</span>
                <strong><?php echo htmlspecialchars($nombreMaestro); ?></strong>
            </div>
            <ul class="sidebar-menu">
                <li><a href="menu.php">Inicio</a></li>
                <li><a href="materias.php" class="active">Gestión de Materias</a></li>
                <li><a href="grupos.php">Mis Grupos</a></li>
                <li><a href="reportes.php" style="color: #FBBF24;">📊 Reportes</a></li>
            </ul>
        </div>
        <div class="sidebar-footer">
            <form action="../logout.php" method="post">
                <button class="sidebar-logout">Cerrar sesión</button>
            </form>
        </div>
    </aside>

    <main class="dashboard-main">
        <input type="hidden" id="page-materia-id" value="<?= $id ?>">

        <div class="card">
            <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
                <h3>Calificando: <span id="materia-titulo">Cargando...</span></h3>
                
                <a href="../../api/maestro/reporte_materia.php?materia_id=<?= $id ?>" 
                   target="_blank" 
                   class="btn btn-primary" 
                   style="background-color: #6366F1; display:flex; align-items:center; gap:5px;">
                   📄 Descargar Lista
                </a>
            </div>

            <div class="card-body table-responsive" style="max-height: none !important; overflow: visible;">
                <table class="custom-table">
                    <thead>
                        <tr id="tabla-head">
                            </tr>
                    </thead>
                    <tbody id="tabla-body">
                        <tr><td>Cargando datos...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script src="../assets/js/maestro-calificaciones.js"></script>
</body>
</html>