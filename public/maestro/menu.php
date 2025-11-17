<?php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
require_once "../../src/Middleware.php";

Middleware::requireRole("maestro");

// Obtener datos básicos del maestro
$pdo = Database::pdo();
$stmt = $pdo->prepare("SELECT username, nombre_completo, email, rol, activo 
                       FROM usuarios WHERE id = ?");
$stmt->execute([Auth::userId()]);
$user = $stmt->fetch();

$nombre = $user["nombre_completo"] ?? Auth::nombreCompleto();
$username = $user["username"];
$rol = strtoupper($user["rol"]);
$activo = (int)$user["activo"] === 1;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Maestro | Control Escolar</title>
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
                <span>Maestro:</span>
                <strong><?php echo htmlspecialchars($nombre); ?></strong>
                <span style="font-size:0.8rem;color:#9CA3AF;">
                    <?php echo htmlspecialchars($username); ?>
                </span>
            </div>

            <div class="sidebar-section-title">Navegación</div>
            <ul class="sidebar-menu">
                <li><a class="active" href="menu.php">Inicio</a></li>
                <li><a href="materias.php">Materias que imparto</a></li>
                <li><a href="grupos.php">Mis grupos</a></li>
                <li><a href="calificar.php">Calificar alumnos</a></li>
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
                <div class="dashboard-header-title">Panel del Maestro</div>
                <div class="dashboard-header-subtitle">
                    Bienvenido, <?php echo htmlspecialchars($nombre); ?>
                </div>
            </div>
            <div class="dashboard-clock" id="dashboard-clock">--:--:--</div>
        </header>

        <section class="dashboard-grid">
            
            <!-- Datos del maestro -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Datos generales</div>
                        <div class="card-subtitle">Información básica</div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="student-name"><?php echo htmlspecialchars($nombre); ?></div>
                    <div class="student-id">
                        Usuario: <?php echo htmlspecialchars($username); ?>
                    </div>
                    <div class="student-role">
                        Rol: <?php echo htmlspecialchars($rol); ?>
                    </div>

                    <div style="margin-top:0.8rem;">
                        <span class="badge <?php echo $activo ? 'badge-success' : 'badge-warning'; ?>">
                            <?php echo $activo ? "CUENTA ACTIVA" : "CUENTA DESACTIVADA"; ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Operaciones del maestro -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Operaciones disponibles</div>
                        <div class="card-subtitle">Accesos rápidos</div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="operations-list">
                        <button class="operation-btn" onclick="window.location.href='materias.php'">
                            Ver materias que imparto
                        </button>

                        <button class="operation-btn" onclick="window.location.href='grupos.php'">
                            Ver mis grupos
                        </button>

                        <button class="operation-btn" onclick="window.location.href='calificar.php'">
                            Calificar alumnos
                        </button>

                        <button class="operation-btn">
                            Reportes académicos (Próximamente)
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
