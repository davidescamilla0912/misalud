<?php
class Doctor {
    private $conn;
    private $table_name = "doctores";

    public $id;
    public $usuario_id;
    public $especialidad;
    public $ubicacion;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function registrar() {
        $query = "INSERT INTO " . $this->table_name . "
                (usuario_id, especialidad, ubicacion)
                VALUES
                (:usuario_id, :especialidad, :ubicacion)";

        $stmt = $this->conn->prepare($query);

        // Sanitizar datos
        $this->especialidad = htmlspecialchars(strip_tags($this->especialidad));
        $this->ubicacion = htmlspecialchars(strip_tags($this->ubicacion));

        // Vincular valores
        $stmt->bindParam(":usuario_id", $this->usuario_id);
        $stmt->bindParam(":especialidad", $this->especialidad);
        $stmt->bindParam(":ubicacion", $this->ubicacion);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function obtenerHorarios() {
        $query = "SELECT h.*, d.especialidad, d.ubicacion, u.nombre, u.apellido
                FROM horarios_disponibles h
                JOIN doctores d ON h.doctor_id = d.id
                JOIN usuarios u ON d.usuario_id = u.id
                WHERE h.doctor_id = :doctor_id
                ORDER BY h.fecha, h.hora";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":doctor_id", $this->id);
        $stmt->execute();

        return $stmt;
    }

    public function obtenerCitasAgendadas() {
        $query = "SELECT c.*, h.fecha, h.hora, u.nombre, u.apellido
                FROM citas c
                JOIN horarios_disponibles h ON c.horario_id = h.id
                JOIN usuarios u ON c.paciente_id = u.id
                WHERE h.doctor_id = :doctor_id
                ORDER BY h.fecha, h.hora";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":doctor_id", $this->id);
        $stmt->execute();

        return $stmt;
    }
}
?> 