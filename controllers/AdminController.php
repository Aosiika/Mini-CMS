<?php
class AdminController extends BaseController {
    private $userModel;
    private $menuModel;
    private $postModel;
    private $pageModel;

    public function __construct() {
        parent::__construct();
        // Inicializar los modelos
        $this->userModel = new User();
        $this->menuModel = new Menu();
        $this->postModel = new Post();
        $this->pageModel = new Page();
        // Verificar permisos
        $this->requireAdmin();
    }

    public function dashboard() {
        // Obtener estadísticas
        $stats = [
            'posts_total' => $this->postModel->countTotal(),
            'pages_total' => $this->pageModel->countTotal(),
            'users_total' => $this->userModel->countTotal(),
            'comments_total' => 0, // Por implementar sistema de comentarios
        ];

        // Obtener posts recientes
        $recent_posts = $this->postModel->getRecent(5);

        // Obtener actividad reciente
        $recent_activity = $this->getRecentActivity();

        require BASE_PATH . '/views/admin/layout/header.php';
        require BASE_PATH . '/views/admin/dashboard.php';
        require BASE_PATH . '/views/admin/layout/footer.php';
    }

    private function getRecentActivity() {
        $activity = [];
        
        // Obtener últimos posts
        $recent_posts = $this->postModel->getRecent(3);
        foreach ($recent_posts as $post) {
            $activity[] = [
                'type' => 'post',
                'action' => 'created',
                'title' => $post['title'],
                'date' => $post['created_at']
            ];
        }

        // Obtener últimos usuarios registrados
        $recent_users = $this->userModel->getRecent(3);
        foreach ($recent_users as $user) {
            $activity[] = [
                'type' => 'user',
                'action' => 'registered',
                'title' => $user['username'],
                'date' => $user['created_at']
            ];
        }

        // Ordenar por fecha
        usort($activity, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return array_slice($activity, 0, 5);
    }

    public function posts() {
        $posts = $this->postModel->getAll();
        require BASE_PATH . '/views/admin/layout/header.php';
        require BASE_PATH . '/views/admin/posts/index.php';
        require BASE_PATH . '/views/admin/layout/footer.php';
    }

    public function menus() {
        $menus = $this->menuModel->getAll();
        require BASE_PATH . '/views/admin/layout/header.php';
        require BASE_PATH . '/views/admin/menus/index.php';
        require BASE_PATH . '/views/admin/layout/footer.php';
    }

    public function pages() {
        $pages = $this->pageModel->getAll();
        require BASE_PATH . '/views/admin/layout/header.php';
        require BASE_PATH . '/views/admin/pages/index.php';
        require BASE_PATH . '/views/admin/layout/footer.php';
    }
} 