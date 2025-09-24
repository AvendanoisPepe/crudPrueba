<?php
class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $nombre;
    public $correo_electronico;
    public $id_rol;
    public $fecha_ingreso;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear usuario
    public function crear() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (nombre, correo_electronico, id_rol, fecha_ingreso) 
                  VALUES (:nombre, :correo_electronico, :rol, :fecha)";
        $stmt = $this->conn->prepare($query);
    
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":correo_electronico", $this->correo_electronico);
        $stmt->bindParam(":rol", $this->id_rol);
        $stmt->bindParam(":fecha", $this->fecha_ingreso);
    
        return $stmt->execute();
    }
    

    // Listar usuarios
    public function listar() {
        $query = "SELECT u.*, r.nombre_cargo 
                  FROM " . $this->table_name . " u 
                  JOIN roles r ON u.id_rol = r.id
                  WHERE u.estado = 'activo'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
   
    
    public function obtenerPorId($id) {
        $query = "SELECT * FROM usuarios WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt;
    }
    
    public function actualizar() {
        $query = "UPDATE usuarios 
                  SET nombre=:nombre, correo_electronico=:correo, id_rol=:id_rol, fecha_ingreso=:fecha 
                  WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":correo", $this->correo_electronico);
        $stmt->bindParam(":id_rol", $this->id_rol);
        $stmt->bindParam(":fecha", $this->fecha_ingreso);
        $stmt->bindParam(":id", $this->id);
        return $stmt->execute();
    }
    public function eliminar($id) {
        $query = "UPDATE " . $this->table_name . " 
            SET estado = 'inactivo', fecha_eliminacion = :fecha
            WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $fechaHoy = date('Y-m-d');
        $stmt->bindParam(':fecha', $fechaHoy);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function guardarContrato($id, $contratoPath) {
        $query = "UPDATE " . $this->table_name . " SET contrato = :contrato WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':contrato', $contratoPath);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
}
