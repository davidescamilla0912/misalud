-- Vista para Citas del Día
CREATE VIEW vw_citas_dia AS
SELECT 
    c.id as cita_id,
    c.paciente_id,
    u_paciente.nombre as paciente_nombre,
    u_paciente.apellido as paciente_apellido,
    u_doctor.nombre as doctor_nombre,
    u_doctor.apellido as doctor_apellido,
    h.fecha,
    h.hora,
    c.estado,
    d.especialidad
FROM citas c
JOIN horarios_disponibles h ON c.horario_id = h.id
JOIN doctores d ON h.doctor_id = d.id
JOIN usuarios u_paciente ON c.paciente_id = u_paciente.id
JOIN usuarios u_doctor ON d.usuario_id = u_doctor.id
WHERE h.fecha >= CURDATE() 
AND h.fecha <= DATE_ADD(CURDATE(), INTERVAL 10 DAY)
ORDER BY h.fecha, h.hora;

-- Procedimiento para Cancelar Cita
DELIMITER //
CREATE PROCEDURE sp_cancelar_cita(
    IN p_cita_id INT,
    OUT p_mensaje VARCHAR(100)
)
BEGIN
    DECLARE v_estado VARCHAR(20);
    DECLARE v_fecha DATE;
    DECLARE v_hora TIME;
    
    -- Obtener estado y fecha de la cita
    SELECT c.estado, h.fecha, h.hora 
    INTO v_estado, v_fecha, v_hora
    FROM citas c
    JOIN horarios_disponibles h ON c.horario_id = h.id
    WHERE c.id = p_cita_id;
    
    -- Validar si la cita existe
    IF v_estado IS NULL THEN
        SET p_mensaje = 'La cita no existe';
    -- Validar si la cita ya está cancelada
    ELSEIF v_estado = 'cancelada' THEN
        SET p_mensaje = 'La cita ya está cancelada';
    -- Validar si la cita ya pasó
    ELSEIF CONCAT(v_fecha, ' ', v_hora) < NOW() THEN
        SET p_mensaje = 'No se puede cancelar una cita pasada';
    ELSE
        -- Cancelar la cita
        UPDATE citas 
        SET estado = 'cancelada' 
        WHERE id = p_cita_id;
        
        -- Liberar el cupo
        UPDATE horarios_disponibles h
        JOIN citas c ON h.id = c.horario_id
        SET h.cupos_disponibles = h.cupos_disponibles + 1
        WHERE c.id = p_cita_id;
        
        SET p_mensaje = 'Cita cancelada exitosamente';
    END IF;
END //
DELIMITER ;

-- Eliminar trigger existente (si existe)
DROP TRIGGER IF EXISTS tr_validar_horario;

-- Trigger para validar horarios
DELIMITER //
CREATE TRIGGER tr_validar_horario
BEFORE INSERT ON horarios_disponibles
FOR EACH ROW
BEGIN
    IF NEW.fecha < CURDATE() THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'No se pueden crear horarios en fechas pasadas';
    END IF;

    IF NEW.cupos_disponibles <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Los cupos disponibles deben ser mayores a 0';
    END IF;
END //
DELIMITER ; 