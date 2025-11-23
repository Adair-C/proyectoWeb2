<?php
require_once "../../src/Database.php";
require_once "../../src/Middleware.php";
Middleware::requireRole("maestro");

$idMateria = $_GET['id'] ?? null;
if(!$idMateria) header("Location: materias.php");

// Obtener nombre materia para mostrar
$db = Database::pdo();
$stmt = $db->prepare("SELECT nombre FROM materias WHERE id = ?");
$stmt->execute([$idMateria]);
$materia = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Alumnos</title>
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
            <div class="dashboard-header-title">Alumnos en: <?php echo htmlspecialchars($materia['nombre']); ?></div>
        </header>

        <div class="card">
            <div class="card-body inscription-container">
                
                <div class="student-list-box">
                    <h3>Disponibles</h3>
                    <div id="list-available">Cargando...</div>
                </div>

                <div class="student-list-box">
                    <h3>Inscritos</h3>
                    <div id="list-enrolled">Cargando...</div>
                </div>

            </div>
        </div>
    </main>
</div>

<script>
const materiaId = <?php echo $idMateria; ?>;

document.addEventListener('DOMContentLoaded', loadLists);

async function loadLists() {
    const res = await fetch(`../../api/maestro/get_alumnos_disponibles.php?materia_id=${materiaId}`);
    const data = await res.json();
    
    renderList('list-available', data.disponibles, 'inscribir');
    renderList('list-enrolled', data.inscritos, 'desinscribir');
}

function renderList(elementId, users, action) {
    const container = document.getElementById(elementId);
    container.innerHTML = '';
    
    if(users.length === 0) {
        container.innerHTML = '<p style="color:#999; text-align:center">Vacío</p>';
        return;
    }

    users.forEach(u => {
        const div = document.createElement('div');
        div.className = 'student-item';
        
        const btnClass = action === 'inscribir' ? 'btn-grade' : 'btn-delete';
        const btnText = action === 'inscribir' ? '+' : 'X';

        div.innerHTML = `
            <span>${u.nombre}</span>
            <button class="btn-action ${btnClass}" onclick="toggleStudent(${u.id}, '${action}')">${btnText}</button>
        `;
        container.appendChild(div);
    });
}

async function toggleStudent(alumnoId, accion) {
    await fetch('../../api/maestro/gestionar_inscripciones.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({ materia_id: materiaId, alumno_id: alumnoId, accion })
    });
    loadLists(); // Recargar listas
}
</script>
</body>
</html>