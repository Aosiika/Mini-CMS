<div class="content">
    <h1 class="title">Configuración del Sitio</h1>

    <?php if (isset($_SESSION['flash'])): ?>
        <div class="notification is-<?php echo $_SESSION['flash']['type'] === 'error' ? 'danger' : 'success'; ?>">
            <button class="delete"></button>
            <?php 
            echo Security::preventXSS($_SESSION['flash']['message']);
            unset($_SESSION['flash']);
            ?>
        </div>
    <?php endif; ?>

    <div class="box">
        <form method="POST" action="<?php echo BASE_URL; ?>admin/settings">
            <input type="hidden" name="csrf_token" value="<?php echo Security::preventXSS($_SESSION['csrf_token']); ?>">
            
            <?php foreach ($settings as $setting): ?>
                <div class="field">
                    <label class="label"><?php echo Security::preventXSS($setting['setting_description']); ?></label>
                    <div class="control">
                        <input class="input" type="text" 
                               name="settings[<?php echo Security::preventXSS($setting['setting_key']); ?>]" 
                               value="<?php echo Security::preventXSS($setting['setting_value']); ?>">
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="field">
                <div class="control">
                    <button type="submit" class="button is-primary">
                        Guardar Cambios
                    </button>
                </div>
            </div>
        </form>
    </div>
</div> 