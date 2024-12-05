<?php
class Settings {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM site_settings ORDER BY setting_key");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get($key) {
        $stmt = $this->db->prepare("SELECT setting_value FROM site_settings WHERE setting_key = :key");
        $stmt->execute(['key' => $key]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['setting_value'] : null;
    }

    public function update($key, $value) {
        $stmt = $this->db->prepare("
            UPDATE site_settings 
            SET setting_value = :value 
            WHERE setting_key = :key
        ");
        return $stmt->execute([
            'key' => $key,
            'value' => $value
        ]);
    }

    public function updateBatch($settings) {
        $success = true;
        foreach ($settings as $key => $value) {
            if (!$this->update($key, $value)) {
                $success = false;
            }
        }
        return $success;
    }
} 