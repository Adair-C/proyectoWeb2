<?php
require_once "../../src/Auth.php";
require_once "../../src/Middleware.php";
Middleware::requireRole("maestro");

$materiaId = $_GET['id'] ?? null;
if(!$materiaId) header("Location: materias.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Calificar Grupo</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/maestro.css">
</head>
<body>
<div class="dashboard">
    <aside class="dashboard-sidebar">
        <div>
            <div class="sidebar-logo">CONTROL <span>ESCOLAR</span></div>
            <ul class="sidebar-menu">
                <li><a href="materias.php">Volver a Materias</a></li>
            </ul>
        </div>
    </aside>

    <main class="dashboard-main">
        <header class="dashboard-header">
            <div class="dashboard-header-title">Calificaciones: <span id="nombre-materia">Cargando...</span></div>
        </header>

        <div class="card">
            <div class="card-body">
                <table class="data-table" width="100%">
                    <thead>
                        <tr>
                            <th>Alumno</th>
                            <th>Nota Actual</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody id="lista-alumnos"></tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script>
const materiaId = <?php echo $materiaId; ?>;
document.addEventListener('DOMContentLoaded', () => {
    fetch(`../../api/maestro/get_alumnos_materia.php?materia_id=${materiaId}`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('nombre-materia').textContent = data.nombre_materia;
            const tbody = document.getElementById('lista-alumnos');
            
            if(data.alumnos.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3">No hay alumnos inscritos.</td></tr>';
                return;
            }

            data.alumnos.forEach(a => {
                const nota = a.calificacion !== null ? `<b>${a.calificacion}</b>` : '<span style="color:#999">S/C</span>';
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${a.nombre}</td>
                    <td>${nota}</td>
                    <td>
                        <a href="calificar.php?materia_id=${materiaId}&alumno_id=${a.id}" class="btn-action btn-grade">Poner Nota</a>
                    </td>
                `;
                tbody.appendChild(row);
            });
        });
});
</script>
</body>
</html>