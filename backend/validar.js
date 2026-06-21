// js/validaciones.js
// Validaciones front-end para PNK Inmobiliaria

// ── Utilidades ─────────────────────────────────────────────────────────────

/**
 * Muestra un mensaje de error bajo un campo
 */
function mostrarError(input, mensaje) {
    limpiarError(input);
    input.classList.add('is-invalid');
    const div = document.createElement('div');
    div.className = 'invalid-feedback';
    div.textContent = mensaje;
    input.parentNode.appendChild(div);
}

/**
 * Limpia el error de un campo
 */
function limpiarError(input) {
    input.classList.remove('is-invalid', 'is-valid');
    const feedback = input.parentNode.querySelector('.invalid-feedback');
    if (feedback) feedback.remove();
}

/**
 * Marca un campo como válido
 */
function marcarValido(input) {
    limpiarError(input);
    input.classList.add('is-valid');
}

/**
 * Muestra alertas globales en un contenedor
 */
function mostrarAlertas(contenedorId, errores, tipo = 'danger') {
    const contenedor = document.getElementById(contenedorId);
    if (!contenedor) return;
    contenedor.innerHTML = errores.map(e =>
        `<div class="alert alert-${tipo} py-2">${e}</div>`
    ).join('');
    contenedor.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function limpiarAlertas(contenedorId) {
    const contenedor = document.getElementById(contenedorId);
    if (contenedor) contenedor.innerHTML = '';
}

// ── Validadores individuales ───────────────────────────────────────────────

function validarRutChileno(rut) {
    const rutLimpio = rut.replace(/[^0-9kK]/g, '');
    if (rutLimpio.length < 8) return false;
    const cuerpo = rutLimpio.slice(0, -1);
    const dv = rutLimpio.slice(-1).toLowerCase();
    let suma = 0;
    let multiplo = 2;
    for (let i = cuerpo.length - 1; i >= 0; i--) {
        suma += parseInt(cuerpo[i]) * multiplo;
        multiplo = multiplo < 7 ? multiplo + 1 : 2;
    }
    const dvEsperado = 11 - (suma % 11);
    const dvFinal = dvEsperado === 11 ? '0' : dvEsperado === 10 ? 'k' : String(dvEsperado);
    return dv === dvFinal;
}

function formatearRut(rut) {
    const rutLimpio = rut.replace(/[^0-9kK]/g, '');
    if (rutLimpio.length < 2) return rutLimpio;
    const cuerpo = rutLimpio.slice(0, -1);
    const dv = rutLimpio.slice(-1).toUpperCase();
    const cuerpoFormateado = cuerpo.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    return `${cuerpoFormateado}-${dv}`;
}

function validarEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function validarTelefonoChileno(tel) {
    const telLimpio = tel.replace(/\s+/g, '');
    return /^\+?56?9\d{8}$/.test(telLimpio);
}

function validarMayorEdad(fechaStr) {
    if (!fechaStr) return false;
    const hoy = new Date();
    const nacimiento = new Date(fechaStr);
    const edad = hoy.getFullYear() - nacimiento.getFullYear();
    const m = hoy.getMonth() - nacimiento.getMonth();
    return edad > 18 || (edad === 18 && m >= 0);
}

function validarPassword(psw) {
    return psw.length >= 8;
}

// ── Validación en tiempo real (on blur) ────────────────────────────────────

function agregarValidacionTiempoReal() {
    // RUT
    document.querySelectorAll('[name="rut"]').forEach(input => {
        input.addEventListener('blur', () => {
            const val = input.value.trim();
            if (!val) { mostrarError(input, 'El RUT es obligatorio.'); return; }
            if (!validarRutChileno(val)) {
                mostrarError(input, 'El RUT ingresado no es válido.');
            } else {
                input.value = formatearRut(val);
                marcarValido(input);
            }
        });
    });

    // Email
    document.querySelectorAll('[name="email"],[name="correo"]').forEach(input => {
        input.addEventListener('blur', () => {
            const val = input.value.trim();
            if (!val) { mostrarError(input, 'El email es obligatorio.'); return; }
            if (!validarEmail(val)) {
                mostrarError(input, 'Ingresa un email válido.');
            } else {
                marcarValido(input);
            }
        });
    });

    // Teléfono
    document.querySelectorAll('[name="telefono"]').forEach(input => {
        input.addEventListener('blur', () => {
            const val = input.value.trim();
            if (!val) { mostrarError(input, 'El teléfono es obligatorio.'); return; }
            if (!validarTelefonoChileno(val)) {
                mostrarError(input, 'Formato: +569XXXXXXXX o 569XXXXXXXX.');
            } else {
                marcarValido(input);
            }
        });
    });

    // Contraseña
    document.querySelectorAll('[name="psw"]').forEach(input => {
        input.addEventListener('input', () => {
            const val = input.value;
            if (!validarPassword(val)) {
                mostrarError(input, 'Mínimo 8 caracteres.');
            } else {
                marcarValido(input);
            }
        });
    });

    // Repetir contraseña
    document.querySelectorAll('[name="psw-repeat"]').forEach(input => {
        input.addEventListener('blur', () => {
            const original = document.querySelector('[name="psw"]');
            if (!original) return;
            if (input.value !== original.value) {
                mostrarError(input, 'Las contraseñas no coinciden.');
            } else {
                marcarValido(input);
            }
        });
    });

    // Fecha nacimiento (mayoría de edad)
    document.querySelectorAll('[name="fecha_nacimiento"]').forEach(input => {
        input.addEventListener('change', () => {
            if (!validarMayorEdad(input.value)) {
                mostrarError(input, 'Debes ser mayor de 18 años.');
            } else {
                marcarValido(input);
            }
        });
    });

    // Nombre y apellido
    document.querySelectorAll('[name="nombre"],[name="apellido"]').forEach(input => {
        input.addEventListener('blur', () => {
            if (!input.value.trim()) {
                mostrarError(input, `Este campo es obligatorio.`);
            } else {
                marcarValido(input);
            }
        });
    });
}

// ── Formulario de Registro (propietario / gestor) ──────────────────────────

function initFormRegistro(endpoint) {
    const form = document.getElementById('formRegistro');
    if (!form) return;

    agregarValidacionTiempoReal();

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        limpiarAlertas('alertasRegistro');

        // Validar todos los campos antes de enviar
        let valido = true;

        // RUT
        const rutInput = form.querySelector('[name="rut"]');
        if (rutInput && !validarRutChileno(rutInput.value.trim())) {
            mostrarError(rutInput, 'RUT inválido.');
            valido = false;
        }

        // Email
        const emailInput = form.querySelector('[name="email"]');
        if (emailInput && !validarEmail(emailInput.value.trim())) {
            mostrarError(emailInput, 'Email inválido.');
            valido = false;
        }

        // Teléfono
        const telInput = form.querySelector('[name="telefono"]');
        if (telInput && !validarTelefonoChileno(telInput.value.trim())) {
            mostrarError(telInput, 'Teléfono inválido.');
            valido = false;
        }

        // Contraseñas
        const pswInput = form.querySelector('[name="psw"]');
        const pswRepeat = form.querySelector('[name="psw-repeat"]');
        if (pswInput && !validarPassword(pswInput.value)) {
            mostrarError(pswInput, 'Contraseña muy corta (mínimo 8 caracteres).');
            valido = false;
        }
        if (pswRepeat && pswInput && pswRepeat.value !== pswInput.value) {
            mostrarError(pswRepeat, 'Las contraseñas no coinciden.');
            valido = false;
        }

        // Edad
        const fechaInput = form.querySelector('[name="fecha_nacimiento"]');
        if (fechaInput && !validarMayorEdad(fechaInput.value)) {
            mostrarError(fechaInput, 'Debes ser mayor de 18 años.');
            valido = false;
        }

        // Términos
        const terminos = form.querySelector('[name="terminos"]');
        if (terminos && !terminos.checked) {
            mostrarError(terminos, 'Debes aceptar los términos y condiciones.');
            valido = false;
        }

        if (!valido) return;

        const btn = form.querySelector('[type="submit"]');
        btn.disabled = true;
        btn.textContent = 'Registrando...';

        try {
            const formData = new FormData(form);
            const res = await fetch(endpoint, { method: 'POST', body: formData });
            const data = await res.json();

            if (data.success) {
                mostrarAlertas('alertasRegistro', [data.mensaje], 'success');
                form.reset();
                setTimeout(() => { window.location.href = 'iniciosesion.php'; }, 2000);
            } else {
                mostrarAlertas('alertasRegistro', data.errores || ['Error desconocido.']);
            }
        } catch (err) {
            mostrarAlertas('alertasRegistro', ['Error de conexión. Intenta nuevamente.']);
        } finally {
            btn.disabled = false;
            btn.textContent = 'Registrarse';
        }
    });
}

