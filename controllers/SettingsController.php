<?php
class SettingsController extends BaseController {
    private $settingsModel;

    public function __construct() {
        parent::__construct();
        $this->requireAdmin();
        $this->settingsModel = new Settings();
    }

    public function index() {
        $settings = $this->settingsModel->getAll();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validateCSRF();
                
                if ($this->settingsModel->updateBatch($_POST['settings'])) {
                    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Configuración actualizada correctamente'];
                } else {
                    throw new Exception('Error al actualizar la configuración');
                }
                
                $this->redirect('admin/settings');
            } catch (Exception $e) {
                $_SESSION['flash'] = ['type' => 'error', 'message' => $e->getMessage()];
            }
        }

        require BASE_PATH . '/views/admin/layout/header.php';
        require BASE_PATH . '/views/admin/settings/index.php';
        require BASE_PATH . '/views/admin/layout/footer.php';
    }
} 