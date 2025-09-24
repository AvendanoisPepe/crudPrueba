<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "config/database.php";
require_once "controllers/UsuarioController.php";

$database = new Database();
$db = $database->getConnection();

$controller = new UsuarioController($db);

$action = $_GET['action'] ?? 'index';

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
        $controller->eliminar($_POST); // lo dejamos listo para después
        break;
    default:
        $controller->index();
}

