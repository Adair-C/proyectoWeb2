document.addEventListener('DOMContentLoaded', () => {
    loadLists();
});

async function loadLists() {
    const materiaId = document.getElementById('page-materia-id').value;
    const availableContainer = document.getElementById('list-available');
    const enrolledContainer = document.getElementById('list-enrolled');
    
    // Placeholder mientras carga
    document.getElementById('materia-titulo').textContent = 'Cargando...';
    availableContainer.innerHTML = 'Cargando...';
    enrolledContainer.innerHTML = 'Cargando...';

    try {
        const res = await fetch(`../../api/maestro/get_alumnos_disponibles.php?materia_id=${materiaId}`);
        const data = await res.json();
        
        if(data.error) {
            alert(data.error);
            return;
        }

        // 1. Actualizar Título (NUEVO)
        document.getElementById('materia-titulo').textContent = data.materia;

        // 2. Renderizar Listas
        renderList(availableContainer, data.disponibles, 'inscribir');
        renderList(enrolledContainer, data.inscritos, 'desinscribir');

    } catch (error) {
        console.error(error);
    }
}

function renderList(container, users, action) {
    container.innerHTML = '';
    if(users.length === 0) {
        container.innerHTML = '<p style="color:#999; text-align:center; padding:10px;">Vacío</p>';
        return;
    }

    users.forEach(u => {
        const div = document.createElement('div');
        div.className = 'student-item';
        
        const btnClass = action === 'inscribir' ? 'btn-success' : 'btn-danger';
        const btnText = action === 'inscribir' ? '+' : '×'; 

        div.innerHTML = `
            <span>${u.nombre}</span>
            <button class="btn ${btnClass}" style="margin:0;" onclick="window.toggleStudent(${u.id}, '${action}')">${btnText}</button>
        `;
        container.appendChild(div);
    });
}

window.toggleStudent = async function(alumnoId, accion) {
    const materiaId = document.getElementById('page-materia-id').value;
    try {
        await fetch('../../api/maestro/gestionar_inscripciones.php', {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify({ materia_id: materiaId, alumno_id: alumnoId, accion })
        });
        loadLists(); 
    } catch (err) { alert("Error de conexión"); }
}