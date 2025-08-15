<?php
class Horario {
    private $conn;
    private $table_name = "horarios_disponibles";

    public $id;
    public $doctor_id;
    public $fecha;
    public $hora;
    public $cupos_disponibles;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function crear() {
        $query = "INSERT INTO " . $this->table_name . "
                (doctor_id, fecha, hora, cupos_disponibles)
                VALUES
                (:doctor_id, :fecha, :hora, :cupos_disponibles)";

        $stmt = $this->conn->prepare($query);

        // Sanitizar datos
        $this->fecha = htmlspecialchars(strip_tags($this->fecha));
        $this->hora = htmlspecialchars(strip_tags($this->hora));

        // Vincular valores
        $stmt->bindParam(":doctor_id", $this->doctor_id);
        $stmt->bindParam(":fecha", $this->fecha);
        $stmt->bindParam(":hora", $this->hora);
        $stmt->bindParam(":cupos_disponibles", $this->cupos_disponibles);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function obtenerHorariosDisponibles() {
        $query = "SELECT h.*, d.especialidad, d.ubicacion, u.nombre, u.apellido
                FROM " . $this->table_name . " h
                JOIN doctores d ON h.doctor_id = d.id
                JOIN usuarios u ON d.usuario_id = u.id
                WHERE h.cupos_disponibles > 0
                AND h.fecha >= CURDATE()
                ORDER BY h.fecha, h.hora";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function actualizarCupos() {
        $query = "UPDATE " . $this->table_name . "
                SET cupos_disponibles = cupos_disponibles - 1
                WHERE id = :id AND cupos_disponibles > 0";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute() && $stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }
}
?> 