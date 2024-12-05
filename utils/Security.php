<?php
class Security {
    public static function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    public static function preventXSS($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    public static function generateRandomToken($length = 32) {
        return bin2hex(random_bytes($length));
    }

    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
} 