<?php
require_once "../../src/Auth.php";
require_once "../../src/Middleware.php";
Middleware::requireRole("maestro");

$materiaId = $_GET['materia_id'];
$alumnoId = $_GET['alumno_id'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignar Calificación</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/maestro.css">
</head>
<body>
<div class="dashboard">
    <div class="card" style="max-width: 400px; margin: 50px auto;">
        <div class="card-header">
            <div class="card-title">Asignar Calificación</div>
        </div>
        <div class="card-body">
            <form id="form-calificar">
                <div class="form-row">
                    <label>Calificación (0-100)</label>
                    <input type="number" id="nota" class="form-input" step="0.1" min="0" max="100" required>
                </div>
                <button type="submit" class="btn-primary" style="width:100%; margin-top:10px">Guardar</button>
                <div id="msg" class="feedback"></div>
                <a href="materia.php?id=<?php echo $materiaId; ?>" style="display:block; text-align:center; margin-top:15px; color:#666">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('form-calificar').addEventListener('submit', async (e) => {
    e.preventDefault();
    const nota = document.getElementById('nota').value;
    const feedback = document.getElementById('msg');

    const res = await fetch('../../api/maestro/guardar_calificacion.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({ 
            materia_id: <?php echo $materiaId; ?>, 
            alumno_id: <?php echo $alumnoId; ?>, 
            calificacion: nota 
        })
    });
    const data = await res.json();

    feedback.style.display = 'block';
    feedback.textContent = data.message;
    feedback.className = res.ok ? 'feedback success' : 'feedback error';
    
    if(res.ok) {
        setTimeout(() => window.location.href = 'materia.php?id=<?php echo $materiaId; ?>', 1000);
    }
});
</script>
</body>
</html>