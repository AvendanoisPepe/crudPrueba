<?php
// views/usuarios/listar.php
// Asume que $stmt viene desde el controlador (UsuarioController::index)
// y es un PDOStatement con los usuarios y nombre_cargo (JOIN)

$year = date('Y');

// Traer festivos (si falla la petición, $festivos quedará vacío)
$festivos = [];
$apiUrl = "https://api-colombia.com/api/v1/holiday/year/{$year}";
$response = @file_get_contents($apiUrl);
if ($response !== false) {
    $data = json_decode($response, true);
    if (is_array($data)) {
        foreach ($data as $h) {
            if (isset($h['date'])) $festivos[] = $h['date'];
        }
    }
}

function calcularDiasHabiles($fechaInicio, $festivos) {
  $inicio = new DateTime($fechaInicio);
  $hoy = new DateTime();
  $dias = 0;

  if ($inicio > $hoy) return 0;

  // empezar a contar desde el día siguiente a la fecha de ingreso
  $inicio->modify('+1 day');

  while ($inicio <= $hoy) {
      $n = (int)$inicio->format('N'); // 1 = lunes ... 7 = domingo
      $s = $inicio->format('Y-m-d');
      if ($n < 6 && !in_array($s, $festivos)) {
          $dias++;
      }
      $inicio->modify('+1 day');
  }
  return $dias;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <title>Listado de Usuarios</title>
</head>
<body>
<section class="w-full min-h-screen flex flex-col items-center bg-gray-50 p-6">
  <div class="w-full max-w-6xl text-center mb-6 flex items-center justify-center flex-wrap gap-8">
    <h1 class="text-4xl md:text-5xl font-bold text-blue-600 mb-2">Listado de Usuarios</h1>
    <a href="index.php?action=crear" class="text-2xl inline-block bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded shadow transition">
      ➕ Crear Usuario
    </a>
  </div>

  <div class="w-full max-w-7xl overflow-x-auto shadow-lg rounded-lg">
    <table class="min-w-full divide-y divide-gray-200 bg-white">
      <thead class="bg-blue-100 text-blue-800">
        <tr>
          <th class="px-4 py-3 text-left text-lg font-bold">ID</th>
          <th class="px-4 py-3 text-left text-lg font-bold">Nombre</th>
          <th class="px-4 py-3 text-left text-lg font-bold">Correo Electrónico</th>
          <th class="px-4 py-3 text-left text-lg font-bold">Cargo</th>
          <th class="px-4 py-3 text-left text-lg font-bold">Fecha Ingreso</th>
          <th class="px-4 py-3 text-left text-lg font-bold">Días Trabajados</th>
          <th class="px-4 py-3 text-left text-lg font-bold">Contrato</th>
          <th class="px-4 py-3 text-left text-lg font-bold">Acciones</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
          $id = htmlspecialchars($row['id']);
          $nombre = htmlspecialchars($row['nombre']);
          $correo = htmlspecialchars($row['correo_electronico']);
          $cargo = htmlspecialchars($row['nombre_cargo']);
          $fecha = htmlspecialchars($row['fecha_ingreso']);
          $diasTrabajados = calcularDiasHabiles($row['fecha_ingreso'], $festivos);
        ?>
        <tr class="hover:bg-gray-50">
          <td class="px-4 py-3 text-base text-left"><?= $id ?></td>
          <td class="px-4 py-3 text-base text-left"><?= $nombre ?></td>
          <td class="px-4 py-3 text-base text-left"><?= $correo ?></td>
          <td class="px-4 py-3 text-base text-left"><?= $cargo ?></td>
          <td class="px-4 py-3 text-base text-left"><?= $fecha ?></td>
          <td class="px-4 py-3 text-base text-left"><?= $diasTrabajados ?></td>
          <td class="px-4 py-3 text-base text-left">
            <?php if (!empty($row['contrato'])): ?>
              <a href="<?= $row['contrato'] ?>" target="_blank" class="text-white font-medium bg-slate-400 hover:bg-slate-500 hover:underline rounded-sm p-2">Ver Contrato</a>
            <?php else: ?>
              <span class="text-gray-500 italic">Sin contrato</span>
            <?php endif; ?>
          </td>
          <td class="px-4 py-3 text-sm space-x-2 flex lg:flex-col xl:flex-row items-center lg:gap-2 xl:gap-0">
            <a href="index.php?action=editar&id=<?= urlencode($row['id']) ?>" class="text-white bg-blue-600 hover:bg-blue-700  hover:underline font-medium rounded-sm p-2">Editar</a>
            <form method="POST" action="index.php?action=eliminar" class="formEliminar inline-block">
              <input type="hidden" name="id" value="<?= $row['id'] ?>">
              <button type="submit" class="text-white bg-red-600 hover:bg-red-700 hover:underline font-medium rounded-sm p-2">Eliminar</button>
            </form>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</section>
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const forms = document.querySelectorAll(".formEliminar");

    forms.forEach(form => {
      form.addEventListener("submit", function(e) {
        e.preventDefault(); // prevenimos envío inmediato

        Swal.fire({
          title: '¿Estás seguro?',
          text: "¡No podrás revertir esta acción!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Sí, eliminar',
          cancelButtonText: 'Cancelar'
        }).then((result) => {
          if (result.isConfirmed) {
            form.submit(); // 👈 ahora sí enviamos el form
          }
        });
      });
    });
  });
</script>
</body>
</html>
