<?php
// public/maestro/materias.php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
require_once "../../src/Middleware.php";

Middleware::requireRole("maestro");
$nombre_maestro = Auth::nombreCompleto() ?? Auth::username();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Materias | Maestro</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
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
            <div class="sidebar-section-title">Navegación</div>
            <ul class="sidebar-menu">
                <li><a href="menu.php">Inicio</a></li>
                <li><a class="active" href="materias.php">Materias que imparto</a></li>
                <li><a href="calificar.php">Calificar alumnos</a></li>
            </ul>
        </div>
        <div class="sidebar-footer">
            <form action="../logout.php" method="post"><button class="sidebar-logout" type="submit">Cerrar sesión</button></form>
        </div>
    </aside>

    <main class="dashboard-main">
        <header class="dashboard-header">
            <div class="dashboard-header-title">📚 Materias que imparto</div>
            <div class="dashboard-clock" id="dashboard-clock">--:--:--</div>
        </header>

        <section class="dashboard-grid" style="grid-template-columns: 1fr;">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Listado de asignaturas</div>
                </div>
                <div class="card-body">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre de la Materia</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="materias-list">
                            <tr><td colspan="3">Cargando materias...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cargar reloj
        function updateClock() {
            const el = document.getElementById("dashboard-clock");
            el.textContent = new Date().toLocaleTimeString();
        }
        setInterval(updateClock, 1000);
        updateClock();
        
        // 1. Llamada AJAX para obtener las materias
        fetch('../../api/maestro/get_materias.php')
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('materias-list');
                tbody.innerHTML = ''; // Limpiar mensaje de carga
                
                if (data.error) {
                    tbody.innerHTML = `<tr><td colspan="3" class="text-danger">Error: ${data.error}</td></tr>`;
                    return;
                }
                if (data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="3">No tienes materias asignadas.</td></tr>`;
                    return;
                }
                
                data.forEach(materia => {
                    const row = tbody.insertRow();
                    row.innerHTML = `
                        <td>${materia.codigo}</td>
                        <td>${materia.nombre}</td>
                        <td><button class="operation-btn" onclick="verDetalles('${materia.id}')">Ver Alumnos</button></td>
                    `;
                });
            })
            .catch(error => {
                document.getElementById('materias-list').innerHTML = `<tr><td colspan="3" class="text-danger">Error de red.</td></tr>`;
                console.error('Error al cargar materias:', error);
            });
    });

    function verDetalles(materiaId) {
        // Redirige a la vista de detalle
        window.location.href = 'materia.php?id=' + materiaId;
    }
</script>
</body>
</html>