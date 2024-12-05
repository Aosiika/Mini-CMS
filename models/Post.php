<?php
class Post {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("
            SELECT p.*, m.name as menu_name 
            FROM posts p 
            LEFT JOIN menus m ON p.menu_id = m.id 
            ORDER BY p.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public function getBySlug($slug) {
        $stmt = $this->db->prepare("
            SELECT 
                p.*,
                m.name as menu_name,
                m.slug as menu_slug
            FROM posts p
            LEFT JOIN menus m ON p.menu_id = m.id
            WHERE p.slug = :slug
            AND (p.status = 'published' OR :is_admin = 1)
        ");
        
        $isAdmin = isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin' ? 1 : 0;
        $stmt->execute(['slug' => $slug, 'is_admin' => $isAdmin]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO posts (menu_id, title, slug, content, excerpt, status, featured_image) 
            VALUES (:menu_id, :title, :slug, :content, :excerpt, :status, :featured_image)
        ");
        
        return $stmt->execute([
            'menu_id' => $data['menu_id'] ?: null,
            'title' => $data['title'],
            'slug' => $this->createSlug($data['title']),
            'content' => $data['content'],
            'excerpt' => $data['excerpt'] ?? null,
            'status' => $data['status'] ?? 'draft',
            'featured_image' => $data['featured_image'] ?? null
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE posts 
            SET menu_id = :menu_id,
                title = :title, 
                slug = :slug,
                content = :content,
                excerpt = :excerpt,
                status = :status,
                featured_image = :featured_image
            WHERE id = :id
        ");
        
        return $stmt->execute([
            'id' => $id,
            'menu_id' => $data['menu_id'] ?: null,
            'title' => $data['title'],
            'slug' => $this->createSlug($data['title'], $id),
            'content' => $data['content'],
            'excerpt' => $data['excerpt'] ?? null,
            'status' => $data['status'] ?? 'draft',
            'featured_image' => $data['featured_image'] ?? null
        ]);
    }

    private function createSlug($title, $excludeId = null) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM posts WHERE slug = :slug AND id != :id
        ");
        $stmt->execute(['slug' => $slug, 'id' => $excludeId ?? 0]);
        
        if ($stmt->fetchColumn() > 0) {
            $slug .= '-' . time();
        }
        
        return $slug;
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM posts WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT p.*, m.name as menu_name 
            FROM posts p 
            LEFT JOIN menus m ON p.menu_id = m.id 
            WHERE p.id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function countTotal() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM posts");
        return $stmt->fetchColumn();
    }

    public function getRecent($limit = 5) {
        $stmt = $this->db->prepare("
            SELECT p.*, m.name as menu_name 
            FROM posts p 
            LEFT JOIN menus m ON p.menu_id = m.id 
            ORDER BY p.created_at DESC 
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getPostsByMenuId($menuId) {
        $stmt = $this->db->prepare("
            SELECT p.*, m.name as menu_name 
            FROM posts p 
            LEFT JOIN menus m ON p.menu_id = m.id 
            WHERE p.menu_id = :menu_id 
            AND p.status = 'published' 
            ORDER BY p.created_at DESC
        ");
        $stmt->execute(['menu_id' => $menuId]);
        return $stmt->fetchAll();
    }
} 