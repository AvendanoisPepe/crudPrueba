<?php
$hoy = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <title>Modificar</title>
</head>
<body>
  <section class="w-full min-h-screen flex flex-col items-center bg-gray-50 p-6">
    <div class="w-full max-w-xl bg-white shadow-lg rounded-lg p-6">
      <h2 class="text-4xl font-bold text-blue-600 mb-6 text-center">Editar Usuario</h2>

      <form method="POST" action="index.php?action=actualizar" id="formEditar" autocomplete="off" class="space-y-5">
        <!-- ID oculto -->
        <input type="hidden" name="id" id="id" value="<?= htmlspecialchars($data['id']) ?>">

        <!-- Nombre -->
        <div>
          <label for="Nombre" class="block text-base font-medium text-gray-700 mb-1">Nombre:</label>
          <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($data['nombre']) ?>" required
            class="w-full px-4 py-2 border border-blue-300 bg-blue-50 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <!-- Correo -->
        <div>
          <label for="correo_electronico" class="block text-base font-medium text-gray-700 mb-1">Correo Electrónico:</label>
          <input type="email" name="correo_electronico" id="correo_electronico" value="<?= htmlspecialchars($data['correo_electronico']) ?>" required
            class="w-full px-4 py-2 border border-blue-300 bg-blue-50 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
          <small id="emailError" class="text-red-500 hidden"></small>
        </div>

        <!-- Rol -->
        <div>
          <label for="id_rol" class="block text-base font-medium text-gray-700 mb-1">Rol:</label>
          <select name="id_rol" id="id_rol" required
            class="w-full px-4 py-2 border border-blue-300 bg-blue-50 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Seleccione un rol</option>
            <?php foreach ($roles as $r): ?>
              <option value="<?= $r['id'] ?>" <?= $r['id'] == $data['id_rol'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($r['nombre_cargo']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Fecha Ingreso -->
        <div>
          <label for="fecha_ingreso" class="block text-base font-medium text-gray-700 mb-1">Fecha Ingreso:</label>
          <input type="date" name="fecha_ingreso" id="fecha_ingreso" max="<?= $hoy ?>" value="<?= htmlspecialchars($data['fecha_ingreso']) ?>" required
            class="w-full px-4 py-2 border border-blue-300 bg-blue-50 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
          <small id="fechaError" class="text-red-500 hidden">La fecha no puede ser futura.</small>
        </div>

        <!-- Botón -->
        <div class="text-center">
          <button type="submit" id="btnActualizar"
            class="text-xl w-1/2 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded-md shadow transition">
            Actualizar
          </button>
        </div>
      </form>
    </div>
  </section>
</body>
</html>

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
