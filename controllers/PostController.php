<?php
class PostController extends BaseController {
    private $postModel;
    private $menuModel;

    public function __construct() {
        parent::__construct();
        $this->postModel = new Post();
        $this->menuModel = new Menu();
        
        // Solo requerir admin para ciertas acciones
        if (in_array($this->getAction(), ['create', 'edit', 'delete'])) {
            $this->requireAdmin();
        }
    }

    private function getAction() {
        return isset($_GET['action']) ? $_GET['action'] : 'index';
    }

    public function index() {
        $posts = $this->postModel->getAll();
        require BASE_PATH . '/views/admin/layout/header.php';
        require BASE_PATH . '/views/admin/posts/index.php';
        require BASE_PATH . '/views/admin/layout/footer.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validateCSRF();
                
                $data = [
                    'menu_id' => (int)($_POST['menu_id'] ?? 0),
                    'title' => Security::sanitizeInput($_POST['title']),
                    'content' => $_POST['content'], // El contenido del editor no necesita sanitización aquí
                    'excerpt' => Security::sanitizeInput($_POST['excerpt'] ?? ''),
                    'status' => $_POST['status'] ?? 'draft'
                ];

                // Manejo de imagen destacada
                if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
                    $data['featured_image'] = $this->handleImageUpload($_FILES['featured_image']);
                }

                if (empty($data['title'])) {
                    throw new Exception('El título es requerido');
                }

                if ($this->postModel->create($data)) {
                    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Post creado correctamente'];
                    $this->redirect('?controller=post&action=index');
                }
            } catch (Exception $e) {
                $_SESSION['flash'] = ['type' => 'error', 'message' => $e->getMessage()];
            }
        }

        $menus = $this->menuModel->getAll();
        require BASE_PATH . '/views/admin/layout/header.php';
        require BASE_PATH . '/views/admin/posts/form.php';
        require BASE_PATH . '/views/admin/layout/footer.php';
    }

    public function delete() {
        try {
            $this->validateCSRF();
            
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) {
                throw new Exception('ID de post inválido');
            }

            // Verificar que el post existe
            $post = $this->postModel->getById($id);
            if (!$post) {
                throw new Exception('Post no encontrado');
            }

            // Eliminar el post
            if ($this->postModel->delete($id)) {
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Post eliminado correctamente'];
            } else {
                throw new Exception('Error al eliminar el post');
            }
        } catch (Exception $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => $e->getMessage()];
        }

        $this->redirect('admin/posts');
    }

    public function edit($id = null) {
        if (!$id) {
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        }

        if (!$id) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'ID de post inválido'];
            $this->redirect('?controller=admin&action=posts');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validateCSRF();
                
                $data = [
                    'menu_id' => (int)($_POST['menu_id'] ?? 0),
                    'title' => Security::sanitizeInput($_POST['title']),
                    'content' => $_POST['content'],
                    'excerpt' => Security::sanitizeInput($_POST['excerpt'] ?? ''),
                    'status' => $_POST['status'] ?? 'draft'
                ];

                // Manejar la eliminación de la imagen destacada
                if (isset($_POST['remove_featured_image']) && $_POST['remove_featured_image'] === '1') {
                    $post = $this->postModel->getById($id);
                    if ($post && !empty($post['featured_image'])) {
                        $imagePath = BASE_PATH . '/uploads/' . $post['featured_image'];
                        if (file_exists($imagePath)) {
                            unlink($imagePath);
                        }
                    }
                    $data['featured_image'] = null;
                }
                // Manejar nueva imagen destacada
                elseif (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
                    $data['featured_image'] = $this->handleImageUpload($_FILES['featured_image']);
                }

                if (empty($data['title'])) {
                    throw new Exception('El título es requerido');
                }

                if ($this->postModel->update($id, $data)) {
                    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Post actualizado correctamente'];
                    $this->redirect('?controller=admin&action=posts');
                }
            } catch (Exception $e) {
                $_SESSION['flash'] = ['type' => 'error', 'message' => $e->getMessage()];
            }
        }

        $post = $this->postModel->getById($id);
        $menus = $this->menuModel->getAll();
        require BASE_PATH . '/views/admin/layout/header.php';
        require BASE_PATH . '/views/admin/posts/form.php';
        require BASE_PATH . '/views/admin/layout/footer.php';
    }

    private function handleImageUpload($file) {
        $uploadDir = BASE_PATH . '/uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = uniqid() . '-' . Security::sanitizeFileName($file['name']);
        $uploadFile = $uploadDir . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $uploadFile)) {
            throw new Exception('Error al subir la imagen');
        }

        return $fileName;
    }

    public function uploadImage() {
        try {
            if (!isset($_FILES['upload'])) {
                throw new Exception('No se ha enviado ninguna imagen');
            }

            $file = $_FILES['upload'];
            $fileName = $this->handleImageUpload($file);
            $url = BASE_URL . '/uploads/' . $fileName;

            // Respuesta para CKEditor
            echo json_encode([
                'uploaded' => 1,
                'fileName' => $fileName,
                'url' => $url
            ]);
            exit;
        } catch (Exception $e) {
            echo json_encode([
                'uploaded' => 0,
                'error' => ['message' => $e->getMessage()]
            ]);
            exit;
        }
    }

    public function view() {
        try {
            $slug = isset($_GET['slug']) ? htmlspecialchars(trim($_GET['slug']), ENT_QUOTES, 'UTF-8') : null;
            
            if (!$slug) {
                throw new Exception('Post no encontrado');
            }

            // Obtener el post con información del menú
            $post = $this->postModel->getBySlug($slug);
            if (!$post) {
                throw new Exception('Post no encontrado');
            }

            // Si el post no está publicado y el usuario no es admin, no mostrar
            if ($post['status'] !== 'published' && (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin')) {
                throw new Exception('Post no encontrado');
            }

            $title = $post['title']; // Para el título de la página
            require BASE_PATH . '/views/layout/header.php';
            require BASE_PATH . '/views/posts/view.php';
            require BASE_PATH . '/views/layout/footer.php';
        } catch (Exception $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => $e->getMessage()];
            $this->redirect('');
        }
    }

    // ... otros métodos del controlador ...
} 