<?php
session_start();

// Configuración de la aplicación
define('BASE_PATH', __DIR__ . '/..');
define('BASE_URL', '/');

// Cargar clases base
require_once __DIR__ . '/DatabaseConnection.php';
require_once __DIR__ . '/helpers.php';