<!DOCTYPE html>
<html lang="es" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - <?php echo $this->settings['site_name']; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>public/css/dark-theme.css">
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
</head>
<body>
    <div class="columns is-gapless" style="min-height: 100vh;">
        <!-- Sidebar -->
        <div class="column is-2">
            <aside class="menu p-4">
                <p class="menu-label">
                    General
                </p>
                <ul class="menu-list">
                    <li><a href="/admin/dashboard">
                        <span class="icon"><i class="fas fa-tachometer-alt"></i></span>
                        <span>Dashboard</span>
                    </a></li>
                </ul>
                <p class="menu-label">
                    Contenido
                </p>
                <ul class="menu-list">
                    <li><a href="/admin/posts">
                        <span class="icon"><i class="fas fa-file-alt"></i></span>
                        <span>Posts</span>
                    </a></li>
                </ul>
                <p class="menu-label">
                    Configuración
                </p>
                <ul class="menu-list">
                    <li><a href="/admin/menus">
                        <span class="icon"><i class="fas fa-bars"></i></span>
                        <span>Menús</span>
                    </a></li>
                    <li><a href="/admin/users">
                        <span class="icon"><i class="fas fa-users"></i></span>
                        <span>Usuarios</span>
                    </a></li>
                    <li><a href="/admin/settings">
                        <span class="icon"><i class="fas fa-cog"></i></span>
                        <span>Configuración del Sitio</span>
                    </a></li>
                    <li><a href="/logout">
                        <span class="icon"><i class="fas fa-sign-out-alt"></i></span>
                        <span>Cerrar Sesión</span>
                    </a></li>
                </ul>
            </aside>
        </div>

        <!-- Contenido principal -->
        <div class="column">
            <nav class="navbar is-white">
                <div class="navbar-brand">
                    <a class="navbar-item" href="/">
                        <strong>Panel de Administración</strong>
                    </a>
                </div>
                <div class="navbar-end">
                    <div class="navbar-item">
                        <div class="buttons">
                            <button class="button is-light" id="theme-toggle">
                                <span class="icon">
                                    <i class="fas fa-sun"></i>
                                </span>
                            </button>
                            <a href="/" class="button is-light">
                                <span class="icon"><i class="fas fa-home"></i></span>
                                <span>Ver Sitio</span>
                            </a>
                        </div>
                    </div>
                </div>
            </nav>
            <div class="section">
                <div class="container">
</body>
</html>