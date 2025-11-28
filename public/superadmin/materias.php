<?php
require_once "../../src/Auth.php";
require_once "../../src/Middleware.php";
Middleware::requireRole("superadmin");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Materias</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/maestro.css">
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
                <span>Sesión:</span>
                <strong>Super Admin</strong>
            </div>
            <div class="sidebar-section-title">Administración</div>
            <ul class="sidebar-menu">
                <li><a href="menu.php">Inicio</a></li>
                <li><a href="usuarios.php">Usuarios</a></li>
                <li><a href="materias.php" class="active">Materias</a></li>
                <li><a href="reportes.php" style="color: #FBBF24;">📊 Reportes</a></li>
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
            <div class="dashboard-header-title">📚 Catálogo de Materias</div>
            <button class="btn btn-success" onclick="openModal()">+ Nueva Materia</button>
        </div>

        <div class="card">
            <div class="card-body table-responsive" style="max-height: none !important;">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Grupo</th>
                            <th>Unidades</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-materias">
                        <tr><td colspan="6">Cargando...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<div id="modal-materia" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <span id="modal-title">Materia</span>
            <button class="close-modal" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="form-materia">
                <input type="hidden" name="id" id="materiaId">
                
                <label>Nombre Materia</label>
                <input type="text" name="nombre" id="nombre" class="form-input" required style="width:100%; margin-bottom:10px;">

                <div style="display:flex; gap:10px;">
                    <div style="flex:1">
                        <label>Código</label>
                        <input type="text" name="codigo" id="codigo" class="form-input" required style="width:100%;">
                    </div>
                    <div style="flex:1">
                        <label>Grupo</label>
                        <input type="text" name="grupo" id="grupo" class="form-input" required style="width:100%;">
                    </div>
                </div>

                <div style="display:flex; gap:10px; margin-top:10px;">
                    <div style="flex:1">
                        <label>Unidades</label>
                        <input type="number" name="unidades" id="unidades" class="form-input" value="3" required style="width:100%;">
                    </div>
                    <div style="flex:1">
                        <label>Estado</label>
                        <select name="activo" id="activo" class="form-select" style="width:100%;">
                            <option value="1">Activa</option>
                            <option value="0">Inactiva</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%; margin-top:20px;">Guardar</button>
            </form>
        </div>
    </div>
</div>

<script>
let isEdit = false;
document.addEventListener('DOMContentLoaded', loadMaterias);

async function loadMaterias() {
    try {
        const res = await fetch('../../api/superadmin/get_all_materias.php');
        const data = await res.json();
        const tbody = document.getElementById('tabla-materias');
        tbody.innerHTML = '';

        if(data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6">No hay materias registradas.</td></tr>';
            return;
        }

        data.forEach(m => {
            const materiaJson = JSON.stringify(m).replace(/"/g, '&quot;');
            tbody.innerHTML += `
                <tr>
                    <td>${m.codigo}</td>
                    <td>${m.nombre}</td>
                    <td>${m.grupo}</td>
                    <td>${m.unidades}</td>
                    <td>${m.activo == 1 ? '<span style="color:green">Activa</span>' : '<span style="color:gray">Inactiva</span>'}</td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="editMateria(${materiaJson})">Editar</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteMateria(${m.id})">Borrar</button>
                    </td>
                </tr>
            `;
        });
    } catch(err) { console.error(err); }
}

function openModal() {
    isEdit = false;
    document.getElementById('form-materia').reset();
    document.getElementById('materiaId').value = '';
    document.getElementById('modal-materia').style.display = 'flex';
}

function closeModal() {
    document.getElementById('modal-materia').style.display = 'none';
}

window.editMateria = function(m) {
    isEdit = true;
    document.getElementById('materiaId').value = m.id;
    document.getElementById('nombre').value = m.nombre;
    document.getElementById('codigo').value = m.codigo;
    document.getElementById('grupo').value = m.grupo;
    document.getElementById('unidades').value = m.unidades;
    document.getElementById('activo').value = m.activo;
    document.getElementById('modal-materia').style.display = 'flex';
}

document.getElementById('form-materia').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    const method = isEdit ? 'PUT' : 'POST';
    
    try {
        const res = await fetch('../../api/superadmin/materia_crud_admin.php', {
            method: method,
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        });
        if(res.ok) {
            closeModal();
            loadMaterias();
        } else {
            alert("Error al guardar materia");
        }
    } catch(err) { alert("Error de conexión"); }
});

window.deleteMateria = async function(id) {
    if(!confirm('¿Borrar materia?')) return;
    try {
        await fetch('../../api/superadmin/materia_crud_admin.php', {
            method: 'DELETE',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id})
        });
        loadMaterias();
    } catch(err) { alert("Error de conexión"); }
}
</script>
</body>
</html>