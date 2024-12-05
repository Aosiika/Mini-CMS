<!DOCTYPE html>
<html lang="es" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="/public/css/dark-theme.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar is-dark">
        <div class="navbar-brand">
            <a class="navbar-item" href="/">
                <strong>Mi CMS</strong>
            </a>
        </div>
        <div class="navbar-menu">
            <div class="navbar-end">
                <div class="navbar-item">
                    <button class="button is-dark" id="theme-toggle">
                        <span class="icon">
                            <i class="fas fa-sun"></i>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Login Form -->
    <section class="hero is-fullheight-with-navbar">
        <div class="hero-body">
            <div class="container">
                <div class="columns is-centered">
                    <div class="column is-4">
                        <div class="box">
                            <h1 class="title has-text-centered">Iniciar Sesión</h1>

                            <?php if (isset($_SESSION['flash'])): ?>
                                <div class="notification is-<?php echo $_SESSION['flash']['type'] === 'error' ? 'danger' : 'success'; ?>">
                                    <button class="delete"></button>
                                    <?php 
                                    echo $_SESSION['flash']['message'];
                                    unset($_SESSION['flash']);
                                    ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="/login">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                
                                <div class="field">
                                    <label class="label">Usuario</label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="text" name="username" required>
                                        <span class="icon is-small is-left">
                                            <i class="fas fa-user"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="field">
                                    <label class="label">Contraseña</label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="password" name="password" required>
                                        <span class="icon is-small is-left">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="control">
                                        <button type="submit" class="button is-primary is-fullwidth">
                                            <span class="icon">
                                                <i class="fas fa-sign-in-alt"></i>
                                            </span>
                                            <span>Iniciar Sesión</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Notificaciones
        const deleteButtons = document.querySelectorAll('.notification .delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', () => {
                button.parentElement.remove();
            });
        });

        // Theme toggle
        const themeToggle = document.getElementById('theme-toggle');
        const html = document.querySelector('html');
        const icon = themeToggle.querySelector('i');

        themeToggle.addEventListener('click', () => {
            if (html.getAttribute('data-theme') === 'dark') {
                html.setAttribute('data-theme', 'light');
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            } else {
                html.setAttribute('data-theme', 'dark');
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            }
        });
    });
    </script>
</body>
</html> 