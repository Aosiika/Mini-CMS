<?php
// Cargar configuración
require_once 'config/config.php';

// Autoloader
spl_autoload_register(function ($class) {
    // Directorios donde buscar las clases
    $directories = [
        'controllers/',
        'models/',
        'core/',
        'helpers/'
    ];

    foreach ($directories as $directory) {
        $file = __DIR__ . '/' . $directory . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Router básico
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Sanitizar input
$controller = preg_replace('/[^a-zA-Z]/', '', $controller);
$action = preg_replace('/[^a-zA-Z]/', '', $action);

// Crear nombre del controlador
$controllerName = ucfirst($controller) . 'Controller';
$controllerFile = __DIR__ . '/controllers/' . $controllerName . '.php';

try {
    if (file_exists($controllerFile)) {
        $controllerInstance = new $controllerName();
        if (method_exists($controllerInstance, $action)) {
            $controllerInstance->$action();
        } else {
            throw new Exception("Acción no encontrada");
        }
    } else {
        throw new Exception("Controlador no encontrado");
    }
} catch (Exception $e) {
    // Manejo de errores
    $_SESSION['flash'] = ['type' => 'error', 'message' => $e->getMessage()];
    header('Location: /');
    exit;
}
