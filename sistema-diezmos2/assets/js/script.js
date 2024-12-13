// script.js
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('sobreForm');
    const addOfrendaBtn = document.getElementById('addOfrenda');
    const addTransferenciaBtn = document.getElementById('addTransferencia');
    const buscarPersonaBtn = document.getElementById('buscarPersona');
    let ofrendaCount = 1;
    let transferenciaCount = 0;

    // Búsqueda de persona por cédula
    buscarPersonaBtn.addEventListener('click', async function() {
        const cedula = document.getElementById('cedula').value;
        if (!cedula) {
            alert('Por favor ingrese una cédula');
            return;
        }

        try {
            const response = await fetch(`buscar_persona.php?cedula=${cedula}`);
            const data = await response.json();
            
            if (data.success) {
                document.getElementById('nombre').value = data.persona.nombre;
                document.getElementById('apellido').value = data.persona.apellido;
                document.getElementById('email').value = data.persona.email || '';
                document.getElementById('telefono').value = data.persona.telefono || '';
                document.getElementById('iglesia').value = data.persona.iglesia || '';
            } else {
                // Limpiar campos si no se encuentra la persona
                document.getElementById('nombre').value = '';
                document.getElementById('apellido').value = '';
                document.getElementById('email').value = '';
                document.getElementById('telefono').value = '';
                document.getElementById('iglesia').value = '';
            }
        } catch (error) {
            console.error('Error al buscar persona:', error);
            alert('Error al buscar persona');
        }
    });

    // Agregar nueva ofrenda
    addOfrendaBtn.addEventListener('click', function() {
        const template = document.getElementById('ofrendaTemplate');
        const container = document.getElementById('ofrendasContainer');
        const clone = document.importNode(template.content, true);

        // Reemplazar INDEX en los nombres de los campos
        clone.querySelectorAll('[name*="INDEX"]').forEach(elem => {
            elem.name = elem.name.replace('INDEX', ofrendaCount);
        });

        // Agregar botón de eliminar
        const removeBtn = clone.querySelector('.btn-remove');
        if (removeBtn) {
            removeBtn.addEventListener('click', function(e) {
                e.target.closest('.ofrenda-item').remove();
                calcularTotal();
            });
        }

        container.appendChild(clone);
        ofrendaCount++;
        calcularTotal();
    });

    // Agregar nueva transferencia
    addTransferenciaBtn.addEventListener('click', function() {
        const template = document.getElementById('transferenciaTemplate');
        const container = document.getElementById('transferenciasContainer');
        const clone = document.importNode(template.content, true);

        // Reemplazar INDEX en los nombres de los campos
        clone.querySelectorAll('[name*="INDEX"]').forEach(elem => {
            elem.name = elem.name.replace('INDEX', transferenciaCount);
        });

        // Manejar selección de banco
        const bancoSelect = clone.querySelector('.banco-select');
        const bancoOtro = clone.querySelector('.banco-otro');
        
        bancoSelect.addEventListener('change', function() {
            bancoOtro.style.display = this.value === 'otro' ? 'block' : 'none';
            bancoOtro.required = this.value === 'otro';
        });

        // Agregar botón de eliminar
        const removeBtn = clone.querySelector('.btn-remove');
        if (removeBtn) {
            removeBtn.addEventListener('click', function(e) {
                e.target.closest('.transferencia-item').remove();
            });
        }

        container.appendChild(clone);
        transferenciaCount++;
    });

    // Calcular total de ofrendas
    function calcularTotal() {
        const ofrendas = document.querySelectorAll('.ofrenda-item');
        let totalUSD = 0;
        let totalVES = 0;

        ofrendas.forEach(ofrenda => {
            const monto = parseFloat(ofrenda.querySelector('input[name*="[monto]"]').value) || 0;
            const moneda = ofrenda.querySelector('select[name*="[moneda]"]').value;
            
            if (moneda === '1') { // USD
                totalUSD += monto;
            } else if (moneda === '2') { // VES
                totalVES += monto;
            }
        });

        // Actualizar totales en la interfaz si existen los elementos
        if (document.getElementById('totalUSD')) {
            document.getElementById('totalUSD').textContent = totalUSD.toFixed(2);
        }
        if (document.getElementById('totalVES')) {
            document.getElementById('totalVES').textContent = totalVES.toFixed(2);
        }
    }

    // Escuchar cambios en montos
    document.addEventListener('input', function(e) {
        if (e.target.matches('input[name*="[monto]"]')) {
            calcularTotal();
        }
    });

    // Validación del formulario antes de enviar
    form.addEventListener('submit', function(e) {
        const cedula = document.getElementById('cedula').value;
        const nombre = document.getElementById('nombre').value;
        const apellido = document.getElementById('apellido').value;
        
        if (!cedula || !nombre || !apellido) {
            e.preventDefault();
            alert('Por favor complete los datos de la persona');
            return;
        }

        const ofrendas = document.querySelectorAll('.ofrenda-item');
        if (ofrendas.length === 0) {
            e.preventDefault();
            alert('Debe agregar al menos una ofrenda');
            return;
        }

        // Validar campos de banco "otro"
        const bancosOtro = document.querySelectorAll('.banco-select');
        let isValid = true;
        
        bancosOtro.forEach(select => {
            if (select.value === 'otro') {
                const otroInput = select.nextElementSibling;
                if (!otroInput.value.trim()) {
                    isValid = false;
                    otroInput.classList.add('error');
                }
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Por favor complete el nombre del banco');
        }
    });
});