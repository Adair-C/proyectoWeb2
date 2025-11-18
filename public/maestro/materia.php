<?php
// public/maestro/materia.php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
require_once "../../src/Middleware.php";

Middleware::requireRole("maestro");
$materiaId = $_GET['id'] ?? null;
if (!$materiaId || !is_numeric($materiaId)) {
    // Si no hay ID, redirige al listado
    header('Location: materias.php');
    exit;
}
$nombre_maestro = Auth::nombreCompleto() ?? Auth::username();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Alumnos de Materia | Maestro</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
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
                <li><a class="active" href="#">Detalle</a></li>
            </ul>
        </div>
        <div class="sidebar-footer">
            <form action="../logout.php" method="post"><button class="sidebar-logout" type="submit">Cerrar sesión</button></form>
        </div>
    </aside>

    <main class="dashboard-main">
        <header class="dashboard-header">
            <div>
                <div class="dashboard-header-title">Alumnos en <span id="materia-nombre">...</span></div>
                <div class="dashboard-header-subtitle">Lista de estudiantes inscritos.</div>
            </div>
            <div class="dashboard-clock" id="dashboard-clock">--:--:--</div>
        </header>

        <section class="dashboard-grid" style="grid-template-columns: 1fr;">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Alumnos y Calificaciones</div>
                    <div><a href="materias.php" class="operation-btn" style="width: auto;">&#9664; Volver a Materias</a></div>
                </div>
                <div class="card-body">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID Alumno</th>
                                <th>Nombre del Alumno</th>
                                <th>Calificación Actual</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="alumnos-list">
                            <tr><td colspan="4">Cargando alumnos...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</div>

<script>
    const materiaId = '<?php echo $materiaId; ?>';

    document.addEventListener('DOMContentLoaded', function() {
        // Cargar reloj
        function updateClock() {
            const el = document.getElementById("dashboard-clock");
            el.textContent = new Date().toLocaleTimeString();
        }
        setInterval(updateClock, 1000);
        updateClock();
        
        // 2. Llamada AJAX para obtener alumnos de la materia
        fetch(`../../api/maestro/get_alumnos_materia.php?materia_id=${materiaId}`)
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('alumnos-list');
                tbody.innerHTML = ''; // Limpiar mensaje de carga

                if (data.error) {
                    document.getElementById('materia-nombre').textContent = 'Error';
                    tbody.innerHTML = `<tr><td colspan="4" class="text-danger">Error: ${data.error}</td></tr>`;
                    return;
                }
                
                document.getElementById('materia-nombre').textContent = data.nombre_materia;

                if (data.alumnos.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="4">No hay alumnos inscritos en esta materia.</td></tr>`;
                    return;
                }
                
                data.alumnos.forEach(alumno => {
                    const row = tbody.insertRow();
                    const calif = alumno.calificacion ? alumno.calificacion : '<span class="badge badge-warning">N/A</span>';
                    row.innerHTML = `
                        <td>${alumno.id}</td>
                        <td>${alumno.nombre}</td>
                        <td>${calif}</td>
                        <td><button class="operation-btn" onclick="calificarAlumno('${materiaId}', '${alumno.id}')">Calificar</button></td>
                    `;
                });
            })
            .catch(error => {
                document.getElementById('alumnos-list').innerHTML = `<tr><td colspan="4" class="text-danger">Error de red al cargar alumnos.</td></tr>`;
                console.error('Error al cargar alumnos:', error);
            });
    });

    function calificarAlumno(materiaId, alumnoId) {
        // Redirige a la vista de calificación con IDs necesarios
        window.location.href = `calificar.php?materia_id=${materiaId}&alumno_id=${alumnoId}`;
    }
</script>
</body>
</html>