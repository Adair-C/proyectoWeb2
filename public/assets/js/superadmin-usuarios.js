
let isEdit = false;

document.addEventListener('DOMContentLoaded', () => {
    loadUsers();
    validarEntradasUser(); 
});


function validarEntradasUser() {
    const nombre = document.getElementById('nombre');
    const username = document.getElementById('username');
    const email = document.getElementById('email');

    
    if(nombre) {
        nombre.addEventListener('input', function() {
            this.value = this.value.replace(/[^a-zA-ZñÑáéíóúÁÉÍÓÚ\s]/g, '');
        });
    }

    
    if(username) {
        username.addEventListener('input', function() {
            this.value = this.value.replace(/[^a-zA-Z0-9ñÑ]/g, '');
        });
    }

    
    if(email) {
        email.addEventListener('input', function() {
            this.value = this.value.replace(/[^a-zA-Z0-9@._-]/g, '');
        });
    }
}


async function loadUsers() {
    const tbody = document.getElementById('tabla-usuarios');
    try {
        
        const res = await fetch('../../api/superadmin/get_usuarios.php');
        const users = await res.json();
        tbody.innerHTML = '';

        if(users.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center">No hay usuarios registrados.</td></tr>';
            return;
        }

        users.forEach(u => {
            const userJson = JSON.stringify(u).replace(/"/g, '&quot;');
            const estado = u.activo == 1 
                ? '<span style="color:#16a34a; font-weight:bold; background:#dcfce7; padding:2px 8px; border-radius:10px;">Activo</span>' 
                : '<span style="color:#dc2626; font-weight:bold; background:#fee2e2; padding:2px 8px; border-radius:10px;">Inactivo</span>';

            tbody.innerHTML += `
                <tr>
                    <td>${u.id}</td>
                    <td>${u.username}</td>
                    <td>${u.nombre_completo}</td>
                    <td><span class="badge badge-info">${u.rol.toUpperCase()}</span></td>
                    <td>${estado}</td>
                    <td>
                        <button class="btn btn-primary btn-sm" 
                                data-user="${userJson}"
                                onclick="editUser(this.dataset.user)">
                            Editar
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="deleteUser(${u.id})">Borrar</button>
                    </td>
                </tr>
            `;
        });
    } catch(err) { console.error(err); }
}


window.openUserModal = function() {
    isEdit = false;
    document.getElementById('form-user').reset();
    document.getElementById('userId').value = '';
    document.getElementById('modal-title').textContent = "Nuevo Usuario";
    document.getElementById('feedback').style.display = 'none';
    document.getElementById('modal-user').style.display = 'flex';
}

window.closeUserModal = function() {
    document.getElementById('modal-user').style.display = 'none';
}

window.editUser = function(userData) {
    isEdit = true;
    let u = (typeof userData === 'string') ? JSON.parse(userData) : userData;

    document.getElementById('userId').value = u.id;
    document.getElementById('nombre').value = u.nombre_completo;
    document.getElementById('username').value = u.username;
    document.getElementById('email').value = u.email;
    document.getElementById('rol').value = u.rol;
    document.getElementById('activo').value = u.activo;
    document.getElementById('password').value = ""; 
    
    document.getElementById('modal-title').textContent = "Editar Usuario";
    document.getElementById('feedback').style.display = 'none';
    document.getElementById('modal-user').style.display = 'flex';
}


document.getElementById('form-user').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    const method = isEdit ? 'PUT' : 'POST';
    const feedback = document.getElementById('feedback');
    
    try {
        const res = await fetch('../../api/superadmin/usuario_crud.php', {
            method: method,
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        });
        const result = await res.json();
        
        if(res.ok) {
            closeUserModal();
            loadUsers();
        } else {
            feedback.style.display = 'block';
            feedback.className = 'feedback error';
            feedback.innerText = result.error || "Error al guardar";
        }
    } catch(err) { alert("Error de conexión"); }
});


window.deleteUser = async function(id) {
    if(!confirm('¿Eliminar usuario permanentemente?')) return;
    try {
        const res = await fetch('../../api/superadmin/usuario_crud.php', {
            method: 'DELETE',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id})
        });
        if(res.ok) loadUsers();
        else alert("Error al eliminar");
    } catch(err) { alert("Error de conexión"); }
}