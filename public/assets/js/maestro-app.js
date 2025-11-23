// Variable global para controlar edición
let isEditing = false;

// --- FUNCIONES PARA GESTIÓN DE MATERIAS (CRUD) ---

function openMateriaModal(edit = false, materia = {}) {
    const modal = document.getElementById('modal-materia');
    const form = document.getElementById('form-materia');
    const title = document.getElementById('modal-title');
    
    isEditing = edit;
    modal.style.display = 'flex';
    form.reset();
    document.getElementById('modal-feedback').style.display = 'none';

    if (edit) {
        title.textContent = 'Editar Materia';
        document.getElementById('materia_id').value = materia.id;
        document.getElementById('nombre').value = materia.nombre;
        document.getElementById('codigo').value = materia.codigo;
        document.getElementById('grupo').value = materia.grupo;
        document.getElementById('unidades').value = materia.unidades;
    } else {
        title.textContent = 'Nueva Materia';
        document.getElementById('materia_id').value = '';
    }
}

function closeMateriaModal() {
    document.getElementById('modal-materia').style.display = 'none';
}

async function saveMateria(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const feedback = document.getElementById('modal-feedback');

    const data = {
        id: formData.get('id'),
        nombre: formData.get('nombre'),
        codigo: formData.get('codigo'),
        grupo: formData.get('grupo'),
        unidades: formData.get('unidades')
    };

    const method = isEditing ? 'PUT' : 'POST';

    try {
        const res = await fetch('../../api/maestro/materia_crud.php', {
            method: method,
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        });
        const result = await res.json();

        feedback.style.display = 'block';
        feedback.textContent = result.message || result.error;
        feedback.className = res.ok ? 'feedback success' : 'feedback error';

        if (res.ok) {
            setTimeout(() => {
                closeMateriaModal();
                window.location.reload(); // Recargar para ver cambios
            }, 1000);
        }
    } catch (error) {
        console.error(error);
    }
}

async function deleteMateria(id) {
    if(!confirm("¿Eliminar materia? Se borrarán todas las calificaciones asociadas.")) return;
    
    try {
        const res = await fetch('../../api/maestro/materia_crud.php', {
            method: 'DELETE',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id})
        });
        if(res.ok) window.location.reload();
        else alert("Error al eliminar");
    } catch(err) { alert("Error de conexión"); }
}

// --- FUNCIONES PARA CALIFICACIONES (GRID) ---

async function saveGrade(input) {
    const alumnoId = input.dataset.alumno;
    const materiaId = input.dataset.materia;
    const unidad = input.dataset.unidad;
    const calificacion = input.value;

    // Validación básica
    if(calificacion < 0 || calificacion > 100) {
        alert("La calificación debe ser entre 0 y 100");
        return;
    }

    // Feedback visual rápido (cambio de borde)
    input.style.borderColor = "#ffc107"; // Amarillo = guardando

    try {
        const res = await fetch('../../api/maestro/guardar_calificacion.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                alumno_id: alumnoId,
                materia_id: materiaId,
                unidad: unidad,
                calificacion: calificacion
            })
        });
        
        if(res.ok) {
            input.style.borderColor = "#28a745"; // Verde = guardado
        } else {
            input.style.borderColor = "#dc3545"; // Rojo = error
        }
    } catch (error) {
        input.style.borderColor = "#dc3545";
    }
}