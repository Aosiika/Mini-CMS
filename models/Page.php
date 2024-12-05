<?php
class Page {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM pages ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function getBySlug($slug) {
        $stmt = $this->db->prepare("SELECT * FROM pages WHERE slug = :slug AND is_active = 1");
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO pages (title, slug, content, is_active) 
            VALUES (:title, :slug, :content, :is_active)
        ");
        
        return $stmt->execute([
            'title' => $data['title'],
            'slug' => $this->createSlug($data['title']),
            'content' => $data['content'],
            'is_active' => $data['is_active'] ?? true
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE pages 
            SET title = :title, 
                slug = :slug,
                content = :content,
                is_active = :is_active 
            WHERE id = :id
        ");
        
        return $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'slug' => $this->createSlug($data['title'], $id),
            'content' => $data['content'],
            'is_active' => $data['is_active'] ?? true
        ]);
    }

    private function createSlug($title, $excludeId = null) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM pages WHERE slug = :slug AND id != :id
        ");
        $stmt->execute(['slug' => $slug, 'id' => $excludeId ?? 0]);
        
        if ($stmt->fetchColumn() > 0) {
            $slug .= '-' . time();
        }
        
        return $slug;
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM pages WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM pages WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function countTotal() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM pages");
        return $stmt->fetchColumn();
    }

    public function getRecent($limit = 5) {
        $stmt = $this->db->prepare("
            SELECT * FROM pages 
            ORDER BY created_at DESC 
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
} 