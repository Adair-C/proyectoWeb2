/**
 * Lógica para Gestión de Materias (SUPERADMIN)
 */

let isEdit = false;

document.addEventListener('DOMContentLoaded', () => {
    loadMaterias();
    validarEntradas(); // Activamos el bloqueo de caracteres
});

// --- 1. BLOQUEO DE TECLADO (VALIDACIÓN) ---
function validarEntradas() {
    const nombre = document.getElementById('nombre');
    const codigo = document.getElementById('codigo');
    const grupo = document.getElementById('grupo');
    const unidades = document.getElementById('unidades');

    
    if(nombre) {
        nombre.addEventListener('input', function() {
            this.value = this.value.replace(/[^a-zA-ZñÑáéíóúÁÉÍÓÚ\s]/g, '');
        });
    }
    
    if(codigo) {
        codigo.addEventListener('input', function() {
            this.value = this.value.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
        });
    }
    if(grupo) {
        grupo.addEventListener('input', function() {
            this.value = this.value.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
        });
    }
    
    if(unidades) {
        unidades.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }
}


async function loadMaterias() {
    try {
        
        const res = await fetch('../../api/superadmin/get_all_materias.php');
        const data = await res.json();
        const tbody = document.getElementById('tabla-materias');
        tbody.innerHTML = '';

        if(data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center">No hay materias registradas.</td></tr>';
            return;
        }

        data.forEach(m => {
            
            const materiaJson = JSON.stringify(m).replace(/"/g, '&quot;');
            
            
            const estado = m.activo == 1 
                ? '<span style="color:#16a34a; font-weight:bold; background:#dcfce7; padding:2px 8px; border-radius:10px;">Activa</span>' 
                : '<span style="color:#dc2626; font-weight:bold; background:#fee2e2; padding:2px 8px; border-radius:10px;">Inactiva</span>';

            tbody.innerHTML += `
                <tr>
                    <td>${m.codigo}</td>
                    <td>${m.nombre}</td>
                    <td>${m.grupo}</td>
                    <td>${m.unidades}</td>
                    <td>${estado}</td>
                    <td>
                        <button class="btn btn-primary btn-sm" 
                                data-materia="${materiaJson}" 
                                onclick="editMateria(this.dataset.materia)">
                            Editar
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="deleteMateria(${m.id})">Borrar</button>
                    </td>
                </tr>
            `;
        });
    } catch(err) { console.error(err); }
}


window.openModal = function() {
    isEdit = false;
    document.getElementById('form-materia').reset();
    document.getElementById('materiaId').value = '';
    document.getElementById('modal-title').innerText = "Nueva Materia";
    document.getElementById('feedback').style.display = 'none';
    document.getElementById('modal-materia').style.display = 'flex';
}

window.closeModal = function() {
    document.getElementById('modal-materia').style.display = 'none';
}

window.editMateria = function(materiaData) {
    isEdit = true;
    let m = (typeof materiaData === 'string') ? JSON.parse(materiaData) : materiaData;

    document.getElementById('materiaId').value = m.id;
    document.getElementById('nombre').value = m.nombre;
    document.getElementById('codigo').value = m.codigo;
    document.getElementById('grupo').value = m.grupo;
    document.getElementById('unidades').value = m.unidades;
    document.getElementById('activo').value = m.activo;
    
    document.getElementById('modal-title').innerText = "Editar Materia";
    document.getElementById('feedback').style.display = 'none';
    document.getElementById('modal-materia').style.display = 'flex';
}


document.getElementById('form-materia').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    const method = isEdit ? 'PUT' : 'POST';
    const feedback = document.getElementById('feedback');
    
    try {
        const res = await fetch('../../api/superadmin/materia_crud_admin.php', {
            method: method,
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        });
        const result = await res.json();

        if(res.ok) {
            closeModal();
            loadMaterias();
        } else {
            feedback.style.display = 'block';
            feedback.className = 'feedback error';
            feedback.innerText = result.error || "Error al guardar";
        }
    } catch(err) { alert("Error de conexión"); }
});


window.deleteMateria = async function(id) {
    if(!confirm('¿Estás seguro de eliminar esta materia?')) return;
    try {
        const res = await fetch('../../api/superadmin/materia_crud_admin.php', {
            method: 'DELETE',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id})
        });
        if(res.ok) loadMaterias();
        else alert("No se pudo eliminar");
    } catch(err) { alert("Error de conexión"); }
}