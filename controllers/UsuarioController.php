<?php
require_once "models/Usuario.php";
require_once __DIR__ . '/../libs/fpdf/fpdf.php';

class UsuarioController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Mostrar lista
    public function index() {
        $usuario = new Usuario($this->db);
        $stmt = $usuario->listar();
        include "views/usuarios/listar.php";
    }

    // controllers/UsuarioController.php
    public function crearForm() {
        // Traer roles desde la DB y convertir a array para la vista
        $stmt = $this->db->prepare("SELECT * FROM roles ORDER BY nombre_cargo");
        $stmt->execute();
        $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // incluir la vista (usa rutas relativas seguras)
        include __DIR__ . "/../views/usuarios/crear.php";
    }

    // Guardar usuario
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
    
    public function editar() {
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
    
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($this->db);
            $usuario->id = $_POST['id'];
            $usuario->nombre = $_POST['nombre'];
            $usuario->correo_electronico = $_POST['correo_electronico'];
            $usuario->id_rol = $_POST['id_rol'];
            $usuario->fecha_ingreso = $_POST['fecha_ingreso'];
            $usuario->actualizar();
    
            header("Location: index.php?action=index");
        }
    }

    public function eliminar($post) {
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

    private function generarContrato($usuario, $firmaPath, $contratoPath) {
        $pdf = new FPDF();
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
       
        $rolTexto = ($usuario->id_rol == 1) ? "Empleado" : "Jefe";
        $pdf->Cell(0,10,"Rol: " . $rolTexto,0,1);
       
        $pdf->Ln(20);
    
        // Firma
        $pdf->Cell(0,10,"Firma:",0,1);
        $pdf->Image($firmaPath, 10, 80, 50, 30);
    
        // Guardar
        $pdf->Output("F", $contratoPath);
    }    
}
