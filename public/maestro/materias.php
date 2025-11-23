<?php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
require_once "../../src/Middleware.php";
Middleware::requireRole("maestro");

$pdo = Database::pdo();
$id = Auth::userId();

// Obtenemos las materias del maestro
$stmt = $pdo->prepare("
    SELECT m.* FROM materias m 
    JOIN asignacion_maestro_materia a ON m.id = a.materia_id 
    WHERE a.maestro_id = ? 
    ORDER BY m.nombre ASC
");
$stmt->execute([$id]);
$materias = $stmt->fetchAll();
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
        <div>
            <div class="sidebar-logo">CONTROL <span>ESCOLAR</span></div>
            <div class="sidebar-user">
                <span>Maestro:</span>
                <strong><?php echo htmlspecialchars($nombre_maestro); ?></strong>
            </div>
            <ul class="sidebar-menu">
                <li><a href="menu.php">Inicio</a></li>
                <li><a href="materias.php" class="active">Gestión de Materias</a></li>
                <li><a href="grupos.php">Mis Grupos</a></li>
            </ul>
        </div>
        <div class="sidebar-footer">
            <form action="../logout.php" method="post"><button class="sidebar-logout">Cerrar sesión</button></form>
        </div>
    </aside>

    <main class="dashboard-main">
        <div class="card">
            <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
                <h3>Mis Asignaturas</h3>
                <button class="btn btn-success" onclick="openMateriaModal(false)">+ Nueva Materia</button>
            </div>
            <div class="card-body">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Materia</th>
                            <th>Grupo</th>
                            <th>Unidades</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($materias)): ?>
                            <tr><td colspan="5" style="text-align:center; padding:20px;">No tienes materias registradas.</td></tr>
                        <?php else: ?>
                            <?php foreach($materias as $m): 
                                // Preparamos el objeto JSON para pasarlo al JS
                                $json = htmlspecialchars(json_encode($m), ENT_QUOTES, 'UTF-8');
                            ?>
                            <tr>
                                <td><?php echo $m['codigo']; ?></td>
                                <td><?php echo $m['nombre']; ?></td>
                                <td><span class="badge badge-info"><?php echo $m['grupo']; ?></span></td>
                                <td><?php echo $m['unidades']; ?></td>
                                <td>
                                    <button class="btn btn-primary" onclick="openMateriaModal(true, <?php echo $json; ?>)">Editar</button>
                                    <button class="btn btn-danger" onclick="deleteMateria(<?php echo $m['id']; ?>)">Borrar</button>
                                    
                                    <a href="gestionar_alumnos.php?id=<?php echo $m['id']; ?>" class="btn btn-warning">Alumnos</a>
                                    <a href="materia.php?id=<?php echo $m['id']; ?>" class="btn btn-success">Calificar</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
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
                    
                    <div style="margin-bottom:15px;">
                        <label style="font-weight:bold; display:block; margin-bottom:5px;">Nombre de la Materia</label>
                        <input type="text" name="nombre" id="nombre" class="form-input" style="width:100%; padding:8px;" required>
                    </div>
                    
                    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px;">
                        <div>
                            <label style="font-size:0.9rem;">Código</label>
                            <input type="text" name="codigo" id="codigo" class="form-input" style="width:100%; padding:8px;" required>
                        </div>
                        <div>
                            <label style="font-size:0.9rem;">Grupo</label>
                            <input type="text" name="grupo" id="grupo" class="form-input" style="width:100%; padding:8px;" placeholder="Ej: 5A" required>
                        </div>
                        <div>
                            <label style="font-size:0.9rem;">Unidades</label>
                            <input type="number" name="unidades" id="unidades" class="form-input" style="width:100%; padding:8px;" value="3" min="1" max="10" required>
                        </div>
                    </div>
                    
                    <div id="modal-feedback" class="feedback"></div>
                    <button class="btn btn-primary" style="width:100%; margin-top:20px; padding:10px;">Guardar Datos</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/maestro-app.js"></script>
</body>
</html>