<?php
// views/usuarios/crear.php
// $roles viene desde el controlador
$hoy = date('Y-m-d');
?>
<h2>Registrar Usuario</h2>

<form method="POST" action="index.php?action=guardar" id="formRegistro" autocomplete="off" enctype="multipart/form-data">
  <input type="text" name="nombre" id="nombre" placeholder="Nombre" required><br>

  <input type="email" name="correo_electronico" id="correo_electronico" placeholder="Correo Electrónico" required>
  <small id="emailError" style="color:red; display:none;"></small><br>

  <select name="id_rol" id="id_rol" required>
    <option value="">Seleccione un rol</option>
    <?php foreach ($roles as $r): ?>
      <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['nombre_cargo']) ?></option>
    <?php endforeach; ?>
  </select><br>

  <input type="date" name="fecha_ingreso" id="fecha_ingreso" max="<?= $hoy ?>" required><br>
  <small id="fechaError" style="color:red; display:none;">La fecha no puede ser futura.</small><br>
  <!-- Firma -->
  <label for="firma">Firma:</label>
  <input type="file" name="firma" accept="image/*" required>
  <button type="submit" id="btnRegistrar" disabled>Registrar</button>
</form>

<script>
const nombre = document.getElementById('nombre');
const correo = document.getElementById('correo_electronico');
const rol = document.getElementById('id_rol');
const fecha = document.getElementById('fecha_ingreso');
const btn = document.getElementById('btnRegistrar');
const emailError = document.getElementById('emailError');
const fechaError = document.getElementById('fechaError');
const hoy = new Date().toISOString().split('T')[0];

async function validarCorreo() {
  if (!correo.value) return false;
  try {
    const res = await fetch('validar_correo.php?correo=' + encodeURIComponent(correo.value));
    if (!res.ok) throw new Error('Error en la petición');
    const data = await res.json();
    if (data.existe) {
      emailError.innerText = 'El correo ya está registrado';
      emailError.style.display = 'inline';
      return false;
    } else {
      emailError.style.display = 'none';
      return true;
    }
  } catch (e) {
    console.error(e);
    // si la validación remota falla, puedes decidir bloquear o permitir.
    // Aquí devolvemos true para no bloquear por errores en la petición.
    return true;
  }
}

function validarFecha() {
  if (fecha.value && fecha.value > hoy) {
    fechaError.style.display = 'inline';
    return false;
  }
  fechaError.style.display = 'none';
  return true;
}

async function validarFormulario() {
  let valido = true;
  if (!nombre.value.trim()) valido = false;
  if (!rol.value) valido = false;
  if (!validarFecha()) valido = false;
  if (!(await validarCorreo())) valido = false;
  btn.disabled = !valido;
}

[nombre, correo, rol, fecha].forEach(el => el.addEventListener('input', validarFormulario));
correo.addEventListener('blur', validarFormulario);
validarFormulario();
</script>
