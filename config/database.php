<?php
class Database {
    // Parámetros de conexión (host, nombre de base de datos, usuario, contraseña)
    private $host = "localhost";
    private $db_name = "crud_prueba";
    private $username = "root";
    private $password = "";
    public $conn;
    
    // Método que establece y retorna la conexión a la base de datos
    public function getConnection() {
        $this->conn = null;
        try {
            // nueva instancia PDO con los parámetros definidos
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }
        // Retorna la conexión (puede ser válida o null si falló)
        return $this->conn;
    }
}
