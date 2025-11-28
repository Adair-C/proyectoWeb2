<?php
require_once "../../src/Auth.php";
require_once "../../src/Middleware.php";
Middleware::requireRole("maestro");

$nombreMaestro = Auth::nombreCompleto();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes Maestro</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/maestro.css">
</head>
<body>
<div class="dashboard">

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
                <span>Maestro:</span>
                <strong><?php echo htmlspecialchars($nombreMaestro); ?></strong>
            </div>
            <ul class="sidebar-menu">
                <li><a href="menu.php">Inicio</a></li>
                <li><a href="materias.php">Gestión de Materias</a></li>
                <li><a href="grupos.php">Mis Grupos</a></li>
                <li><a href="reportes.php" class="active" style="color: #FBBF24;">📊 Reportes</a></li>
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
            <div class="dashboard-header-title">📊 Centro de Reportes</div>
            <div class="dashboard-header-subtitle">Genera listas y reportes de tus grupos</div>
        </header>

        <div class="card" style="max-width: 600px;">
            <div class="card-header">
                <div class="card-title">Listas de Asistencia y Calificaciones</div>
            </div>
            <div class="card-body">
                <form onsubmit="generarReporte(event)">
                    
                    <div style="margin-bottom: 20px;">
                        <label class="form-label" style="font-weight: bold;">1. Selecciona la Materia:</label>
                        <select id="select-materia" class="form-select" required>
                            <option value="">Cargando tus materias...</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 25px;">
                        <label class="form-label" style="font-weight: bold;">2. Filtrar Alumnos:</label>
                        <select id="select-filtro" class="form-select">
                            <option value="todos">📋 Mostrar Todos</option>
                            <option value="aprobados">✅ Solo Aprobados (Promedio >= 70)</option>
                            <option value="reprobados">❌ Solo Reprobados (Promedio < 70)</option>
                        </select>
                    </div>

                    <button class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 1rem;">
                        📄 Generar PDF
                    </button>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const select = document.getElementById('select-materia');
    try {
        const res = await fetch('../../api/maestro/get_materias.php');
        const data = await res.json();

        select.innerHTML = '<option value="">-- Selecciona una materia --</option>';
        
        if(data.length === 0) {
            select.innerHTML = '<option value="">No tienes materias asignadas</option>';
            return;
        }

        data.forEach(m => {
            select.innerHTML += `<option value="${m.id}">${m.nombre} (Grupo ${m.grupo})</option>`;
        });

    } catch (err) {
        select.innerHTML = '<option>Error al cargar materias</option>';
    }
});

function generarReporte(e) {
    e.preventDefault();
    const id = document.getElementById('select-materia').value;
    const filtro = document.getElementById('select-filtro').value; // Obtener filtro

    if(!id) {
        alert("Selecciona una materia primero.");
        return;
    }
    // Enviamos ID y Filtro a la API
    window.open(`../../api/maestro/reporte_materia.php?id=${id}&filtro=${filtro}`, '_blank');
}
</script>
</body>
</html>