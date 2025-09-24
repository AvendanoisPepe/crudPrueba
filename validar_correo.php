<?php
// validar_correo.php (colocar en la raíz del proyecto)
require_once 'config/database.php';
require_once 'models/Usuario.php';

$database = new Database();
$db = $database->getConnection();
$usuario = new Usuario($db);

$correo = $_GET['correo'] ?? '';

header('Content-Type: application/json; charset=utf-8');

if (!$correo) {
    echo json_encode(['existe' => false]);
    exit;
}

try {
    $existe = $usuario->existeCorreo($correo);
    echo json_encode(['existe' => $existe]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error interno']);
}
