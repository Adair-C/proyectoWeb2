<?php
require_once "../../src/Auth.php";
require_once "../../src/Middleware.php";
Middleware::requireRole("alumno");

// Datos para el sidebar
$nombreUsuario = Auth::nombreCompleto() ?? "Alumno";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Calificaciones</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/alumno.css">
</head>
<body>
<div class="dashboard">

    <aside class="dashboard-sidebar">
        <div>
            <div class="sidebar-logo">
                CONTROL <span>ESCOLAR</span>
            </div>
            <div class="sidebar-user">
                <span>Alumno:</span>
                <strong><?php echo htmlspecialchars($nombreUsuario); ?></strong>
            </div>

            <div class="sidebar-section-title">Navegación</div>
            <ul class="sidebar-menu">
                <li><a href="menu.php">Inicio</a></li>
                <li><a href="materias.php">Mis Materias</a></li>
                <li><a href="calificaciones.php" class="active">Calificaciones</a></li>
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
            <div class="dashboard-header-title">📊 Kárdex de Calificaciones</div>
        </header>

        <div class="card">
            <div class="card-body table-responsive">
                <table class="grades-table" id="tabla-notas">
                    <thead>
                        <tr id="header-row">
                            <th>Materia</th>
                            </tr>
                    </thead>
                    <tbody id="body-rows">
                        <tr><td>Cargando...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    try {
        const res = await fetch('../../api/alumno/get_notas.php');
        const data = await res.json();
        
        renderTable(data.materias, data.notas);
    } catch(err) {
        console.error(err);
        document.getElementById('body-rows').innerHTML = '<tr><td>Error al cargar datos.</td></tr>';
    }
});

function renderTable(materias, notas) {
    const thead = document.getElementById('header-row');
    const tbody = document.getElementById('body-rows');
    
    // 1. Determinar el máximo de unidades para dibujar las columnas
    let maxUnidades = 0;
    materias.forEach(m => {
        if(m.unidades > maxUnidades) maxUnidades = m.unidades;
    });

    // 2. Construir cabecera
    let headHtml = '<th>Materia</th>';
    for(let i=1; i <= maxUnidades; i++) {
        headHtml += `<th style="text-align:center">U${i}</th>`;
    }
    headHtml += '<th style="text-align:center">Promedio</th>';
    thead.innerHTML = headHtml;

    // 3. Construir filas
    tbody.innerHTML = '';
    if(materias.length === 0) {
        tbody.innerHTML = '<tr><td colspan="10">Sin registros.</td></tr>';
        return;
    }

    materias.forEach(m => {
        let tr = document.createElement('tr');
        let html = `<td>
                        <div style="font-weight:600">${m.nombre}</div>
                        <div style="font-size:0.8rem; color:#666">Grupo ${m.grupo}</div>
                    </td>`;
        
        let suma = 0;
        let cont = 0;

        // Iterar columnas
        for(let i=1; i <= maxUnidades; i++) {
            // Si la materia tiene menos unidades que el máximo global
            if(i > m.unidades) {
                html += `<td style="background:#f9fafb;"></td>`; 
                continue;
            }

            let calif = '-';
            let colorClass = '';

            // Verificar si existe nota
            if(notas[m.id] && notas[m.id][i] !== undefined) {
                const val = parseFloat(notas[m.id][i]);
                calif = val;
                suma += val;
                cont++;
                // Criterio ejemplo < 70 reprueba
                colorClass = val < 70 ? 'grade-bad' : 'grade-good'; 
            }

            html += `<td style="text-align:center" class="grade-number ${colorClass}">${calif}</td>`;
        }

        // Calcular promedio
        let promedio = cont > 0 ? (suma / cont).toFixed(1) : '-';
        let promClass = '';
        if(promedio !== '-') {
            promClass = promedio < 70 ? 'grade-bad' : 'grade-good';
        }

        html += `<td style="text-align:center" class="grade-number grade-avg ${promClass}">${promedio}</td>`;
        tr.innerHTML = html;
        tbody.appendChild(tr);
    });
}
</script>
</body>
</html>