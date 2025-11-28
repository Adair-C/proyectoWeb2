<?php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
require_once "../../src/Middleware.php";

Middleware::requireRole("alumno");

// Puedes obtener más datos del usuario si quieres
$pdo = Database::pdo();
$stmt = $pdo->prepare("SELECT username, nombre_completo, email, rol, activo FROM usuarios WHERE id = ?");
$stmt->execute([Auth::userId()]);
$user = $stmt->fetch();

$nombre = $user["nombre_completo"] ?? Auth::nombreCompleto() ?? Auth::username();
$username = $user["username"] ?? Auth::username();
$rol = $user["rol"] ?? Auth::rol();
$activo = (int)($user["activo"] ?? 0) === 1;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Menú alumno | Control Escolar</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

<div class="dashboard">
    <!-- Sidebar -->
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
                <span>Alumno:</span>
                <strong><?php echo htmlspecialchars($nombre); ?></strong>
                <span style="font-size:0.8rem;color:#9CA3AF;">
                    <?php echo htmlspecialchars($username); ?>
                </span>
            </div>

            <div class="sidebar-section-title">Navegación</div>
            <ul class="sidebar-menu">
                <li><a href="menu.php" class="active">Inicio</a></li>
                <li><a href="materias.php">Materias</a></li>
                <li><a href="calificaciones.php">Calificaciones</a></li>
                <li><a href="inscripcion.php" style="color:#10B981;">+ Inscribir Materia</a></li>
                <!-- agrega más enlaces según vayas creando vistas -->
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
                <div class="dashboard-header-title">Datos del alumno</div>
                <div class="dashboard-header-subtitle">
                    Bienvenido, <?php echo htmlspecialchars($nombre); ?>.
                </div>
            </div>
            <div class="dashboard-clock" id="dashboard-clock">--:--:--</div>
        </header>

        <section class="dashboard-grid">
            <!-- Columna izquierda -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Datos generales</div>
                        <div class="card-subtitle">Información de tu cuenta</div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="student-name"><?php echo htmlspecialchars($nombre); ?></div>
                    <div class="student-id">
                        Usuario: <?php echo htmlspecialchars($username); ?>
                    </div>
                    <div class="student-role">
                        Rol: <?php echo htmlspecialchars(strtoupper($rol)); ?>
                    </div>

                    <div style="margin-top:0.8rem;">
                        <span class="badge <?php echo $activo ? 'badge-success' : 'badge-warning'; ?>">
                            <?php echo $activo ? "CUENTA ACTIVA" : "CUENTA DESACTIVADA"; ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Columna derecha (estatus / reinscripción / operaciones) -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Operaciones académicas</div>
                        <div class="card-subtitle">
                            Accesos rápidos
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div style="margin-bottom:0.8rem;">
                        <div style="font-size:0.85rem; font-weight:600; margin-bottom:0.25rem;">
                            Estatus académico
                        </div>
                        <ul class="info-list">
                            <li>
                                <span>Estatus</span>
                                <span>VIGENTE</span>
                            </li>
                            <li>
                                <span>Inscrito</span>
                                <span>SÍ</span>
                            </li>
                            <!-- Puedes conectar esto a otra tabla en un futuro -->
                        </ul>
                    </div>

                    <div style="margin-bottom:0.8rem;">
                        <div style="font-size:0.85rem; font-weight:600; margin-bottom:0.25rem;">
                            Reinscripción
                        </div>
                        <ul class="info-list">
                            <li>
                                <span>Periodo actual</span>
                                <span>2025-1</span>
                            </li>
                            <li>
                                <span>Estatus de pago</span>
                                <span><span class="badge badge-success">Sin adeudos</span></span>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <div style="font-size:0.85rem; font-weight:600; margin-bottom:0.25rem;">
                            Operaciones
                        </div>
                        <div class="operations-list">
                            <button class="operation-btn" type="button" onclick="window.location.href='calificaciones.php'">
                                Ver calificaciones
                            </button>
                            <button class="operation-btn" type="button" onclick="window.location.href='materias.php'">
                                Ver materias inscritas
                            </button>
                            <!-- Más opciones a futuro -->
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>

<script>
// Reloj simple
function updateClock() {
    const clock = document.getElementById("dashboard-clock");
    if (!clock) return;
    const now = new Date();
    const h = String(now.getHours()).padStart(2, "0");
    const m = String(now.getMinutes()).padStart(2, "0");
    const s = String(now.getSeconds()).padStart(2, "0");
    clock.textContent = `${h}:${m}:${s}`;
}
setInterval(updateClock, 1000);
updateClock();
</script>

</body>
</html>
