document.addEventListener('DOMContentLoaded', () => {
    loadGrupos();
});

async function loadGrupos() {
    const container = document.getElementById('grupos-container');
    container.innerHTML = '<p style="text-align:center; color:#666;">Cargando grupos...</p>';

    try {
        const res = await fetch('../../api/maestro/get_grupos.php');
        const grupos = await res.json();

        container.innerHTML = '';

        if (grupos.error) {
            container.innerHTML = `<p style="color:red; text-align:center;">Error: ${grupos.error}</p>`;
            return;
        }

        if (grupos.length === 0) {
            container.innerHTML = '<p style="text-align:center;">No tienes grupos asignados aún.</p>';
            return;
        }

        grupos.forEach(g => {
            const card = document.createElement('div');
            card.className = 'card';
            card.style.textAlign = 'center';
            
            // Construimos el HTML de la tarjeta
            card.innerHTML = `
                <div class="card-body">
                    <h1 style="font-size:3rem; color:var(--primary); margin:0;">${g.grupo}</h1>
                    <p style="color:#666; margin-bottom:15px;">GRUPO</p>
                    <span class="badge badge-info" style="background:#17a2b8; color:white; padding:5px 10px; border-radius:10px; font-size:0.8rem;">
                        ${g.total} Materia(s)
                    </span>
                    <br><br>
                    <a href="materias.php" class="btn btn-primary">Ver Materias</a>
                </div>
            `;
            container.appendChild(card);
        });

    } catch (error) {
        console.error(error);
        container.innerHTML = '<p style="color:red; text-align:center;">Error de conexión.</p>';
    }
}