// ── Formulario de Login ────────────────────────────────────────────────────

function initFormLogin() {
    const form = document.getElementById('formLogin');
    if (!form) return;

    agregarValidacionTiempoReal();

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        limpiarAlertas('alertasLogin');

        const correo = form.querySelector('[name="correo"]');
        const psw    = form.querySelector('[name="psw"]');
        let valido = true;

        if (!correo.value.trim()) { mostrarError(correo, 'El correo es obligatorio.'); valido = false; }
        else if (!validarEmail(correo.value.trim())) { mostrarError(correo, 'Email inválido.'); valido = false; }

        if (!psw.value) { mostrarError(psw, 'La contraseña es obligatoria.'); valido = false; }

        if (!valido) return;

        const btn = form.querySelector('[type="submit"]');
        btn.disabled = true;
        btn.textContent = 'Entrando...';

        try {
            const formData = new FormData(form);
            const res = await fetch('backend/iniciar_sesion.php', { method: 'POST', body: formData });
            const data = await res.json();

            if (data.success) {
                mostrarAlertas('alertasLogin', [`¡Bienvenido, ${data.nombre}!`], 'success');
                setTimeout(() => { window.location.href = data.redirect; }, 1000);
            } else {
                mostrarAlertas('alertasLogin', data.errores || ['Error al iniciar sesión.']);
            }
        } catch (err) {
            mostrarAlertas('alertasLogin', ['Error de conexión. Intenta nuevamente.']);
        } finally {
            btn.disabled = false;
            btn.textContent = 'Iniciar Sesión';
        }
    });
}

