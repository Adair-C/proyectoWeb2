document.addEventListener('DOMContentLoaded', async () => {
    // Obtenemos el ID desde un input oculto en el HTML
    const materiaId = document.getElementById('page-materia-id').value;
    
    const tbody = document.getElementById('tabla-body');
    tbody.innerHTML = '<tr><td>Cargando datos...</td></tr>';

    try {
        const res = await fetch(`../../api/maestro/get_calificaciones_tabla.php?materia_id=${materiaId}`);
        const data = await res.json();

        const materia = data.materia;
        const alumnos = data.alumnos;
        const notas = data.notas; 

        // Actualizar título
        document.getElementById('materia-titulo').textContent = `${materia.nombre} (${materia.grupo})`;

        // 1. Construir Cabecera Dinámica (U#1... U#N)
        const thead = document.getElementById('tabla-head');
        let headHtml = '<th>Alumno</th>';
        for(let i=1; i <= materia.unidades; i++) {
            headHtml += `<th style="text-align:center">U#${i}</th>`;
        }
        thead.innerHTML = headHtml;

        // 2. Construir Filas
        tbody.innerHTML = '';
        if(alumnos.length === 0) {
            tbody.innerHTML = '<tr><td colspan="100" style="text-align:center">No hay alumnos inscritos.</td></tr>';
            return;
        }

        alumnos.forEach(alumno => {
            let rowHtml = `<tr><td>${alumno.nombre_completo}</td>`;
            
            for(let i=1; i <= materia.unidades; i++) {
                let val = (notas[alumno.id] && notas[alumno.id][i]) ? notas[alumno.id][i] : '';
                
                rowHtml += `
                    <td style="text-align:center">
                        <input type="number" 
                               class="input-grade" 
                               value="${val}" 
                               min="0" max="100"
                               step="0.01"
                               data-alumno="${alumno.id}"
                               data-materia="${materiaId}"
                               data-unidad="${i}"
                               onchange="window.saveGrade(this)"> 
                    </td>`;
            }
            rowHtml += '</tr>';
            tbody.innerHTML += rowHtml;
        });

    } catch (error) {
        console.error(error);
        tbody.innerHTML = '<tr><td colspan="100" style="color:red">Error al cargar datos.</td></tr>';
    }
});

// Función para guardar calificación individualmente
window.saveGrade = async function(input) {
    const data = {
        alumno_id: input.dataset.alumno,
        materia_id: input.dataset.materia,
        unidad: input.dataset.unidad,
        calificacion: input.value
    };
    
    input.style.borderColor = "#ffc107"; 

    try {
        const res = await fetch('../../api/maestro/guardar_calificacion.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        });
        
        if(res.ok) {
            input.style.borderColor = "#28a745"; 
        } else {
            input.style.borderColor = "#dc3545";
        }
    } catch(err) { 
        input.style.borderColor = "#dc3545"; 
    }
}