<?php
// public/maestro/calificar.php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
require_once "../../src/Middleware.php";

Middleware::requireRole("maestro");
$materiaId = $_GET['materia_id'] ?? null;
$alumnoId = $_GET['alumno_id'] ?? null;

if (!$materiaId || !$alumnoId || !is_numeric($materiaId) || !is_numeric($alumnoId)) {
    header('Location: materias.php'); 
    exit;
}
$nombre_maestro = Auth::nombreCompleto() ?? Auth::username();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Calificar Alumno | Maestro</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <style>
        .feedback-message { padding: 10px; border-radius: 6px; font-weight: 600; margin-top: 15px; }
        .feedback-success { background: #D1FAE5; color: #065F46; border: 1px solid #34D399; }
        .feedback-error { background: #FEE2E2; color: #991B1B; border: 1px solid #F87171; }
    </style>
</head>
<body>

<div class="dashboard">
    <aside class="dashboard-sidebar">
        <div>
            <div class="sidebar-logo">CONTROL <span>ESCOLAR</span></div>
            <div class="sidebar-user"><strong><?php echo htmlspecialchars($nombre_maestro); ?></strong></div>
            <div class="sidebar-section-title">Navegación</div>
            <ul class="sidebar-menu">
                <li><a href="menu.php">Inicio</a></li>
                <li><a href="materias.php">Materias que imparto</a></li>
                <li><a class="active" href="calificar.php">Calificar alumnos</a></li>
            </ul>
        </div>
        <div class="sidebar-footer">
            <form action="../logout.php" method="post"><button class="sidebar-logout" type="submit">Cerrar sesión</button></form>
        </div>
    </aside>

    <main class="dashboard-main">
        <header class="dashboard-header">
            <div class="dashboard-header-title">✍️ Calificar Alumno</div>
            <div class="dashboard-clock" id="dashboard-clock">--:--:--</div>
        </header>

        <section class="dashboard-grid" style="grid-template-columns: 1fr;">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Registro de Calificación</div>
                </div>
                <div class="card-body">
                    <p>Materia ID: <strong><?php echo htmlspecialchars($materiaId); ?></strong> | Alumno ID: <strong><?php echo htmlspecialchars($alumnoId); ?></strong></p>
                    
                    <form id="calificar-form" class="vertical-form">
                        <label for="calificacion" class="form-label">Calificación (0 a 100)</label>
                        <input type="number" id="calificacion" name="calificacion" class="form-input" min="0" max="100" placeholder="Ej. 95" required>
                        
                        <div id="feedback-calificacion"></div>
                        
                        <button type="submit" class="btn-primary" style="margin-top: 1rem; width: 100%;">Guardar Calificación</button>
                    </form>
                    
                    <a href="materia.php?id=<?php echo htmlspecialchars($materiaId); ?>" class="operation-btn" style="width: 100%; text-align: center; margin-top: 10px;">Volver a la lista de alumnos</a>
                </div>
            </div>
        </section>
    </main>
</div>

<script>
    const materiaId = '<?php echo $materiaId; ?>';
    const alumnoId = '<?php echo $alumnoId; ?>';
    const form = document.getElementById('calificar-form');
    const feedback = document.getElementById('feedback-calificacion');
    
    // Cargar reloj
    function updateClock() {
        const el = document.getElementById("dashboard-clock");
        el.textContent = new Date().toLocaleTimeString();
    }
    setInterval(updateClock, 1000);
    updateClock();

    // 3. Manejo del Formulario con AJAX
    form.addEventListener('submit', function(e) {
        e.preventDefault(); 
        
        feedback.innerHTML = '';
        
        const calificacion = document.getElementById('calificacion').value;
        const data = {
            materia_id: materiaId,
            alumno_id: alumnoId,
            calificacion: calificacion
        };

        fetch('../../api/maestro/guardar_calificacion.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                feedback.innerHTML = `<div class="feedback-message feedback-success">${result.message}</div>`;
            } else {
                feedback.innerHTML = `<div class="feedback-message feedback-error">${result.message}</div>`;
            }
        })
        .catch(error => {
            feedback.innerHTML = `<div class="feedback-message feedback-error">Error de red al enviar datos.</div>`;
            console.error('Error AJAX:', error);
        });
    });
</script>
</body>
</html>