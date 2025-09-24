<?php
// Activar la visualización de errores en pantalla
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Incluir archivo de configuración de base de datos y el controlador de usuarios
require_once "config/database.php";
require_once "controllers/UsuarioController.php";

// Crear instancia de conexión a la base de datos
$database = new Database();
$db = $database->getConnection();

// Crear instancia del controlador de usuarios, pasando la conexión
$controller = new UsuarioController($db);

// Obtener la acción desde la URL (GET), por defecto será 'index'
$action = $_GET['action'] ?? 'index';

// Ejecutar la acción correspondiente según el valor de $action
switch ($action) {
    case 'index':
        $controller->index();
        break;
    case 'crear':
        $controller->crearForm();
        break;
    case 'guardar':
        $controller->guardar($_POST, $_FILES);
        break;
    case 'editar':
        $controller->editar();
        break;
    case 'actualizar':
        $controller->actualizar();
        break;
    case 'eliminar':
        $controller->eliminar($_POST);
        break;
    default:
        $controller->index();
}

