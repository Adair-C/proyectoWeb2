<?php
require_once "../../app/Config/Database.php";
require_once "../../app/Helpers/Auth.php";
require_once "../../app/Helpers/Middleware.php";
Middleware::requireRole("superadmin");

// 1. Lógica del Dashboard: Obtener conteos rápidos
$pdo = Database::pdo();
$totalAlumnos = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE rol='alumno'")->fetchColumn();
$totalMaestros = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE rol='maestro'")->fetchColumn();
$totalMaterias = $pdo->query("SELECT COUNT(*) FROM materias")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <style>
        .stat-card {
            display: flex; flex-direction: column; justify-content: center;
            text-align: center; padding: 20px; transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-3px); }
        .stat-number { font-size: 2.5rem; font-weight: 800; color: var(--dash-primary); margin: 0; }
        .stat-label { color: #666; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; }
    </style>
</head>
<body>
<div class="dashboard">

    <aside class="dashboard-sidebar">
        <div>
            <div class="sidebar-brand">
            <img src="../assets/img/logo.png" alt="Logo"> 
            <div class="sidebar-brand-text">
                CONTROL<br>
                <span style="color: #a78bfa;">ESCOLAR ADMIN</span>
            </div>
        </div>
            <div class="sidebar-user">
                <span>Sesión:</span>
                <strong>Super Admin</strong>
            </div>
            <div class="sidebar-section-title">Administración</div>
            <ul class="sidebar-menu">
                <li><a href="menu.php" class="active">Inicio</a></li>
                <li><a href="usuarios.php">Usuarios</a></li>
                <li><a href="materias.php">Materias</a></li>
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
        <header class="dashboard-header">
            <div>
                <div class="dashboard-header-title">Panel de Control</div>
                <div class="dashboard-header-subtitle">Vista general del sistema escolar</div>
            </div>
            <div class="dashboard-clock" id="clock">--:--</div>
        </header>

        <div class="dashboard-grid">
            
            <div class="card stat-card">
                <div class="stat-number" style="color: #3B82F6;"><?php echo $totalAlumnos; ?></div>
                <div class="stat-label">Alumnos</div>
            </div>

            <div class="card stat-card">
                <div class="stat-number" style="color: #8B5CF6;"><?php echo $totalMaestros; ?></div>
                <div class="stat-label">Maestros</div>
            </div>

            <div class="card stat-card">
                <div class="stat-number" style="color: #10B981;"><?php echo $totalMaterias; ?></div>
                <div class="stat-label">Materias</div>
            </div>

            <div class="card" style="grid-column: span 1 / -1;"> <div class="card-header">
                    <div class="card-title">Acciones Rápidas</div>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
                        <button class="btn btn-primary" onclick="location.href='usuarios.php'">
                            👥 Administrar Usuarios
                        </button>
                        <button class="btn btn-primary" style="background:#10B981;" onclick="location.href='materias.php'">
                            📚 Catálogo de Materias
                        </button>
                        <button class="btn btn-primary" style="background:#6366F1;" onclick="window.open('../../api/superadmin/reporte_general.php', '_blank')">
                            📊 Generar Reporte PDF
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<script>
    // Reloj simple
    setInterval(() => {
        const now = new Date();
        document.getElementById('clock').textContent = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    }, 1000);
</script>

</body>
</html>