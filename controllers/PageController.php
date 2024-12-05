<?php
class PageController extends BaseController {
    private $pageModel;
    private $postModel;

    public function __construct() {
        parent::__construct();
        $this->pageModel = new Page();
        $this->postModel = new Post();
    }

    public function show() {
        try {
            $slug = isset($_GET['slug']) ? htmlspecialchars(trim($_GET['slug']), ENT_QUOTES, 'UTF-8') : null;
            error_log("Slug received: " . $slug);
            
            if (!$slug) {
                throw new Exception('Página no encontrada');
            }

            // Primero buscar en posts
            $page = $this->postModel->getBySlug($slug);
            
            // Si no se encuentra en posts, buscar en pages
            if (!$page) {
                $page = $this->pageModel->getBySlug($slug);
            }

            error_log("Page found: " . print_r($page, true));
            
            if (!$page) {
                throw new Exception('Página no encontrada');
            }

            require BASE_PATH . '/views/layout/header.php';
            require BASE_PATH . '/views/pages/show.php';
            require BASE_PATH . '/views/layout/footer.php';
        } catch (Exception $e) {
            error_log("Error in PageController::show(): " . $e->getMessage());
            $_SESSION['flash'] = ['type' => 'error', 'message' => $e->getMessage()];
            $this->redirect('');
        }
    }

    public function delete() {
        try {
            $this->validateCSRF();
            
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) {
                throw new Exception('ID de página inválido');
            }

            $page = $this->pageModel->getById($id);
            if (!$page) {
                throw new Exception('Página no encontrada');
            }

            if ($this->pageModel->delete($id)) {
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Página eliminada correctamente'];
            } else {
                throw new Exception('Error al eliminar la página');
            }
        } catch (Exception $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => $e->getMessage()];
        }

        $this->redirect('?controller=admin&action=pages');
    }
} 