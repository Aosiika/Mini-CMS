<?php
class HomeController extends BaseController {
    public function index() {
        require BASE_PATH . '/views/layout/header.php';
        require BASE_PATH . '/views/home/index.php';
        require BASE_PATH . '/views/layout/footer.php';
    }
} 