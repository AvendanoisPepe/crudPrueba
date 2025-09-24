<?php
// $roles viene desde el controlador
$hoy = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <title>Registrar</title>
</head>
<body>
  <section class="w-full min-h-screen flex flex-col items-center bg-gray-50 p-6">
    <div class="w-full max-w-xl bg-white shadow-lg rounded-lg p-6">
      <h2 class="text-4xl font-bold text-blue-600 mb-6 text-center">Registrar Usuario</h2>

      <form method="POST" action="index.php?action=guardar" id="formRegistro" autocomplete="off" enctype="multipart/form-data" class="space-y-5">
        <!-- Nombre -->
        <div>
          <label for="Nombre" class="block text-base font-medium text-gray-700 mb-1">Nombre:</label>
          <input type="text" name="nombre" id="nombre" placeholder="Nombre" required
            class="w-full px-4 py-2 border border-blue-300 bg-blue-50 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <!-- Correo -->
        <div class="w-full flex flex-col">
          <label for="correo_electronico" class="block text-base font-medium text-gray-700 mb-1">Correo Electrónico:</label>
          <input type="email" name="correo_electronico" id="correo_electronico" placeholder="Correo Electrónico" required
            class="w-full px-4 py-2 border border-blue-300 bg-blue-50 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
          <small id="emailError" class="w-full py-2 bg-red-100 text-red-500 hidden border border-red-300 text-base rounded-md text-center mt-2"></small>
        </div>

        <!-- Rol -->
        <div>
          <label for="id_rol" class="block text-base font-medium text-gray-700 mb-1">Rol:</label>
          <select name="id_rol" id="id_rol" required
            class="w-full px-4 py-2 border border-blue-300 bg-blue-50 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option class="border-blue-300 bg-blue-50" value="">Seleccione un rol</option>
            <?php foreach ($roles as $r): ?>
              <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['nombre_cargo']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Fecha Ingreso -->
        <div class="w-full flex flex-col">
          <label for="fecha_ingreso" class="block text-base font-medium text-gray-700 mb-1">Fecha Ingreso:</label>
          <input type="date" name="fecha_ingreso" id="fecha_ingreso" max="<?= $hoy ?>" required
            class="w-full px-4 py-2 border border-blue-300 bg-blue-50 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
          <small id="fechaError" class="w-full py-2 bg-red-100 text-red-500 hidden border border-red-300 text-base rounded-md text-center mt-2">La fecha no puede ser futura.</small>
        </div>

        <!-- Firma -->
        <div>
          <label for="firma" class="block text-base font-medium text-gray-700 mb-1">Firma de contrato:</label>
          <input type="file" name="firma" accept="image/*" required
            class="w-full px-4 py-2 border border-blue-300 bg-blue-50 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <!-- Botón -->
        <div class="text-center">
          <button type="submit" id="btnRegistrar" disabled
            class="text-xl w-1/2 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded-md shadow transition disabled:opacity-50 disabled:cursor-not-allowed">
            Registrar
          </button>
        </div>
      </form>
    </div>
  </section>
</body>
</html>
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
