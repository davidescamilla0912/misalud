<?php
class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $tipo_usuario;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function registrar() {
        $query = "INSERT INTO " . $this->table_name . " (nombre, apellido, email, password, tipo_usuario) VALUES (:nombre, :apellido, :email, :password, :tipo_usuario)";
        $stmt = $this->conn->prepare($query);
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->apellido = htmlspecialchars(strip_tags($this->apellido));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $this->tipo_usuario = htmlspecialchars(strip_tags($this->tipo_usuario));
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":apellido", $this->apellido);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":tipo_usuario", $this->tipo_usuario);
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function login() {
        $query = "SELECT id, nombre, apellido, password, tipo_usuario FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $this->email = htmlspecialchars(strip_tags($this->email));
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($this->password, $row['password'])) {
                $this->id = $row['id'];
                $this->nombre = $row['nombre'];
                $this->apellido = $row['apellido'];
                $this->tipo_usuario = $row['tipo_usuario'];
                return true;
            }
        }
        return false;
    }

    public function emailExiste() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $this->email = htmlspecialchars(strip_tags($this->email));
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function obtenerTodos() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY fecha_registro DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function eliminar($id) {
        $query = "SELECT tipo_usuario FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row['tipo_usuario'] === 'doctor') {
                $query = "DELETE h FROM horarios_disponibles h INNER JOIN doctores d ON h.doctor_id = d.id WHERE d.usuario_id = :id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(":id", $id);
                $stmt->execute();
                $query = "DELETE FROM doctores WHERE usuario_id = :id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(":id", $id);
                $stmt->execute();
            }
            if($row['tipo_usuario'] === 'paciente') {
                $query = "DELETE FROM citas WHERE paciente_id = :id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(":id", $id);
                $stmt->execute();
            }
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            return $stmt->execute();
        }
        return false;
    }

    public function contarPorTipo($tipo) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE tipo_usuario = :tipo";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":tipo", $tipo);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?> 