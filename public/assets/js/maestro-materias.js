// public/assets/js/maestro-materias.js

document.addEventListener('DOMContentLoaded', () => {
    loadMaterias();
    
    // Reloj
    setInterval(() => {
        const el = document.getElementById("dashboard-clock");
        if(el) el.textContent = new Date().toLocaleTimeString();
    }, 1000);
});

let isEditing = false;

// --- CARGAR MATERIAS ---
async function loadMaterias() {
    const tbody = document.getElementById('materias-body');
    tbody.innerHTML = '<tr><td colspan="4">Cargando...</td></tr>';

    try {
        const res = await fetch('../../api/maestro/get_materias.php');
        const data = await res.json();

        tbody.innerHTML = '';
        
        if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4">No tienes materias registradas.</td></tr>';
            return;
        }

        data.forEach(materia => {
            const row = document.createElement('tr');
            // Escapamos comillas para pasarlo al onclick
            const json = JSON.stringify(materia).replace(/"/g, '&quot;');
            
            row.innerHTML = `
                <td>${materia.codigo}</td>
                <td>${materia.nombre}</td>
                <td>
                    <button class="btn-action btn-edit" onclick="openModal(true, ${json})">✏️ Editar</button>
                    <button class="btn-action btn-delete" onclick="deleteMateria(${materia.id})">🗑️ Borrar</button>
                </td>
                <td>
                    <a href="gestionar_alumnos.php?id=${materia.id}" class="btn-action btn-students">👥 Alumnos</a>
                    <a href="materia.php?id=${materia.id}" class="btn-action btn-grade">✅ Calificar</a>
                </td>
            `;
            tbody.appendChild(row);
        });

    } catch (error) {
        tbody.innerHTML = '<tr><td colspan="4" style="color:red">Error al cargar datos</td></tr>';
    }
}

// --- MODAL ---
window.openModal = function(edit = false, materia = {}) {
    isEditing = edit;
    const modal = document.getElementById('modal-materia');
    const title = document.getElementById('modal-title');
    const form = document.getElementById('form-materia');
    const feedback = document.getElementById('modal-feedback');

    feedback.style.display = 'none';
    form.reset();
    modal.style.display = 'flex';

    if (edit) {
        title.textContent = 'Editar Materia';
        document.getElementById('materia_id').value = materia.id;
        document.getElementById('nombre').value = materia.nombre;
        document.getElementById('codigo').value = materia.codigo;
    } else {
        title.textContent = 'Nueva Materia';
        document.getElementById('materia_id').value = '';
    }
}

window.closeModal = function() {
    document.getElementById('modal-materia').style.display = 'none';
}

// --- GUARDAR (CREATE / UPDATE) ---
document.getElementById('form-materia').addEventListener('submit', async (e) => {
    e.preventDefault();
    const feedback = document.getElementById('modal-feedback');
    const formData = new FormData(e.target);
    
    const data = {
        id: formData.get('id'),
        nombre: formData.get('nombre'),
        codigo: formData.get('codigo')
    };

    const method = isEditing ? 'PUT' : 'POST';

    try {
        const res = await fetch('../../api/maestro/materia_crud.php', {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await res.json();

        feedback.style.display = 'block';
        feedback.textContent = result.message || result.error;
        
        if (res.ok) {
            feedback.className = 'feedback success';
            setTimeout(() => {
                closeModal();
                loadMaterias();
            }, 1000);
        } else {
            feedback.className = 'feedback error';
        }
    } catch (err) {
        console.error(err);
    }
});

// --- ELIMINAR ---
window.deleteMateria = async function(id) {
    if(!confirm('¿Estás seguro? Se borrarán alumnos y calificaciones de esta materia.')) return;

    try {
        const res = await fetch('../../api/maestro/materia_crud.php', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });
        const result = await res.json();
        
        if(res.ok) {
            alert('Materia eliminada');
            loadMaterias();
        } else {
            alert('Error: ' + result.error);
        }
    } catch (err) {
        alert('Error de red');
    }
}