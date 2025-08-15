<?php //vista de citas
$content = ob_get_clean();
?>
//vista de citas
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestión de Citas</h2>
    <a href="/admin/dashboard.php" class="btn btn-secondary">Volver al Dashboard</a>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Paciente</th>
                <th>Doctor</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $citas->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['nombre_paciente'] . ' ' . $row['apellido_paciente']); ?></td>
                <td><?php echo htmlspecialchars($row['nombre_doctor'] . ' ' . $row['apellido_doctor']); ?></td>
                <td><?php echo date('d/m/Y', strtotime($row['fecha'])); ?></td>
                <td><?php echo date('H:i', strtotime($row['hora'])); ?></td>
                <td>
                    <span class="badge bg-<?php echo $row['estado'] === 'confirmada' ? 'success' : ($row['estado'] === 'pendiente' ? 'warning' : 'danger'); ?>">
                        <?php echo ucfirst($row['estado']); ?>
                    </span>
                </td>
                <td>
                    <form method="POST" action="/admin/citas.php?action=eliminar" style="display: inline;" onsubmit="return confirm('¿Está seguro de eliminar esta cita?');">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php
require_once __DIR__ . '/../layouts/main.php';
?> 