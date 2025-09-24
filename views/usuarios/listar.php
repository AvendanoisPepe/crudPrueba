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
  <title>Listado de Usuarios</title>
</head>
<body>
  <h2>Listado de Usuarios</h2>
  <p><a href="index.php?action=crear">➕ Crear Usuario</a></p>

  <table border="1" cellpadding="6" cellspacing="0">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Correo</th>
        <th>Cargo</th>
        <th>Fecha Ingreso</th>
        <th>Días Trabajados</th>
        <th>Contrato</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): 
        // seguridad al mostrar
        $id = htmlspecialchars($row['id']);
        $nombre = htmlspecialchars($row['nombre']);
        $correo = htmlspecialchars($row['correo_electronico']);
        $cargo = htmlspecialchars($row['nombre_cargo']);
        $fecha = htmlspecialchars($row['fecha_ingreso']);
        $diasTrabajados = calcularDiasHabiles($row['fecha_ingreso'], $festivos);
    ?>
      <tr>
        <td><?= $id ?></td>
        <td><?= $nombre ?></td>
        <td><?= $correo ?></td>
        <td><?= $cargo ?></td>
        <td><?= $fecha ?></td>
        <td><?= $diasTrabajados ?></td>
        <td>
        <?php if (!empty($row['contrato'])): ?>
              <a href="<?= $row['contrato'] ?>" target="_blank">Ver Contrato</a>
          <?php else: ?>
              <span>Sin contrato</span>
          <?php endif; ?>
        </td>
        <td>
          <a href="index.php?action=editar&id=<?= urlencode($row['id']) ?>">Editar</a>
          |
          <form method="POST" action="index.php?action=eliminar" style="display:inline;" 
                onsubmit="return confirm('¿Seguro que deseas eliminar este usuario?')">
              <input type="hidden" name="id" value="<?= $row['id'] ?>">
              <button type="submit">Eliminar</button>
          </form>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</body>
</html>
