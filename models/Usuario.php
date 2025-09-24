<?php
// Clase que representa el modelo de Usuario y gestiona operaciones con la base de datos
class Usuario {
    // Conexión a la base de datos (PDO) y nombre de la tabla
    private $conn;
    private $table_name = "usuarios";
    // Propiedades públicas que representan los campos del usuario
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
        // Consulta SQL con parámetros nombrados para insertar datos
        $query = "INSERT INTO " . $this->table_name . " 
                  (nombre, correo_electronico, id_rol, fecha_ingreso) 
                  VALUES (:nombre, :correo_electronico, :rol, :fecha)";
        // Preparar la consulta para evitar inyecciones SQL
        $stmt = $this->conn->prepare($query);
        // Asignar valores a los parámetros usando las propiedades del objeto
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":correo_electronico", $this->correo_electronico);
        $stmt->bindParam(":rol", $this->id_rol);
        $stmt->bindParam(":fecha", $this->fecha_ingreso);
    
        return $stmt->execute();
    }
    
    // Listar usuarios
    public function listar() {
        // Consulta SQL que une la tabla de usuarios con la de roles
        // Se seleccionan todos los campos del usuario (u.*) y el nombre del cargo desde la tabla roles
        $query = "SELECT u.*, r.nombre_cargo 
                  FROM " . $this->table_name . " u 
                  JOIN roles r ON u.id_rol = r.id
                  WHERE u.estado = 'activo'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
   
    // Datos de un usuario específico según su ID
    public function obtenerPorId($id) {
        $query = "SELECT * FROM usuarios WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt;
    }
    
    // Actualiza los datos de un usuario existente en la base de datos
    public function actualizar() {
        $query = "UPDATE usuarios 
                  SET nombre=:nombre, correo_electronico=:correo, id_rol=:id_rol, fecha_ingreso=:fecha 
                  WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        // Asignar los valores actuales del objeto a los parámetros de la consulta
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":correo", $this->correo_electronico);
        $stmt->bindParam(":id_rol", $this->id_rol);
        $stmt->bindParam(":fecha", $this->fecha_ingreso);
        $stmt->bindParam(":id", $this->id);
        return $stmt->execute();
    }

    // Marca un usuario como inactivo en lugar de eliminarlo físicamente
    public function eliminar($id) {
        // Consulta SQL para actualizar el estado del usuario y registrar la fecha de eliminación
        $query = "UPDATE " . $this->table_name . " 
            SET estado = 'inactivo', fecha_eliminacion = :fecha
            WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $fechaHoy = date('Y-m-d');
        $stmt->bindParam(':fecha', $fechaHoy);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Método que actualiza la ruta del contrato PDF generado para un usuario específico
    public function guardarContrato($id, $contratoPath) {
        $query = "UPDATE " . $this->table_name . " SET contrato = :contrato WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':contrato', $contratoPath);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Método que guarda la ruta del archivo de firma para un usuario específico
    public function guardarFirma($id, $firmaPath) {
        $query = "UPDATE " . $this->table_name . " SET firma = :firma WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':firma', $firmaPath);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Método que verifica si ya existe un correo electrónico registrado en la base de datos
    public function existeCorreo($correo) {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE correo_electronico = :correo";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
}
