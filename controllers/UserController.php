<?php
class UserController extends BaseController {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }

    public function index() {
        $perPage = $_GET['per_page'] ?? 10;
        $page = $_GET['page'] ?? 1;
        $offset = ($page - 1) * $perPage;

        $users = $this->userModel->getUsers($perPage, $offset);
        $totalUsers = $this->userModel->countUsers();

        require BASE_PATH . '/views/admin/layout/header.php';
        require BASE_PATH . '/views/admin/users/index.php';
        require BASE_PATH . '/views/admin/layout/footer.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validateCSRF();
                
                $data = [
                    'username' => Security::sanitizeInput($_POST['username']),
                    'email' => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
                    'password' => $_POST['password'],
                    'role' => $_POST['role'],
                    'status' => $_POST['status']
                ];

                // Validaciones
                if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
                    throw new Exception('Todos los campos son requeridos');
                }

                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('Email inválido');
                }

                if (strlen($data['password']) < 6) {
                    throw new Exception('La contraseña debe tener al menos 6 caracteres');
                }

                if ($this->userModel->create($data)) {
                    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Usuario creado correctamente'];
                    $this->redirect('admin/users');
                }
            } catch (Exception $e) {
                $_SESSION['flash'] = ['type' => 'error', 'message' => $e->getMessage()];
            }
        }

        require BASE_PATH . '/views/admin/layout/header.php';
        require BASE_PATH . '/views/admin/users/form.php';
        require BASE_PATH . '/views/admin/layout/footer.php';
    }

    public function edit($id = null) {
        if (!$id) {
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        }

        if (!$id) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'ID de usuario inválido'];
            $this->redirect('admin/users');
        }

        $user = $this->userModel->getById($id);
        if (!$user) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Usuario no encontrado'];
            $this->redirect('admin/users');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validateCSRF();
                
                $data = [
                    'username' => Security::sanitizeInput($_POST['username']),
                    'email' => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
                    'role' => $_POST['role'],
                    'status' => $_POST['status']
                ];

                // Solo actualizar la contraseña si se proporciona una nueva
                if (!empty($_POST['password'])) {
                    if (strlen($_POST['password']) < 6) {
                        throw new Exception('La contraseña debe tener al menos 6 caracteres');
                    }
                    $data['password'] = $_POST['password'];
                }

                if ($this->userModel->update($id, $data)) {
                    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Usuario actualizado correctamente'];
                    $this->redirect('admin/users');
                }
            } catch (Exception $e) {
                $_SESSION['flash'] = ['type' => 'error', 'message' => $e->getMessage()];
            }
        }

        require BASE_PATH . '/views/admin/layout/header.php';
        require BASE_PATH . '/views/admin/users/form.php';
        require BASE_PATH . '/views/admin/layout/footer.php';
    }

    public function updateRole() {
        try {
            $this->validateCSRF();
            
            $data = json_decode(file_get_contents('php://input'), true);
            $userId = filter_var($data['user_id'], FILTER_VALIDATE_INT);
            $role = $data['role'];

            if (!$userId || !in_array($role, ['user', 'admin'])) {
                throw new Exception('Datos inválidos');
            }

            if ($this->userModel->updateRole($userId, $role)) {
                $this->jsonResponse(['success' => true]);
            } else {
                throw new Exception('Error al actualizar el rol');
            }
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function updateStatus() {
        try {
            $this->validateCSRF();
            
            $data = json_decode(file_get_contents('php://input'), true);
            $userId = filter_var($data['user_id'], FILTER_VALIDATE_INT);
            $status = $data['status'];

            if (!$userId || !in_array($status, ['active', 'inactive', 'blocked'])) {
                throw new Exception('Datos inválidos');
            }

            if ($this->userModel->updateStatus($userId, $status)) {
                $this->jsonResponse(['success' => true]);
            } else {
                throw new Exception('Error al actualizar el estado');
            }
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function delete() {
        try {
            $this->validateCSRF();
            
            $data = json_decode(file_get_contents('php://input'), true);
            $userId = filter_var($data['user_id'], FILTER_VALIDATE_INT);

            if (!$userId) {
                throw new Exception('ID de usuario inválido');
            }

            // No permitir eliminar el propio usuario
            if ($userId === $_SESSION['user']['id']) {
                throw new Exception('No puedes eliminar tu propio usuario');
            }

            if ($this->userModel->delete($userId)) {
                $this->jsonResponse(['success' => true]);
            } else {
                throw new Exception('Error al eliminar el usuario');
            }
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function someAction() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar el token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $_SESSION['flash'] = ['type' => 'error', 'message' => 'Token CSRF inválido'];
                header('Location: /ruta-de-error');
                exit;
            }

            // Lógica de la acción
        }
    }
} 