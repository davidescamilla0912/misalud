<?php  //controlador de citas
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Cita.php';

class CitaController extends BaseController {
    private $cita;

    public function __construct() {
        parent::__construct();
        $this->cita = new Cita($this->db);
    }

    public function index() {
        $this->requireRole('administrador');
        $citas = $this->cita->obtenerTodas();
        
        return $this->render('citas/index', [
            'title' => 'Gestión de Citas',
            'citas' => $citas,
            'messages' => $this->getMessages()
        ]);
    }

    public function eliminar() {
        $this->requireRole('administrador');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            if ($this->cita->eliminar($_POST['id'])) {
                $this->setSuccess('Cita eliminada correctamente');
            } else {
                $this->setError('Error al eliminar la cita');
            }
        }
        
        $this->redirect('/admin/citas.php');
    }

    public function agendar() {
        $this->requireRole('paciente');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['horario_id'])) {
            $this->cita->paciente_id = $this->user['id'];
            $this->cita->horario_id = $_POST['horario_id'];
            
            if ($this->cita->agendar()) {
                $this->setSuccess('Cita agendada correctamente');
            } else {
                $this->setError('Error al agendar la cita');
            }
        }
        
        $this->redirect('/paciente/dashboard.php');
    }

    public function cancelar() {
        $this->requireRole('paciente');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cita_id'])) {
            if ($this->cita->cancelar($_POST['cita_id'], $this->user['id'])) {
                $this->setSuccess('Cita cancelada correctamente');
            } else {
                $this->setError('Error al cancelar la cita');
            }
        }
        
        $this->redirect('/paciente/dashboard.php');
    }
} 