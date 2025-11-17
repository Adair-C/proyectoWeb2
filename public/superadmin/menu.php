<?php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
require_once "../../src/Middleware.php";

Middleware::requireRole("superadmin");

$pdo = Database::pdo();

// Puedes sacar info básica del usuario
$stmt = $pdo->prepare("SELECT username, nombre_completo, email, rol, activo 
                       FROM usuarios WHERE id = ?");
$stmt->execute([Auth::userId()]);
$user = $stmt->fetch();

$nombre   = $user["nombre_completo"] ?? Auth::nombreCompleto() ?? Auth::username();
$username = $user["username"] ?? Auth::username();
$rol      = strtoupper($user["rol"] ?? Auth::rol());
$activo   = (int)($user["activo"] ?? 0) === 1;

// Datos rápidos del sistema (conteos)
$counts = [
    "total"   => 0,
    "alumnos" => 0,
    "maestros"=> 0,
    "inactivos" => 0,
];

$stmt = $pdo->query("SELECT COUNT(*) AS c FROM usuarios");
$counts["total"] = (int)$stmt->fetch()["c"];

$stmt = $pdo->query("SELECT COUNT(*) AS c FROM usuarios WHERE rol = 'alumno'");
$counts["alumnos"] = (int)$stmt->fetch()["c"];

$stmt = $pdo->query("SELECT COUNT(*) AS c FROM usuarios WHERE rol = 'maestro'");
$counts["maestros"] = (int)$stmt->fetch()["c"];

$stmt = $pdo->query("SELECT COUNT(*) AS c FROM usuarios WHERE activo = 0");
$counts["inactivos"] = (int)$stmt->fetch()["c"];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Superadmin | Control Escolar</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

<div class="dashboard">
    <!-- Sidebar -->
    <aside class="dashboard-sidebar">
        <div>
            <div class="sidebar-logo">
                CONTROL <span>ESCOLAR</span>
            </div>

            <div class="sidebar-user">
                <span>Superadministrador:</span>
                <strong><?php echo htmlspecialchars($nombre); ?></strong>
                <span style="font-size:0.8rem;color:#9CA3AF;">
                    <?php echo htmlspecialchars($username); ?>
                </span>
            </div>

            <div class="sidebar-section-title">Administración</div>
            <ul class="sidebar-menu">
                <li><a href="menu.php" class="active">Inicio</a></li>
                <li><a href="usuarios.php">Gestión de usuarios</a></li>
                <li><a href="reportes.php">Reportes del sistema</a></li>
                <li><a href="configuracion.php">Configuración general</a></li>
            </ul>
        </div>

        <div class="sidebar-footer">
            <form action="../logout.php" method="post">
                <button class="sidebar-logout" type="submit">Cerrar sesión</button>
            </form>
        </div>
    </aside>

    <!-- Contenido principal -->
    <main class="dashboard-main">
        <header class="dashboard-header">
            <div>
                <div class="dashboard-header-title">Panel del Superadmin</div>
                <div class="dashboard-header-subtitle">
                    Vista general del sistema escolar.
                </div>
            </div>
            <div class="dashboard-clock" id="dashboard-clock">--:--:--</div>
        </header>

        <section class="dashboard-grid">
            <!-- Resumen del sistema -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Resumen rápido</div>
                        <div class="card-subtitle">Usuarios registrados</div>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="info-list">
                        <li>
                            <span>Total de usuarios</span>
                            <span><?php echo $counts["total"]; ?></span>
                        </li>
                        <li>
                            <span>Alumnos</span>
                            <span><?php echo $counts["alumnos"]; ?></span>
                        </li>
                        <li>
                            <span>Maestros</span>
                            <span><?php echo $counts["maestros"]; ?></span>
                        </li>
                        <li>
                            <span>Cuentas inactivas</span>
                            <span><?php echo $counts["inactivos"]; ?></span>
                        </li>
                    </ul>

                    <div style="margin-top:0.9rem;">
                        <span class="badge <?php echo $activo ? 'badge-success' : 'badge-warning'; ?>">
                            <?php echo $activo ? "TU CUENTA ESTÁ ACTIVA" : "TU CUENTA ESTÁ DESACTIVADA"; ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Acciones del superadmin -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Acciones rápidas</div>
                        <div class="card-subtitle">Administración del sistema</div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="operations-list">
                        <button class="operation-btn" type="button" onclick="window.location.href='usuarios.php'">
                            Gestionar usuarios (activar / desactivar / cambiar rol)
                        </button>

                        <button class="operation-btn" type="button" onclick="window.location.href='../register.php'">
                            Registrar nuevo alumno/maestro
                        </button>

                        <button class="operation-btn" type="button" onclick="window.location.href='reportes.php'">
                            Ver reportes de actividad
                        </button>

                        <button class="operation-btn" type="button" onclick="window.location.href='configuracion.php'">
                            Configuración general del sistema
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>

<script>
function updateClock() {
    const el = document.getElementById("dashboard-clock");
    const now = new Date();
    el.textContent = now.toLocaleTimeString();
}
setInterval(updateClock, 1000);
updateClock();
</script>

</body>
</html>
