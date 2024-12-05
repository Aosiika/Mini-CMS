<?php
require_once __DIR__ . '/../config/DatabaseConnection.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("
            SELECT id, username, email, role, status, last_login, created_at 
            FROM users 
            ORDER BY created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT id, username, email, role, status, last_login, created_at 
            FROM users 
            WHERE id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create($data) {
        // Verificar si el usuario o email ya existe
        if ($this->usernameExists($data['username'])) {
            throw new Exception('El nombre de usuario ya está en uso');
        }
        if ($this->emailExists($data['email'])) {
            throw new Exception('El email ya está en uso');
        }

        $stmt = $this->db->prepare("
            INSERT INTO users (username, email, password, role, status) 
            VALUES (:username, :email, :password, :role, :status)
        ");
        
        return $stmt->execute([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $data['role'],
            'status' => $data['status']
        ]);
    }

    public function update($id, $data) {
        // Verificar si el usuario o email ya existe (excluyendo el usuario actual)
        if ($this->usernameExists($data['username'], $id)) {
            throw new Exception('El nombre de usuario ya está en uso');
        }
        if ($this->emailExists($data['email'], $id)) {
            throw new Exception('El email ya está en uso');
        }

        $sql = "UPDATE users SET 
                username = :username,
                email = :email,
                role = :role,
                status = :status";

        $params = [
            'id' => $id,
            'username' => $data['username'],
            'email' => $data['email'],
            'role' => $data['role'],
            'status' => $data['status']
        ];

        // Solo actualizar la contraseña si se proporciona una nueva
        if (isset($data['password']) && !empty($data['password'])) {
            $sql .= ", password = :password";
            $params['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function updateRole($userId, $role) {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET role = :role 
            WHERE id = :id
        ");
        return $stmt->execute([
            'id' => $userId,
            'role' => $role
        ]);
    }

    public function updateStatus($userId, $status) {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET status = :status 
            WHERE id = :id
        ");
        return $stmt->execute([
            'id' => $userId,
            'status' => $status
        ]);
    }

    private function usernameExists($username, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM users WHERE username = :username";
        $params = ['username' => $username];

        if ($excludeId) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    private function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        $params = ['email' => $email];

        if ($excludeId) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    public function updateLastLogin($userId) {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET last_login = CURRENT_TIMESTAMP 
            WHERE id = :id
        ");
        return $stmt->execute(['id' => $userId]);
    }

    public function countTotal() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM users");
        return $stmt->fetchColumn();
    }

    public function getRecent($limit = 5) {
        $stmt = $this->db->prepare("
            SELECT * FROM users 
            ORDER BY created_at DESC 
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function authenticate($username, $password) {
        try {
            // Añadir logging para depuración
            error_log("Intento de login para usuario: " . $username);
            
            $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ? AND status = 'active'");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                error_log("Usuario no encontrado o inactivo: " . $username);
                return false;
            }

            error_log("Hash almacenado: " . $user['password']);
            $verified = password_verify($password, $user['password']);
            error_log("Verificación de contraseña: " . ($verified ? "exitosa" : "fallida"));

            if ($verified) {
                $this->updateLastLogin($user['id']);
                unset($user['password']);
                return $user;
            }

            return false;
        } catch (PDOException $e) {
            error_log("Error de autenticación: " . $e->getMessage());
            return false;
        }
    }

    public function isAdmin() {
        return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
    }

    public function getUsers($limit, $offset) {
        $stmt = $this->db->prepare("SELECT * FROM users LIMIT :limit OFFSET :offset");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countUsers() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM users");
        return $stmt->fetchColumn();
    }
} 