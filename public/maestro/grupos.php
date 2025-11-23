<?php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
require_once "../../src/Middleware.php";
Middleware::requireRole("maestro");

$pdo = Database::pdo();
$id = Auth::userId();
$nombre_maestro = Auth::nombreCompleto();

// Obtener grupos y conteo
$sql = "SELECT m.grupo, COUNT(*) as total 
        FROM materias m 
        JOIN asignacion_maestro_materia a ON m.id = a.materia_id 
        WHERE a.maestro_id = ? 
        GROUP BY m.grupo ORDER BY m.grupo";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$grupos = $stmt->fetchAll();
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

        <div class="dashboard-grid" style="grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));">
            <?php if(empty($grupos)): ?>
                <p>No tienes grupos asignados aún.</p>
            <?php else: ?>
                <?php foreach($grupos as $g): ?>
                <div class="card" style="text-align:center;">
                    <div class="card-body">
                        <h1 style="font-size:3rem; color:var(--primary); margin:0;"><?php echo htmlspecialchars($g['grupo']); ?></h1>
                        <p style="color:#666; margin-bottom:15px;">GRUPO</p>
                        <span class="badge badge-info"><?php echo $g['total']; ?> Materia(s)</span>
                        <br><br>
                        <a href="materias.php" class="btn btn-primary">Ver / Editar Materias</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>