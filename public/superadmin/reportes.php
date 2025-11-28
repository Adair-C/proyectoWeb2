<?php
require_once "../../app/Config/Database.php";
require_once "../../app/Helpers/Auth.php";
require_once "../../app/Helpers/Middleware.php";
Middleware::requireRole("superadmin");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Generador de Reportes</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/maestro.css"> </head>
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
                <span>Sesión:</span>
                <strong>Super Admin</strong>
            </div>
            <ul class="sidebar-menu">
                <li><a href="menu.php">Inicio</a></li>
                <li><a href="usuarios.php">Usuarios</a></li>
                <li><a href="materias.php">Materias</a></li>
                <li><a href="reportes.php" class="active" style="color: #FBBF24;">📊 Reportes</a></li>
            </ul>
        </div>
        <div class="sidebar-footer">
            <form action="../logout.php" method="post">
                <button class="sidebar-logout">Cerrar sesión</button>
            </form>
        </div>
    </aside>

    <main class="dashboard-main">
        <header class="dashboard-header">
            <div class="dashboard-header-title">📊 Generador de Reportes</div>
            <div class="dashboard-header-subtitle">Descarga reportes personalizados en PDF</div>
        </header>

        <div class="dashboard-grid" style="grid-template-columns: 1fr 1fr;">
            
            <div class="card">
                <div class="card-header"><div class="card-title">👥 Reportes de Usuarios</div></div>
                <div class="card-body">
                    <form onsubmit="generarReporteUsuarios(event)">
                        <div style="margin-bottom: 15px;">
                            <label class="form-label">Filtrar por Rol:</label>
                            <select id="filtro-rol" class="form-select">
                                <option value="todos">Todos los usuarios</option>
                                <option value="alumno">Solo Alumnos</option>
                                <option value="maestro">Solo Maestros</option>
                                <option value="superadmin">Administradores</option>
                            </select>
                        </div>
                        <button class="btn btn-primary" style="width:100%">📄 Descargar PDF</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><div class="card-title">📚 Reportes de Materias</div></div>
                <div class="card-body">
                    <form onsubmit="generarReporteMaterias(event)">
                        <div style="margin-bottom: 15px;">
                            <label class="form-label">Tipo de reporte:</label>
                            <select id="filtro-materia-tipo" class="form-select" onchange="toggleMaestros(this.value)">
                                <option value="global">Catálogo completo de materias</option>
                                <option value="por_maestro">Materias asignadas a un profesor</option>
                            </select>
                        </div>

                        <div id="div-select-maestro" style="margin-bottom: 15px; display: none;">
                            <label class="form-label">Selecciona al Profesor:</label>
                            <select id="select-maestro" class="form-select">
                                <option value="">Cargando lista...</option>
                            </select>
                        </div>

                        <button class="btn btn-primary" style="width:100%; background-color: #10B981;">📄 Descargar PDF</button>
                    </form>
                </div>
            </div>

        </div>
    </main>
</div>

<script>
// 1. GENERAR REPORTE USUARIOS
function generarReporteUsuarios(e) {
    e.preventDefault();
    const rol = document.getElementById('filtro-rol').value;
    const url = `../../api/superadmin/reporte_personalizado.php?tipo=usuarios&filtro=${rol}`;
    window.open(url, '_blank');
}

// 2. LÓGICA UI PARA MATERIAS
function toggleMaestros(val) {
    const div = document.getElementById('div-select-maestro');
    if (val === 'por_maestro') {
        div.style.display = 'block';
        cargarMaestros(); // Cargar la lista solo si es necesario
    } else {
        div.style.display = 'none';
    }
}

// 3. CARGAR LISTA DE MAESTROS (AJAX)
let maestrosCargados = false;
async function cargarMaestros() {
    if (maestrosCargados) return; // Evitar recargar
    
    const select = document.getElementById('select-maestro');
    try {
        const res = await fetch('../../api/superadmin/get_maestros_lista.php');
        const data = await res.json();
        
        select.innerHTML = '<option value="">-- Selecciona --</option>';
        data.forEach(m => {
            select.innerHTML += `<option value="${m.id}">${m.nombre_completo}</option>`;
        });
        maestrosCargados = true;
    } catch (err) {
        select.innerHTML = '<option>Error al cargar</option>';
    }
}

// 4. GENERAR REPORTE MATERIAS
function generarReporteMaterias(e) {
    e.preventDefault();
    const tipo = document.getElementById('filtro-materia-tipo').value;
    
    if (tipo === 'global') {
        window.open('../../api/superadmin/reporte_personalizado.php?tipo=materias', '_blank');
    } else {
        const idMaestro = document.getElementById('select-maestro').value;
        if (!idMaestro) {
            alert("Por favor selecciona un maestro.");
            return;
        }
        window.open(`../../api/superadmin/reporte_personalizado.php?tipo=materias&id=${idMaestro}`, '_blank');
    }
}
</script>
</body>
</html>