<?php
// Incluir configuración de base de datos y modelo de Usuario
require_once 'config/database.php';
require_once 'models/Usuario.php';

$database = new Database();
$db = $database->getConnection();
$usuario = new Usuario($db);

// Obtener el correo desde la URL (GET), o cadena vacía si no se proporciona
$correo = $_GET['correo'] ?? '';

header('Content-Type: application/json; charset=utf-8');

if (!$correo) {
    echo json_encode(['existe' => false]);
    exit;
}

try {
    // Verificar si el correo existe en la base de datos
    $existe = $usuario->existeCorreo($correo);
    echo json_encode(['existe' => $existe]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error interno']);
}
