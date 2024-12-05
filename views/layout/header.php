<?php
$menuModel = new Menu();
$menuTree = $menuModel->getMenuTree();
?>
<!DOCTYPE html>
<html lang="es" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title . ' - ' : ''; ?><?php echo $this->settings['site_name']; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>public/css/dark-theme.css">
    <style>
        /* Estilos base para el tema */
        :root {
            --background-color: #ffffff;
            --text-color: #4a4a4a;
            --card-background: #ffffff;
            --card-text: #4a4a4a;
            --navbar-background: #00d1b2;
            --navbar-text: #ffffff;
            --border-color: #dbdbdb;
            --link-color: #3273dc;
            --button-background: #f5f5f5;
            --button-text: #363636;
            --footer-background: #fafafa;
        }

        [data-theme="dark"] {
            --background-color: #1a1a1a;
            --text-color: #ffffff;
            --card-background: #2c2c2c;
            --card-text: #ffffff;
            --navbar-background: #2c2c2c;
            --navbar-text: #ffffff;
            --border-color: #363636;
            --link-color: #3273dc;
            --button-background: #363636;
            --button-text: #ffffff;
            --footer-background: #2c2c2c;
        }

        body {
            background-color: var(--background-color);
            color: var(--text-color);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .navbar.is-primary {
            background-color: var(--navbar-background) !important;
        }

        .navbar-item, .navbar-link {
            color: var(--navbar-text) !important;
        }

        .title {
            color: var(--text-color) !important;
        }

        .button.is-light {
            background-color: var(--button-background) !important;
            color: var(--button-text) !important;
        }
    </style>
</head>
<body>
    <nav class="navbar is-primary" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <a class="navbar-item" href="/">
                <?php echo $this->settings['site_name']; ?>
            </a>
            <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarBasic">
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
            </a>
        </div>

        <div id="navbarBasic" class="navbar-menu">
            <div class="navbar-start">
                <?php foreach ($menuTree as $menu): ?>
                    <?php if (empty($menu['children'])): ?>
                        <!-- Menú simple -->
                        <a class="navbar-item" href="/<?php echo Security::preventXSS($menu['slug']); ?>">
                            <?php echo Security::preventXSS($menu['name']); ?>
                        </a>
                    <?php else: ?>
                        <!-- Menú desplegable -->
                        <div class="navbar-item has-dropdown is-hoverable">
                            <a class="navbar-link" href="/<?php echo Security::preventXSS($menu['slug']); ?>">
                                <?php echo Security::preventXSS($menu['name']); ?>
                            </a>
                            <div class="navbar-dropdown">
                                <?php foreach ($menu['children'] as $child): ?>
                                    <a class="navbar-item" href="/<?php echo Security::preventXSS($child['slug']); ?>">
                                        <?php echo Security::preventXSS($child['name']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <div class="navbar-end">
                <div class="navbar-item">
                    <div class="buttons">
                        <button class="button" id="theme-toggle">
                            <span class="icon">
                                <i class="fas fa-sun"></i>
                            </span>
                        </button>
                        <?php if (isLoggedIn()): ?>
                            <?php if (isAdmin()): ?>
                                <a href="/admin/dashboard" class="button is-light">
                                    <span class="icon">
                                        <i class="fas fa-cog"></i>
                                    </span>
                                    <span>Panel Admin</span>
                                </a>
                            <?php endif; ?>
                            <a href="/logout" class="button is-light">
                                <span class="icon">
                                    <i class="fas fa-sign-out-alt"></i>
                                </span>
                                <span>Cerrar Sesión</span>
                            </a>
                        <?php else: ?>
                            <a href="/login" class="button is-light">
                                <span class="icon">
                                    <i class="fas fa-sign-in-alt"></i>
                                </span>
                                <span>Iniciar Sesión</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <section class="section">
        <div class="container">
</body>
</html>