<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';

class UserGenerator {
    private $db;
    private $userModel;
    private $totalUsers = 3000;
    private $statuses = ['active', 'inactive', 'blocked'];
    private $roles = ['user', 'admin'];
    private $statusDistribution = [70, 20, 10]; // 70% active, 20% inactive, 10% blocked
    private $roleDistribution = [90, 10]; // 90% users, 10% admins

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->userModel = new User();
    }

    public function generate() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) FROM users WHERE username LIKE 'UserTest%'");
            $existingUsers = $stmt->fetchColumn();
            
            if ($existingUsers > 0) {
                echo "¿Ya existen {$existingUsers} usuarios de prueba. ¿Desea eliminarlos antes de continuar? (s/n): ";
                $answer = trim(fgets(STDIN));
                if (strtolower($answer) === 's') {
                    $this->db->exec("DELETE FROM users WHERE username LIKE 'UserTest%'");
                    echo "Usuarios de prueba eliminados.\n";
                }
            }

            $startTime = microtime(true);
            $usersCreated = 0;

            echo "Iniciando generación de {$this->totalUsers} usuarios...\n";

            $stmt = $this->db->prepare("
                INSERT INTO users (username, email, password, role, status) 
                VALUES (:username, :email, :password, :role, :status)
            ");

            for ($i = 1; $i <= $this->totalUsers; $i++) {
                $username = "UserTest" . str_pad($i, 4, '0', STR_PAD_LEFT);
                $email = strtolower($username) . "@example.com";
                
                $randomNum = rand(1, 100);
                $status = $this->getRandomStatus($randomNum);
                
                $randomNum = rand(1, 100);
                $role = $this->getRandomRole($randomNum);

                try {
                    $stmt->execute([
                        'username' => $username,
                        'email' => $email,
                        'password' => password_hash('password123', PASSWORD_DEFAULT),
                        'role' => $role,
                        'status' => $status
                    ]);
                    
                    $usersCreated++;
                    
                    if ($usersCreated % 100 === 0) {
                        $percentage = round(($usersCreated / $this->totalUsers) * 100, 2);
                        echo "Progreso: {$percentage}% ({$usersCreated}/{$this->totalUsers})\n";
                    }
                } catch (Exception $e) {
                    echo "Error creando usuario {$username}: " . $e->getMessage() . "\n";
                }
            }

            $endTime = microtime(true);
            $timeElapsed = round($endTime - $startTime, 2);

            echo "\nGeneración completada:\n";
            echo "- Usuarios creados: {$usersCreated}\n";
            echo "- Tiempo total: {$timeElapsed} segundos\n";
            
            $this->showStatistics();

        } catch (Exception $e) {
            echo "Error general: " . $e->getMessage() . "\n";
        }
    }

    private function getRandomStatus($randomNum) {
        if ($randomNum <= $this->statusDistribution[0]) {
            return 'active';
        } elseif ($randomNum <= $this->statusDistribution[0] + $this->statusDistribution[1]) {
            return 'inactive';
        } else {
            return 'blocked';
        }
    }

    private function getRandomRole($randomNum) {
        if ($randomNum <= $this->roleDistribution[0]) {
            return 'user';
        } else {
            return 'admin';
        }
    }

    private function showStatistics() {
        $stmt = $this->userModel->getDb()->query("
            SELECT 
                status, 
                COUNT(*) as count,
                ROUND(COUNT(*) * 100.0 / SUM(COUNT(*)) OVER(), 2) as percentage
            FROM users 
            GROUP BY status
        ");
        $statusStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $this->userModel->getDb()->query("
            SELECT 
                role, 
                COUNT(*) as count,
                ROUND(COUNT(*) * 100.0 / SUM(COUNT(*)) OVER(), 2) as percentage
            FROM users 
            GROUP BY role
        ");
        $roleStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "\nEstadísticas de usuarios:\n";
        echo "\nPor estado:\n";
        foreach ($statusStats as $stat) {
            echo "- {$stat['status']}: {$stat['count']} ({$stat['percentage']}%)\n";
        }

        echo "\nPor rol:\n";
        foreach ($roleStats as $stat) {
            echo "- {$stat['role']}: {$stat['count']} ({$stat['percentage']}%)\n";
        }
    }
}

// Ejecutar el generador
$generator = new UserGenerator();
$generator->generate(); 