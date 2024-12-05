<?php
class AuthController extends BaseController {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }

    public function login() {
        if (isLoggedIn()) {
            $this->redirect('admin/dashboard');
        }

        // Verificar si la sesión ya está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Generar el token CSRF si no existe
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = Security::generateRandomToken();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar el token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $_SESSION['flash'] = ['type' => 'error', 'message' => 'Token CSRF inválido'];
                header('Location: /login');
                exit;
            }
            
            $username = Security::sanitizeInput($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $_SESSION['flash'] = ['type' => 'error', 'message' => 'Usuario y contraseña son requeridos'];
            } else {
                try {
                    $user = $this->userModel->authenticate($username, $password);

                    if ($user) {
                        $_SESSION['user'] = $user;
                        $_SESSION['flash'] = ['type' => 'success', 'message' => '¡Bienvenido!'];
                        $this->redirect('admin/dashboard');
                    } else {
                        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Usuario o contraseña incorrectos'];
                    }
                } catch (Exception $e) {
                    error_log('Error de login: ' . $e->getMessage());
                    $_SESSION['flash'] = ['type' => 'error', 'message' => $e->getMessage()];
                }
            }
        }

        require BASE_PATH . '/views/auth/login.php';
    }

    public function logout() {
        session_unset();
        session_destroy();
        $this->redirect('login');
    }
} 