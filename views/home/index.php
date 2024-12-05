<div class="content">
    <h1 class="title">Bienvenido a <?php echo $this->settings['site_name']; ?></h1>
    
    <div class="box">
        <div class="content">
            <p class="is-size-4">Este es un CMS simple y elegante construido con PHP y Bulma CSS.</p>
            <p>Navega por los menús para explorar el contenido.</p>
        </div>
    </div>

    <?php if (isset($_SESSION['flash'])): ?>
        <div class="notification is-<?php echo $_SESSION['flash']['type'] === 'error' ? 'danger' : 'success'; ?>">
            <button class="delete"></button>
            <?php 
            echo Security::preventXSS($_SESSION['flash']['message']);
            unset($_SESSION['flash']);
            ?>
        </div>
    <?php endif; ?>
</div> 