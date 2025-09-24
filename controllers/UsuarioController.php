<?php
// Incluir el modelo de Usuario para acceder a las funciones de base de datos
require_once "models/Usuario.php";
// Incluir la librería FPDF para generar archivos PDF (contratos)
require_once __DIR__ . '/../libs/fpdf/fpdf.php';

class UsuarioController {
    // Propiedad privada para almacenar la conexión a la base de datos
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Método que muestra la vista principal con el listado de usuarios activos
    public function index() {
        // Crear instancia del modelo Usuario con la conexión actual
        $usuario = new Usuario($this->db);
        // Obtener listado de usuarios activos desde el modelo
        $stmt = $usuario->listar();
        // Incluir la vista que renderiza la tabla de usuarios
        include "views/usuarios/listar.php";
    }

    // Método que prepara y muestra el formulario para registrar un nuevo usuario
    public function crearForm() {
        // Traer roles desde la DB y convertir a array para la vista
        $stmt = $this->db->prepare("SELECT * FROM roles ORDER BY nombre_cargo");
        // Ejecutar la consulta
        $stmt->execute();
        $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // incluir la vista (usa rutas relativas seguras)
        include __DIR__ . "/../views/usuarios/crear.php";
    }

    // Guarda un nuevo usuario en la base de datos, procesa su firma y genera su contrato
    public function guardar($post, $files) {
        $usuario = new Usuario($this->db);
        $usuario->nombre = $post['nombre'];
        $usuario->correo_electronico = $post['correo_electronico'];
        $usuario->id_rol = $post['id_rol'];
        $usuario->fecha_ingreso = $post['fecha_ingreso'];

    
        if ($usuario->crear()) {
            $id = $this->db->lastInsertId(); // ID del usuario creado
    
            // Procesar la firma
            $firmaPath = "public/firmas/firma_$id.png";
            if (!is_dir("public/firmas")) mkdir("public/firmas", 0777, true);
            move_uploaded_file($files['firma']['tmp_name'], $firmaPath);
            // Guardar ruta de firma en DB
            $usuario->guardarFirma($id, $firmaPath);
            // Generar contrato PDF
            $contratoPath = "public/contratos/contrato_$id.pdf";
            if (!is_dir("public/contratos")) mkdir("public/contratos", 0777, true);
            $this->generarContrato($usuario, $firmaPath, $contratoPath);
    
            // Guardar ruta en DB
            $usuario->guardarContrato($id, $contratoPath);
    
            header("Location: index.php?msg=creado");
        } else {
            header("Location: index.php?msg=error");
        }
    }

    // Prepara y muestra el formulario para editar un usuario existente
    public function editar() {
        // Obtener el ID del usuario desde la URL (GET), o null si no está presente
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?action=index");
            exit;
        }
    
        $usuario = new Usuario($this->db);
        $stmt = $usuario->obtenerPorId($id);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$data) {
            header("Location: index.php?action=index");
            exit;
        }
        // 🔹 cargar roles aquí y pasarlos a la vista
        $rolesStmt = $this->db->query("SELECT * FROM roles");
        $roles = $rolesStmt->fetchAll(PDO::FETCH_ASSOC);

        include "views/usuarios/editar.php";
    }
    
    // Actualización de un usuario
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Crear instancia del modelo Usuario y asignar los datos recibidos
            $usuario = new Usuario($this->db);
            $usuario->id = $_POST['id'];
            $usuario->nombre = $_POST['nombre'];
            $usuario->correo_electronico = $_POST['correo_electronico'];
            $usuario->id_rol = $_POST['id_rol'];
            $usuario->fecha_ingreso = $_POST['fecha_ingreso'];
            // Ejecutar el método actualizar() del modelo
            $usuario->actualizar();
    
            header("Location: index.php?action=index");
        }
    }

    // Método que procesa la solicitud para eliminar (desactivar) un usuario
    public function eliminar($post) {
        // Validar que se haya recibido un ID
        if (empty($post['id'])) {
            header("Location: index.php?msg=error");
            exit;
        }
        $usuario = new Usuario($this->db);
        $id = (int)$post['id'];
        $result = $usuario->eliminar($id);
    
        if ($result) {
            header("Location: index.php?msg=eliminado");
        } else {
            header("Location: index.php?msg=error");
        }
        exit;
    }

    // Método privado que genera un contrato en PDF con los datos del usuario y su firma
    private function generarContrato($usuario, $firmaPath, $contratoPath) {
        // Crear una nueva instancia de FPDF
        $pdf = new FPDF();
        // Agregar una página al documento y establecer fuente
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',16);
    
        // Título
        $pdf->Cell(0,10,"Contrato de Usuario",0,1,'C');
        $pdf->Ln(10);
    
        // Datos del usuario
        $pdf->SetFont('Arial','',12);
        $pdf->Cell(0,10,"Nombre: " . $usuario->nombre,0,1);
        $pdf->Cell(0,10,"Correo: " . $usuario->correo_electronico,0,1);
        $pdf->Cell(0,10,"Fecha de ingreso: " . $usuario->fecha_ingreso,0,1);
       
        // Determinar el texto del rol según el ID
        $rolTexto = ($usuario->id_rol == 1) ? "Empleado" : "Jefe";
        $pdf->Cell(0,10,"Rol: " . $rolTexto,0,1);
        $pdf->Ln(20);
    
        // Firma
        $pdf->Cell(0,10,"Firma:",0,1);
        $pdf->Image($firmaPath, 10, 110, 50, 30);
    
        // Guardar
        $pdf->Output("F", $contratoPath);
    }    
}
