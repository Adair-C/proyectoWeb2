document.addEventListener('DOMContentLoaded', () => {
    loadMaterias(); // <--- AQUÍ SE LLAMA A LA API AL INICIAR
});

let isEditing = false;

// --- CARGAR TABLA DESDE API ---
async function loadMaterias() {
    const tbody = document.getElementById('materias-body');
    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center">Cargando materias...</td></tr>';

    try {
        const res = await fetch('../../api/maestro/get_materias.php');
        const data = await res.json();

        tbody.innerHTML = '';

        if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center">No tienes materias registradas.</td></tr>';
            return;
        }

        data.forEach(m => {
            const row = document.createElement('tr');
            
            // Convertimos el objeto a string JSON seguro para ponerlo en el atributo data
            const jsonMateria = JSON.stringify(m).replace(/"/g, '&quot;');

            row.innerHTML = `
                <td>${m.codigo}</td>
                <td>${m.nombre}</td>
                <td><span class="badge badge-info">${m.grupo}</span></td>
                <td>${m.unidades}</td>
                <td>
                    <button class="btn btn-primary" 
                            data-materia="${jsonMateria}" 
                            onclick="openMateriaModal(true, this.dataset.materia)">
                        Editar
                    </button>
                    
                    <button class="btn btn-danger" onclick="deleteMateria(${m.id})">Borrar</button>
                    
                    <a href="gestionar_alumnos.php?id=${m.id}" class="btn btn-warning">Alumnos</a>
                    <a href="materia.php?id=${m.id}" class="btn btn-success">Calificar</a>
                </td>
            `;
            tbody.appendChild(row);
        });

    } catch (error) {
        console.error(error);
        tbody.innerHTML = '<tr><td colspan="5" style="color:red; text-align:center">Error de conexión.</td></tr>';
    }
}

// --- MODAL ---
window.openMateriaModal = function(edit = false, materiaData = null) {
    isEditing = edit;
    const modal = document.getElementById('modal-materia');
    const form = document.getElementById('form-materia');
    document.getElementById('modal-feedback').style.display = 'none';
    form.reset();
    
    document.getElementById('modal-title').innerText = edit ? 'Editar Materia' : 'Nueva Materia';
    
    if (edit && materiaData) {
        let materia = (typeof materiaData === 'string') ? JSON.parse(materiaData) : materiaData;
        document.getElementById('materia_id').value = materia.id;
        document.getElementById('nombre').value = materia.nombre;
        document.getElementById('codigo').value = materia.codigo;
        document.getElementById('grupo').value = materia.grupo;
        document.getElementById('unidades').value = materia.unidades;
    } else {
        document.getElementById('materia_id').value = '';
    }
    modal.style.display = 'flex';
}

window.closeMateriaModal = function() {
    document.getElementById('modal-materia').style.display = 'none';
}

// --- GUARDAR ---
window.saveMateria = async function(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    const method = isEditing ? 'PUT' : 'POST';
    const feedback = document.getElementById('modal-feedback');

    try {
        const res = await fetch('../../api/maestro/materia_crud.php', {
            method: method,
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        });
        const result = await res.json();
        
        feedback.style.display = 'block';
        feedback.innerText = result.message || result.error;
        feedback.className = res.ok ? 'feedback success' : 'feedback error';

        if(res.ok) {
            setTimeout(() => {
                closeMateriaModal();
                loadMaterias(); // Recargar tabla sin recargar página
            }, 1000);
        }
    } catch (err) { console.error(err); }
}

// --- BORRAR ---
window.deleteMateria = async function(id) {
    if(!confirm("¿Eliminar materia?")) return;
    try {
        const res = await fetch('../../api/maestro/materia_crud.php', {
            method: 'DELETE',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id})
        });
        if(res.ok) loadMaterias(); // Recargar tabla
    } catch(err) { alert("Error de conexión"); }
}