// ── CRUD de propiedades ────────────────────────────────────────────────────

async function cargarPropiedades() {
    try {
        const res = await fetch('backend/propiedades_controller.php?accion=listar');
        const data = await res.json();
        const lista = document.getElementById('listaPropiedades');
        if (!lista) return;

        if (!data.success || data.data.length === 0) {
            lista.innerHTML = '<p class="text-muted">No tienes propiedades registradas.</p>';
            return;
        }

        lista.innerHTML = data.data.map(p => `
            <div class="list-group-item d-flex justify-content-between align-items-center" id="prop-${p.id}">
                <div>
                    <strong>${p.titulo}</strong> — ${p.tipo} — $${Number(p.precio).toLocaleString('es-CL')}
                    <span class="badge ${estadoBadge(p.estado)} ms-2">${p.estado}</span>
                </div>
                <div>
                    <button class="btn btn-sm btn-primary me-1" onclick="abrirEditar(${p.id})">Editar</button>
                    <button class="btn btn-sm btn-danger" onclick="confirmarEliminar(${p.id})">Eliminar</button>
                </div>
            </div>
        `).join('');
    } catch (e) {
        console.error('Error cargando propiedades:', e);
    }
}

function estadoBadge(estado) {
    const map = { Publicada: 'bg-success', Pendiente: 'bg-warning text-dark', Vendida: 'bg-secondary', Arrendada: 'bg-info text-dark' };
    return map[estado] || 'bg-secondary';
}

async function confirmarEliminar(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar esta propiedad? Esta acción no se puede deshacer.')) return;

    const formData = new FormData();
    formData.append('accion', 'eliminar');
    formData.append('id', id);

    const res = await fetch('backend/propiedades_controller.php', { method: 'POST', body: formData });
    const data = await res.json();

    if (data.success) {
        document.getElementById(`prop-${id}`)?.remove();
        alert(data.mensaje);
    } else {
        alert(data.mensaje || 'Error al eliminar.');
    }
}

