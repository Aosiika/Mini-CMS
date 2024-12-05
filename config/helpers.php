<?php
// Funciones de ayuda
function isLoggedIn() {
    return isset($_SESSION['user']);
}

function isAdmin() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
}

function redirect($path) {
    header('Location: ' . BASE_URL . $path);
    exit;
}

function formatDate($date) {
    return date('d/m/Y H:i', strtotime($date));
}

function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $message = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $message;
    }
    return null;
}

function url($path = '') {
    return BASE_URL . ltrim($path, '/');
}

// Otras funciones helper que necesites...