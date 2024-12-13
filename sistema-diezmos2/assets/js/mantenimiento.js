// mantenimiento.js
document.addEventListener('DOMContentLoaded', function() {
    // Manejo de tabs
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const tabId = button.getAttribute('data-tab');
            
            // Remover clases active
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Agregar clase active al tab seleccionado
            button.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });

    // Manejo de modales
    const modales = document.querySelectorAll('.modal');
    const closeButtons = document.querySelectorAll('.close');

    closeButtons.forEach(button => {
        button.addEventListener('click', () => {
            button.closest('.modal').style.display = 'none';
        });
    });

    window.addEventListener('click', (e) => {
        modales.forEach(modal => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    });

    // Manejar envío de formularios
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            await guardarRegistro(form);
        });
    });
});

// Función auxiliar para capitalizar primera letra
function capitalize(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

// Mostrar modal para nuevo registro
function mostrarModal(tipo) {
    const modal = document.getElementById(`modal${capitalize(tipo)}`);
    const form = document.getElementById(`form${capitalize(tipo)}`);
    const titulo = document.getElementById(`tituloModal${capitalize(tipo)}`);
    
    // Limpiar formulario
    form.reset();
    form.querySelector('input[name="id"]').value = '';
    titulo.textContent = `Agregar ${capitalize(tipo)}`;
    
    modal.style.display = 'block';
}

// Cargar y mostrar datos para edición
async function editarRegistro(tipo, id) {
    try {
        const response = await fetch(`obtener_registro.php?tipo=${tipo}&id=${id}`);
        const data = await response.json();
        
        if (data.success) {
            const modal = document.getElementById(`modal${capitalize(tipo)}`);
            const form = document.getElementById(`form${capitalize(tipo)}`);
            const titulo = document.getElementById(`tituloModal${capitalize(tipo)}`);
            
            // Llenar formulario con datos
            form.querySelector('input[name="id"]').value = data.registro.id;
            Object.keys(data.registro).forEach(key => {
                const input = form.querySelector(`[name="${key}"]`);
                if (input) {
                    input.value = data.registro[key];
                }
            });
            
            titulo.textContent = `Editar ${capitalize(tipo)}`;
            modal.style.display = 'block';
        } else {
            alert('No se encontró el registro');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al cargar los datos');
    }
}

// Cambiar estado (activar/desactivar)
async function toggleEstado(tipo, id) {
    if (confirm('¿Está seguro que desea cambiar el estado de este registro?')) {
        try {
            const response = await fetch('procesar_mantenimiento.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `tipo=${tipo}&id=${id}&accion=toggle`
            });
            
            const data = await response.json();
            
            if (data.success) {
                location.reload();
            } else {
                alert(data.error || 'Error al cambiar el estado');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al procesar la solicitud');
        }
    }
}

// Guardar registro (crear/actualizar)
async function guardarRegistro(form) {
    try {
        const formData = new FormData(form);
        
        const response = await fetch('procesar_mantenimiento.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            form.closest('.modal').style.display = 'none';
            location.reload();
        } else {
            alert(data.error || 'Error al guardar el registro');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al guardar el registro');
    }
}

// Validación de formularios
function validarFormulario(tipo, form) {
    const campos = form.querySelectorAll('input[required], textarea[required]');
    let valido = true;
    
    campos.forEach(campo => {
        if (!campo.value.trim()) {
            campo.classList.add('error');
            valido = false;
        } else {
            campo.classList.remove('error');
        }
    });
    
    // Validaciones específicas por tipo
    switch(tipo) {
        case 'moneda':
            const codigo = form.querySelector('input[name="codigo"]');
            if (codigo.value.length > 3) {
                codigo.classList.add('error');
                alert('El código de moneda no debe exceder 3 caracteres');
                valido = false;
            }
            break;
            
        case 'banco':
            const codigoBanco = form.querySelector('input[name="codigo"]');
            if (codigoBanco.value && codigoBanco.value.length > 20) {
                codigoBanco.classList.add('error');
                alert('El código de banco no debe exceder 20 caracteres');
                valido = false;
            }
            break;
    }
    
    return valido;
}

// Manejar errores de red
function manejarError(error) {
    console.error('Error:', error);
    alert('Ha ocurrido un error en la conexión. Por favor, intente nuevamente.');
}

// Actualizar tabla después de cambios
function actualizarTabla(tipo, data) {
    const tbody = document.querySelector(`#${tipo}s tbody`);
    tbody.innerHTML = ''; // Limpiar tabla
    
    data.forEach(registro => {
        const tr = document.createElement('tr');
        tr.setAttribute('data-id', registro.id);
        
        // Crear celdas según el tipo
        switch(tipo) {
            case 'moneda':
                tr.innerHTML = `
                    <td>${registro.codigo}</td>
                    <td>${registro.nombre}</td>
                    <td>${registro.simbolo}</td>
                    <td><span class="estado-${registro.activo ? 'activo' : 'inactivo'}">${registro.activo ? 'Activo' : 'Inactivo'}</span></td>
                    <td>
                        <button class="btn-edit" onclick="editarRegistro('moneda', ${registro.id})">Editar</button>
                        <button class="btn-toggle" onclick="toggleEstado('moneda', ${registro.id})">${registro.activo ? 'Desactivar' : 'Activar'}</button>
                    </td>
                `;
                break;
                
            // Agregar casos para otros tipos...
        }
        
        tbody.appendChild(tr);
    });
}