async function abrirEditar(id) {
    const res = await fetch(`backend/propiedades_controller.php?accion=obtener&id=${id}`);
    const data = await res.json();
    if (!data.success) { alert('No se pudo cargar la propiedad.'); return; }

    const p = data.data;
    // Rellenar formulario modal de edición
    document.getElementById('editId').value       = p.id;
    document.getElementById('editTitulo').value   = p.titulo;
    document.getElementById('editDescripcion').value = p.descripcion || '';
    document.getElementById('editTipo').value     = p.tipo;
    document.getElementById('editPrecio').value   = p.precio;
    document.getElementById('editDireccion').value = p.direccion;
    document.getElementById('editCiudad').value   = p.ciudad;
    document.getElementById('editSuperficie').value = p.superficie || '';
    document.getElementById('editHabitaciones').value = p.habitaciones;
    document.getElementById('editBanos').value    = p.banos;
    document.getElementById('editEstado').value   = p.estado;

    const modal = new bootstrap.Modal(document.getElementById('modalEditar'));
    modal.show();
}

function initFormCrearPropiedad() {
    const form = document.getElementById('formCrearPropiedad');
    if (!form) return;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        limpiarAlertas('alertasPropiedad');

        const titulo  = form.querySelector('[name="titulo"]');
        const tipo    = form.querySelector('[name="tipo"]');
        const precio  = form.querySelector('[name="precio"]');
        const dir     = form.querySelector('[name="direccion"]');
        const ciudad  = form.querySelector('[name="ciudad"]');
        let valido = true;

        [titulo, tipo, precio, dir, ciudad].forEach(inp => {
            if (!inp.value.trim()) { mostrarError(inp, 'Campo obligatorio.'); valido = false; }
        });

        if (precio && (isNaN(precio.value) || Number(precio.value) <= 0)) {
            mostrarError(precio, 'El precio debe ser mayor a 0.');
            valido = false;
        }

        if (!valido) return;

        const btn = form.querySelector('[type="submit"]');
        btn.disabled = true;
        btn.textContent = 'Guardando...';

        const formData = new FormData(form);
        formData.append('accion', 'crear');

        const res = await fetch('backend/propiedades_controller.php', { method: 'POST', body: formData });
        const data = await res.json();

        if (data.success) {
            mostrarAlertas('alertasPropiedad', [data.mensaje], 'success');
            form.reset();
            form.querySelectorAll('.is-valid').forEach(i => i.classList.remove('is-valid'));
            cargarPropiedades();
        } else {
            mostrarAlertas('alertasPropiedad', data.errores || ['Error al crear propiedad.']);
        }

        btn.disabled = false;
        btn.textContent = 'Guardar Propiedad';
    });
}

function initFormEditarPropiedad() {
    const form = document.getElementById('formEditarPropiedad');
    if (!form) return;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        limpiarAlertas('alertasEditar');

        const formData = new FormData(form);
        formData.append('accion', 'editar');

        const btn = form.querySelector('[type="submit"]');
        btn.disabled = true;

        const res = await fetch('backend/propiedades_controller.php', { method: 'POST', body: formData });
        const data = await res.json();

        if (data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditar'));
            modal?.hide();
            cargarPropiedades();
            setTimeout(() => alert(data.mensaje), 300);
        } else {
            mostrarAlertas('alertasEditar', data.errores || [data.mensaje]);
        }

        btn.disabled = false;
    });
}

// ── Inicialización automática al cargar el DOM ─────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    // Login
    initFormLogin();

    // Registros (el endpoint se define con data-endpoint en el form)
    const formReg = document.getElementById('formRegistro');
    if (formReg) {
        const endpoint = formReg.dataset.endpoint || 'backend/registrar_usuario.php';
        initFormRegistro(endpoint);
    }

    // Dashboard propietario
    if (document.getElementById('listaPropiedades')) {
        cargarPropiedades();
        initFormCrearPropiedad();
        initFormEditarPropiedad();
    }
});
