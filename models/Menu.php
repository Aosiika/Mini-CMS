<?php
class Menu {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM menus ORDER BY parent_id, order_index");
        return $stmt->fetchAll();
    }

    public function getVisible() {
        $stmt = $this->db->query("
            SELECT * FROM menus 
            WHERE is_visible = 1 
            ORDER BY parent_id, order_index ASC
        ");
        return $stmt->fetchAll();
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO menus (name, slug, url, parent_id, order_index, is_visible) 
            VALUES (:name, :slug, :url, :parent_id, :order_index, :is_visible)
        ");
        
        $slug = $this->generateURL($data['name']);
        
        return $stmt->execute([
            'name' => $data['name'],
            'slug' => $slug,
            'url' => $slug,
            'parent_id' => $data['parent_id'] ?: null,
            'order_index' => $data['order_index'] ?? 0,
            'is_visible' => $data['is_visible'] ?? true
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE menus 
            SET name = :name,
                url = :url,
                parent_id = :parent_id,
                order_index = :order_index,
                is_visible = :is_visible
            WHERE id = :id
        ");
        
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'url' => $data['url'] ?? $this->generateURL($data['name']),
            'parent_id' => $data['parent_id'] ?: null,
            'order_index' => $data['order_index'],
            'is_visible' => $data['is_visible']
        ]);
    }

    public function delete($id) {
        // Primero actualizamos los menús hijos para que no tengan padre
        $stmt = $this->db->prepare("
            UPDATE menus SET parent_id = NULL WHERE parent_id = :id
        ");
        $stmt->execute(['id' => $id]);

        // Luego eliminamos el menú
        $stmt = $this->db->prepare("DELETE FROM menus WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM menus WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    private function createSlug($text, $excludeId = null) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text)));
        
        // Verificar si el slug ya existe
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM menus WHERE slug = :slug AND id != :id
        ");
        $stmt->execute(['slug' => $slug, 'id' => $excludeId ?? 0]);
        
        if ($stmt->fetchColumn() > 0) {
            $slug .= '-' . time();
        }
        
        return $slug;
    }

    public function getMenuTree() {
        // Primero obtener todos los menús
        $stmt = $this->db->query("
            SELECT id, name, slug, parent_id, order_index as `order`, is_visible 
            FROM menus 
            WHERE is_visible = 1 
            ORDER BY order_index ASC
        ");
        $menus = $stmt->fetchAll();
        
        // Construir el árbol
        $tree = [];
        $children = [];
        
        // Primero agrupar los hijos por padre
        foreach ($menus as $menu) {
            if ($menu['parent_id']) {
                if (!isset($children[$menu['parent_id']])) {
                    $children[$menu['parent_id']] = [];
                }
                $children[$menu['parent_id']][] = $menu;
            }
        }
        
        // Construir el árbol final
        foreach ($menus as $menu) {
            if (!$menu['parent_id']) {
                $menu['children'] = isset($children[$menu['id']]) ? $children[$menu['id']] : [];
                $tree[] = $menu;
            }
        }
        
        return $tree;
    }

    private function getChildren($menus, $parentId) {
        $children = [];
        foreach ($menus as $menu) {
            if ($menu['parent_id'] == $parentId) {
                $menu['children'] = $this->getChildren($menus, $menu['id']);
                $children[] = $menu;
            }
        }
        return $children;
    }

    private function generateURL($name) {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    }

    public function getBySlug($slug) {
        $stmt = $this->db->prepare("
            SELECT id, name, slug, url, parent_id, order_index, is_visible 
            FROM menus 
            WHERE slug = :slug
        ");
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch();
    }
} 