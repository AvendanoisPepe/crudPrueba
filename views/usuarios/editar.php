<?php
// views/usuarios/editar.php
$hoy = date('Y-m-d');
?>
<h2>Editar Usuario</h2>

<form method="POST" action="index.php?action=actualizar" id="formEditar" autocomplete="off">
  <input type="hidden" name="id" id="id" value="<?= htmlspecialchars($data['id']) ?>">

  <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($data['nombre']) ?>" required><br>

  <input type="email" name="correo_electronico" id="correo_electronico" value="<?= htmlspecialchars($data['correo_electronico']) ?>" required>
  <small id="emailError" style="color:red; display:none;"></small><br>

  <select name="id_rol" id="id_rol" required>
    <option value="">Seleccione un rol</option>
    <?php foreach ($roles as $r): ?>
      <option value="<?= $r['id'] ?>" <?= $r['id'] == $data['id_rol'] ? 'selected' : '' ?>>
        <?= htmlspecialchars($r['nombre_cargo']) ?>
      </option>
    <?php endforeach; ?>
  </select><br>

  <input type="date" name="fecha_ingreso" id="fecha_ingreso" max="<?= $hoy ?>" value="<?= htmlspecialchars($data['fecha_ingreso']) ?>" required><br>
  <small id="fechaError" style="color:red; display:none;">La fecha no puede ser futura.</small><br>

  <button type="submit" id="btnActualizar">Actualizar</button>
</form>

<script>
    const idUsuario = document.getElementById('id').value;
    const nombre = document.getElementById('nombre');
    const correo = document.getElementById('correo_electronico');
    const rol = document.getElementById('id_rol');
    const fecha = document.getElementById('fecha_ingreso');
    const btn = document.getElementById('btnActualizar');
    const emailError = document.getElementById('emailError');
    const fechaError = document.getElementById('fechaError');
    const hoy = new Date().toISOString().split('T')[0];


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
        if (!(await validarCorreoEditar())) valido = false;
        btn.disabled = !valido;
    }

    [nombre, correo, rol, fecha].forEach(el => el.addEventListener('input', validarFormulario));
    correo.addEventListener('blur', validarFormulario);
    validarFormulario();
</script>
