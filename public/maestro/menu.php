<?php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
require_once "../../src/Middleware.php";

Middleware::requireRole("maestro");

$pdo = Database::pdo();
$stmt = $pdo->prepare("SELECT username, nombre_completo, email, rol, activo FROM usuarios WHERE id = ?");
$stmt->execute([Auth::userId()]);
$user = $stmt->fetch();

// CORRECCIÓN: Si borraste la BD, el usuario de la sesión ya no existe. 
// Forzamos logout para evitar el error "value of type bool".
if (!$user) {
    Auth::logout();
    header("Location: ../login.php");
    exit;
}

$nombre = $user["nombre_completo"];
$username = $user["username"];
$rol = strtoupper($user["rol"]);
$activo = (int)$user["activo"] === 1;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Maestro</title>
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
                <strong><?php echo htmlspecialchars($nombre); ?></strong>
            </div>
            <ul class="sidebar-menu">
                <li><a class="active" href="menu.php">Inicio</a></li>
                <li><a href="materias.php">Gestión de Materias</a></li>
                <li><a href="grupos.php">Mis Grupos</a></li>
            </ul>
        </div>
        <div class="sidebar-footer">
            <form action="../logout.php" method="post">
                <button class="sidebar-logout" type="submit">Cerrar sesión</button>
            </form>
        </div>
    </aside>

    <main class="dashboard-main">
        <header class="dashboard-header">
            <div class="dashboard-header-title">Panel del Maestro</div>
            <div class="dashboard-header-subtitle">Bienvenido, <?php echo htmlspecialchars($nombre); ?></div>
        </header>

        <section class="dashboard-grid">
            <div class="card">
                <div class="card-header"><div class="card-title">Datos generales</div></div>
                <div class="card-body">
                    <div class="student-name"><?php echo htmlspecialchars($nombre); ?></div>
                    <div class="student-id">Usuario: <?php echo htmlspecialchars($username); ?></div>
                    <div style="margin-top:0.8rem;">
                        <span class="badge <?php echo $activo ? 'badge-success' : 'badge-warning'; ?>">
                            <?php echo $activo ? "CUENTA ACTIVA" : "INACTIVA"; ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><div class="card-title">Accesos Rápidos</div></div>
                <div class="card-body">
                    <div class="operations-list">
                        <button class="operation-btn" onclick="window.location.href='materias.php'">Gestión de Materias</button>
                        <button class="operation-btn" onclick="window.location.href='grupos.php'">Ver mis grupos</button>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>
</body>
</html>