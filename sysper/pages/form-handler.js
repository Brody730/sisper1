document.addEventListener('DOMContentLoaded', function () {
    // Función para mostrar mensajes
    function mostrarMensaje(mensaje, esError = false) {
        let mensajeDiv = document.getElementById('mensajeFormulario');
        if (!mensajeDiv) {
            mensajeDiv = document.createElement('div');
            mensajeDiv.id = 'mensajeFormulario';
            document.getElementById('registroForm').insertAdjacentElement('beforebegin', mensajeDiv);
        }
        mensajeDiv.className = esError ? 'alert alert-danger' : 'alert alert-success';
        mensajeDiv.textContent = mensaje;
        mensajeDiv.scrollIntoView({ behavior: 'smooth' });
        setTimeout(() => mensajeDiv.remove(), 5000);
    }

    // Inicializar Select2 para la selección múltiple de empleados
    $('.js-example-basic-multiple').select2({
        placeholder: 'Selecciona los empleados',
        ajax: {
            url: 'obtener_empleados.php', // Endpoint para obtener la lista de empleados
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term, page: params.page };
            },
            processResults: function (data) {
                return { results: data };
            },
            cache: true
        }
    });

    // Validar horarios
    function validarHorarios() {
        const horaInicio = document.getElementById('hora_inicio').value;
        const horaTermino = document.getElementById('hora_termino').value;

        if (horaInicio && horaTermino) {
            if (horaInicio >= horaTermino) {
                mostrarMensaje('La hora de inicio debe ser anterior a la hora de término', true);
                return false;
            }
        }
        return true;
    }

    // Validar fecha
    function validarFecha() {
        const fecha = new Date(document.getElementById('fecha_act').value);
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0); // Solo comparar fechas, ignorando el tiempo

        if (fecha < hoy) {
            mostrarMensaje('La fecha no puede ser anterior a hoy', true);
            return false;
        }
        return true;
    }

    // Manejar envío del formulario
    document.getElementById('registroForm').addEventListener('submit', function (e) {
        e.preventDefault();

        // Validaciones
        if (!validarHorarios() || !validarFecha()) {
            return;
        }

        // Validar que se haya seleccionado al menos un empleado
        const empleadosSeleccionados = $('#empleados').select2('data');
        if (empleadosSeleccionados.length === 0) {
            mostrarMensaje('Debe seleccionar al menos un empleado', true);
            return;
        }

        // Mostrar indicador de carga
        const submitButton = this.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.textContent;
        submitButton.textContent = 'Enviando...';
        submitButton.disabled = true;

        const formData = new FormData(this);

        // Debug: Mostrar datos enviados
        console.log('Datos a enviar:');
        for (let pair of formData.entries()) {
            console.log(`${pair[0]}: ${pair[1]}`);
        }

        fetch('procesar.php', {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                console.log('Respuesta del servidor:', data);

                if (data.success) {
                    mostrarMensaje('Registro completado con éxito');
                    this.reset();
                    $('#empleados').val(null).trigger('change'); // Limpiar Select2
                } else {
                    mostrarMensaje(data.message || 'Error al procesar el registro', true);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('Error al procesar el registro: ' + error.message, true);
            })
            .finally(() => {
                // Restaurar el botón
                submitButton.textContent = originalButtonText;
                submitButton.disabled = false;
            });
    });
});
