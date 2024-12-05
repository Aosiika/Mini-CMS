<?php
class MenuController extends BaseController {
    private $menuModel;
    private $postModel;

    public function __construct() {
        parent::__construct();
        $this->menuModel = new Menu();
        $this->postModel = new Post();
        
        // Solo requerir admin para acciones administrativas
        if (in_array($this->getAction(), ['create', 'edit', 'delete'])) {
            $this->requireAdmin();
        }
    }

    private function getAction() {
        return isset($_GET['action']) ? $_GET['action'] : 'index';
    }

    // Método específico para generar slugs de menú
    private function generateMenuSlug($name) {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    }

    public function show() {
        try {
            $slug = isset($_GET['slug']) ? htmlspecialchars(trim($_GET['slug']), ENT_QUOTES, 'UTF-8') : null;
            
            if (!$slug) {
                throw new Exception('Menú no encontrado');
            }

            $menu = $this->menuModel->getBySlug($slug);
            if (!$menu) {
                throw new Exception('Menú no encontrado');
            }

            $posts = $this->postModel->getPostsByMenuId($menu['id']);
            
            require BASE_PATH . '/views/layout/header.php';
            require BASE_PATH . '/views/menu/show.php';
            require BASE_PATH . '/views/layout/footer.php';
        } catch (Exception $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => $e->getMessage()];
            $this->redirect('');
        }
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validateCSRF();
                
                $data = [
                    'name' => Security::sanitizeInput($_POST['name']),
                    'parent_id' => (int)($_POST['parent_id'] ?? 0),
                    'order_index' => (int)($_POST['order_index'] ?? 0),
                    'is_visible' => isset($_POST['is_visible'])
                ];

                if (empty($data['name'])) {
                    throw new Exception('El nombre es requerido');
                }

                if ($this->menuModel->create($data)) {
                    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Menú creado correctamente'];
                    $this->redirect('admin/menus');
                }
            } catch (Exception $e) {
                $_SESSION['flash'] = ['type' => 'error', 'message' => $e->getMessage()];
            }
        }

        $menus = $this->menuModel->getAll();
        require BASE_PATH . '/views/admin/layout/header.php';
        require BASE_PATH . '/views/admin/menus/form.php';
        require BASE_PATH . '/views/admin/layout/footer.php';
    }

    public function edit($id = null) {
        if (!$id) {
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        }

        if (!$id) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'ID de menú inválido'];
            $this->redirect('admin/menus');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validateCSRF();
                
                $data = [
                    'name' => Security::sanitizeInput($_POST['name']),
                    'parent_id' => (int)($_POST['parent_id'] ?? 0),
                    'order_index' => (int)($_POST['order_index'] ?? 0),
                    'is_visible' => isset($_POST['is_visible'])
                ];

                if (empty($data['name'])) {
                    throw new Exception('El nombre es requerido');
                }

                if ($this->menuModel->update($id, $data)) {
                    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Menú actualizado correctamente'];
                    $this->redirect('admin/menus');
                }
            } catch (Exception $e) {
                $_SESSION['flash'] = ['type' => 'error', 'message' => $e->getMessage()];
            }
        }

        $menu = $this->menuModel->getById($id);
        if (!$menu) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Menú no encontrado'];
            $this->redirect('admin/menus');
        }

        $menus = $this->menuModel->getAll();
        require BASE_PATH . '/views/admin/layout/header.php';
        require BASE_PATH . '/views/admin/menus/form.php';
        require BASE_PATH . '/views/admin/layout/footer.php';
    }

    public function delete() {
        try {
            $this->validateCSRF();
            
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) {
                throw new Exception('ID de menú inválido');
            }

            if ($this->menuModel->delete($id)) {
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Menú eliminado correctamente'];
            } else {
                throw new Exception('Error al eliminar el menú');
            }
        } catch (Exception $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => $e->getMessage()];
        }

        $this->redirect('admin/menus');
    }
} 