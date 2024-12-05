<?php
abstract class BaseController {
    protected $db;
    protected $security;
    protected $settings;

    public function __construct() {
        if (!function_exists('isLoggedIn')) {
            require_once BASE_PATH . '/config/config.php';
        }
        
        $this->db = Database::getInstance()->getConnection();
        $this->checkSecurityHeaders();
        $this->security = new Security();
        $this->initCSRFToken();
        $this->settings = $this->loadSettings();
    }

    protected function checkSecurityHeaders() {
        header("X-XSS-Protection: 1; mode=block");
        header("X-Frame-Options: SAMEORIGIN");
        header("X-Content-Type-Options: nosniff");
        header("Referrer-Policy: strict-origin-when-cross-origin");
        header("Content-Security-Policy: default-src 'self' https:; script-src 'self' 'unsafe-inline' 'unsafe-eval' https:; style-src 'self' 'unsafe-inline' https:; img-src 'self' data: https:;");
    }

    protected function initCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    protected function validateCSRF() {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!$token || !isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            throw new Exception('Token CSRF inválido');
        }
    }

    protected function requireAuth() {
        if (!isLoggedIn()) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Debe iniciar sesión'];
            $this->redirect('login');
            exit;
        }
    }

    protected function requireAdmin() {
        if (!isAdmin()) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Acceso denegado'];
            $this->redirect('');
            exit;
        }
    }

    protected function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect($path = '') {
        header('Location: /' . ltrim($path, '/'));
        exit;
    }

    protected function generateUrl($route, $params = []) {
        $url = BASE_URL . ltrim($route, '/');
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        return $url;
    }

    protected function loadSettings() {
        $settingsModel = new Settings();
        return [
            'site_name' => $settingsModel->get('site_name'),
            'site_footer' => $settingsModel->get('site_footer')
        ];
    }
} 