<?php
require_once "../../src/Auth.php";
require_once "../../src/Middleware.php";
Middleware::requireRole("maestro");
$materiaId = $_GET['id'] ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Calificar</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/maestro.css">
</head>
<body>
<div class="dashboard">
    <aside class="dashboard-sidebar">
        <div class="sidebar-logo">CONTROL <span>ESCOLAR</span></div>
        <ul class="sidebar-menu">
            <li><a href="materias.php">Volver</a></li>
        </ul>
    </aside>

    <main class="dashboard-main">
        <header class="dashboard-header">
            <div class="dashboard-header-title">📝 Calificando: <span id="materia-titulo">...</span></div>
        </header>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="custom-table" id="tabla-calificaciones">
                        <thead>
                            <tr id="tabla-head">
                                </tr>
                        </thead>
                        <tbody id="tabla-body">
                            <tr><td>Cargando datos...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="../assets/js/maestro-app.js"></script>
<script>
// Lógica específica para pintar esta tabla compleja
document.addEventListener('DOMContentLoaded', async () => {
    const materiaId = <?php echo $materiaId; ?>;
    const res = await fetch(`../../api/maestro/get_calificaciones_tabla.php?materia_id=${materiaId}`);
    const data = await res.json();

    const materia = data.materia;
    const alumnos = data.alumnos;
    const notas = data.notas; // Objeto { alumnoId: { unidad: nota } }

    document.getElementById('materia-titulo').textContent = `${materia.nombre} (Grupo ${materia.grupo})`;

    // 1. Construir Cabecera (U#1, U#2...)
    const thead = document.getElementById('tabla-head');
    let headHtml = '<th>Alumno</th>';
    for(let i=1; i <= materia.unidades; i++) {
        headHtml += `<th style="text-align:center">U#${i}</th>`;
    }
    thead.innerHTML = headHtml;

    // 2. Construir Cuerpo
    const tbody = document.getElementById('tabla-body');
    tbody.innerHTML = '';

    if(alumnos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="100">No hay alumnos inscritos.</td></tr>';
        return;
    }

    alumnos.forEach(alumno => {
        let rowHtml = `<tr><td>${alumno.nombre_completo}</td>`;
        
        for(let i=1; i <= materia.unidades; i++) {
            // Buscar si ya existe nota
            let val = '';
            if(notas[alumno.id] && notas[alumno.id][i]) {
                val = notas[alumno.id][i];
            }

            rowHtml += `
                <td style="text-align:center">
                    <input type="number" 
                           class="input-grade" 
                           value="${val}" 
                           min="0" max="100"
                           data-alumno="${alumno.id}"
                           data-materia="${materiaId}"
                           data-unidad="${i}"
                           onchange="saveGrade(this)"> 
                </td>`;
        }
        rowHtml += '</tr>';
        tbody.innerHTML += rowHtml;
    });
});
</script>
</body>
</html>