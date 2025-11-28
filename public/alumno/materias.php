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
    <title>Mis Materias</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/alumno.css">
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
                <span>Alumno:</span>
                <strong><?php echo htmlspecialchars($nombreUsuario); ?></strong>
            </div>

            <div class="sidebar-section-title">Navegación</div>
            <ul class="sidebar-menu">
                <li><a href="menu.php">Inicio</a></li>
                <li><a href="materias.php" class="active">Mis Materias</a></li>
                <li><a href="calificaciones.php">Calificaciones</a></li>
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
            <div class="dashboard-header-title">📚 Mis Materias</div>
        </header>

        <div class="dashboard-grid" id="grid-materias">
            <p>Cargando materias...</p>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const container = document.getElementById('grid-materias');
    
    try {
        // Llamamos a la API
        const res = await fetch('../../api/alumno/get_inscritas.php');
        const materias = await res.json();
        
        container.innerHTML = '';
        
        if(materias.length === 0) {
            container.innerHTML = '<p>No estás inscrito en ninguna materia.</p>';
            return;
        }

        materias.forEach(m => {
            const card = document.createElement('div');
            card.className = 'card subject-card';
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
                    <div style="margin-top:15px; font-size:0.8rem; color:#666;">
                        Evaluación: ${m.unidades} Parciales
                    </div>
                </div>
            `;
            container.appendChild(card);
        });
    } catch (err) {
        console.error(err);
        container.innerHTML = '<p style="color:red">Error al cargar materias.</p>';
    }
});
</script>
</body>
</html>