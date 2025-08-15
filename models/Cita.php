<?php
class Cita {
    private $conn;
    private $table_name = "citas";

    public $id;
    public $paciente_id;
    public $horario_id;
    public $estado;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function agendar() {
        $query = "INSERT INTO " . $this->table_name . "
                (paciente_id, horario_id, estado)
                VALUES
                (:paciente_id, :horario_id, 'pendiente')";

        $stmt = $this->conn->prepare($query);

        // Vincular valores
        $stmt->bindParam(":paciente_id", $this->paciente_id);
        $stmt->bindParam(":horario_id", $this->horario_id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function obtenerCitasPaciente() {
        $query = "SELECT c.*, h.fecha, h.hora, d.especialidad, d.ubicacion,
                        u_d.nombre as nombre_doctor, u_d.apellido as apellido_doctor
                FROM " . $this->table_name . " c
                JOIN horarios_disponibles h ON c.horario_id = h.id
                JOIN doctores d ON h.doctor_id = d.id
                JOIN usuarios u_d ON d.usuario_id = u_d.id
                WHERE c.paciente_id = :paciente_id
                ORDER BY h.fecha, h.hora";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":paciente_id", $this->paciente_id);
        $stmt->execute();

        return $stmt;
    }

    public function obtenerDetallesCita() {
        $query = "SELECT c.*, h.fecha, h.hora, d.especialidad, d.ubicacion,
                        u_d.nombre as nombre_doctor, u_d.apellido as apellido_doctor,
                        u_p.nombre as nombre_paciente, u_p.apellido as apellido_paciente
                FROM " . $this->table_name . " c
                JOIN horarios_disponibles h ON c.horario_id = h.id
                JOIN doctores d ON h.doctor_id = d.id
                JOIN usuarios u_d ON d.usuario_id = u_d.id
                JOIN usuarios u_p ON c.paciente_id = u_p.id
                WHERE c.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerTodas() {
        $query = "SELECT c.*, 
                        CONCAT(p.nombre, ' ', p.apellido) as nombre_paciente,
                        CONCAT(d.nombre, ' ', d.apellido) as nombre_doctor
                 FROM citas c
                 INNER JOIN usuarios p ON c.paciente_id = p.id
                 INNER JOIN doctores doc ON c.doctor_id = doc.id
                 INNER JOIN usuarios d ON doc.usuario_id = d.id
                 ORDER BY c.fecha DESC, c.hora DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function contarTodas() {
        $query = "SELECT COUNT(*) as total FROM citas";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function contarPorEstado($estado) {
        $query = "SELECT COUNT(*) as total FROM citas WHERE estado = :estado";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":estado", $estado);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?> 