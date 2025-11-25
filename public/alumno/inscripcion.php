<?php
require_once "../../src/Auth.php";
require_once "../../src/Middleware.php";
Middleware::requireRole("alumno");

$nombreUsuario = Auth::nombreCompleto() ?? "Alumno";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inscribir Materias</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/alumno.css">
</head>
<body>
<div class="dashboard">

    <aside class="dashboard-sidebar">
        <div>
            <div class="sidebar-logo">CONTROL <span>ESCOLAR</span></div>
            <div class="sidebar-user">
                <span>Alumno:</span>
                <strong><?php echo htmlspecialchars($nombreUsuario); ?></strong>
            </div>

            <div class="sidebar-section-title">Navegación</div>
            <ul class="sidebar-menu">
                <li><a href="menu.php">Inicio</a></li>
                <li><a href="materias.php">Mis Materias</a></li>
                <li><a href="calificaciones.php">Calificaciones</a></li>
                <li><a href="inscripcion.php" class="active" style="color:#10B981;">+ Inscribir Materia</a></li>
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
            <div class="dashboard-header-title">✍️ Inscripción de Materias</div>
            <div class="dashboard-header-subtitle">Selecciona las materias para este ciclo</div>
        </header>

        <div class="dashboard-grid" id="grid-disponibles">
            <p>Buscando materias disponibles...</p>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', loadAvailable);

async function loadAvailable() {
    const container = document.getElementById('grid-disponibles');
    try {
        const res = await fetch('../../api/alumno/get_disponibles.php');
        const materias = await res.json();
        
        container.innerHTML = '';

        if(materias.length === 0) {
            container.innerHTML = '<div class="card"><div class="card-body">No hay materias disponibles para inscribir (o ya las tienes todas).</div></div>';
            return;
        }

        materias.forEach(m => {
            const card = document.createElement('div');
            card.className = 'card subject-card';
            // Agregamos botón de inscribir
            card.innerHTML = `
                <div class="card-body">
                    <div style="display:flex; justify-content:space-between;">
                        <span class="subject-code">${m.codigo}</span>
                        <span class="badge-group">Grupo ${m.grupo}</span>
                    </div>
                    <h3 style="margin: 10px 0; color: #111827;">${m.nombre}</h3>
                    <div class="subject-teacher">
                        👨‍🏫 Maestro: <strong>${m.maestro}</strong>
                    </div>
                    <button class="btn-primary" 
                            style="width:100%; margin-top:15px; background:#10B981;" 
                            onclick="enroll(${m.id})">
                        Inscribirse
                    </button>
                </div>
            `;
            container.appendChild(card);
        });
    } catch (error) {
        container.innerHTML = '<p style="color:red">Error al cargar datos.</p>';
    }
}

async function enroll(id) {
    if(!confirm("¿Confirmas tu inscripción a esta materia?")) return;

    try {
        const res = await fetch('../../api/alumno/inscribir.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ materia_id: id })
        });
        const result = await res.json();

        if(res.ok && !result.error) {
            alert(result.message);
            loadAvailable(); // Recargar la lista para que desaparezca la inscrita
        } else {
            alert("Error: " + (result.error || "No se pudo inscribir"));
        }
    } catch (err) {
        alert("Error de conexión");
    }
}
</script>
</body>
</html>