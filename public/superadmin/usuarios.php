<?php
require_once "../../src/Auth.php";
require_once "../../src/Middleware.php";
Middleware::requireRole("superadmin");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/maestro.css"> </head>
<body>
<div class="dashboard">

    <aside class="dashboard-sidebar">
        <div>
            <div class="sidebar-logo">PANEL <span>ADMIN</span></div>
            <div class="sidebar-user">
                <span>Sesión:</span>
                <strong>Super Admin</strong>
            </div>
            <div class="sidebar-section-title">Administración</div>
            <ul class="sidebar-menu">
                <li><a href="menu.php">Inicio</a></li>
                <li><a href="usuarios.php" class="active">Usuarios</a></li>
                <li><a href="materias.php">Materias</a></li>
            </ul>
        </div>
        <div class="sidebar-footer">
            <form action="../logout.php" method="post">
                <button class="sidebar-logout">Cerrar sesión</button>
            </form>
        </div>
    </aside>
    <main class="dashboard-main">
        <div class="dashboard-header">
            <div class="dashboard-header-title">👥 Usuarios del Sistema</div>
            <button class="btn btn-success" onclick="openModal()">+ Nuevo Usuario</button>
        </div>

        <div class="card">
            <div class="card-body table-responsive" style="max-height: none !important;">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Nombre</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-usuarios">
                        <tr><td colspan="6">Cargando...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<div id="modal-user" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <span id="modal-title">Usuario</span>
            <button class="close-modal" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="form-user">
                <input type="hidden" name="id" id="userId">
                
                <label>Nombre Completo</label>
                <input type="text" name="nombre" id="nombre" class="form-input" required style="width:100%; margin-bottom:10px;">

                <label>Usuario (Login)</label>
                <input type="text" name="username" id="username" class="form-input" required style="width:100%; margin-bottom:10px;">

                <label>Email</label>
                <input type="email" name="email" id="email" class="form-input" required style="width:100%; margin-bottom:10px;">

                <label>Rol</label>
                <select name="rol" id="rol" class="form-select" style="width:100%; margin-bottom:10px;">
                    <option value="alumno">Alumno</option>
                    <option value="maestro">Maestro</option>
                    <option value="superadmin">Superadmin</option>
                </select>

                <label>Contraseña <small>(Dejar vacía para no cambiar)</small></label>
                <input type="password" name="password" id="password" class="form-input" style="width:100%; margin-bottom:10px;">

                <label>Estado</label>
                <select name="activo" id="activo" class="form-select" style="width:100%; margin-bottom:15px;">
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </select>

                <button type="submit" class="btn btn-primary" style="width:100%">Guardar</button>
            </form>
        </div>
    </div>
</div>

<script>
let isEdit = false;

document.addEventListener('DOMContentLoaded', loadUsers);

async function loadUsers() {
    try {
        const res = await fetch('../../api/superadmin/get_usuarios.php');
        const users = await res.json();
        const tbody = document.getElementById('tabla-usuarios');
        tbody.innerHTML = '';

        if(users.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6">No hay usuarios registrados.</td></tr>';
            return;
        }

        users.forEach(u => {
            // Escapar datos para evitar errores en el JSON al pasar a la funcion
            const userJson = JSON.stringify(u).replace(/"/g, '&quot;');
            
            tbody.innerHTML += `
                <tr>
                    <td>${u.id}</td>
                    <td>${u.username}</td>
                    <td>${u.nombre_completo}</td>
                    <td><span class="badge badge-info">${u.rol.toUpperCase()}</span></td>
                    <td>${u.activo == 1 ? '<span style="color:green">● Activo</span>' : '<span style="color:red">● Inactivo</span>'}</td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="editUser(${userJson})">Editar</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteUser(${u.id})">Borrar</button>
                    </td>
                </tr>
            `;
        });
    } catch(err) {
        console.error(err);
    }
}

function openModal() {
    isEdit = false;
    document.getElementById('form-user').reset();
    document.getElementById('userId').value = '';
    document.getElementById('modal-title').textContent = "Nuevo Usuario";
    document.getElementById('modal-user').style.display = 'flex';
}

function closeModal() {
    document.getElementById('modal-user').style.display = 'none';
}

window.editUser = function(u) {
    isEdit = true;
    document.getElementById('userId').value = u.id;
    document.getElementById('nombre').value = u.nombre_completo;
    document.getElementById('username').value = u.username;
    document.getElementById('email').value = u.email;
    document.getElementById('rol').value = u.rol;
    document.getElementById('activo').value = u.activo;
    document.getElementById('password').value = ""; // Limpiar password
    
    document.getElementById('modal-title').textContent = "Editar Usuario";
    document.getElementById('modal-user').style.display = 'flex';
}

document.getElementById('form-user').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    
    const method = isEdit ? 'PUT' : 'POST';
    
    try {
        const res = await fetch('../../api/superadmin/usuario_crud.php', {
            method: method,
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        });
        
        if(res.ok) {
            closeModal();
            loadUsers();
        } else {
            alert("Error al guardar usuario");
        }
    } catch(err) { alert("Error de conexión"); }
});

window.deleteUser = async function(id) {
    if(!confirm('¿Eliminar usuario permanentemente?')) return;
    try {
        await fetch('../../api/superadmin/usuario_crud.php', {
            method: 'DELETE',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id})
        });
        loadUsers();
    } catch(err) { alert("Error de conexión"); }
}
</script>
</body>
</html>