<div class="content">
    <h1 class="title"><?php echo isset($menu) ? 'Editar' : 'Crear'; ?> Menú</h1>
    
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="notification is-<?php echo $_SESSION['flash']['type'] === 'error' ? 'danger' : 'success'; ?>">
            <button class="delete"></button>
            <?php 
            echo Security::preventXSS($_SESSION['flash']['message'] ?? '');
            unset($_SESSION['flash']);
            ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo Security::preventXSS($_SESSION['csrf_token'] ?? ''); ?>">
        
        <div class="field">
            <label class="label">Nombre</label>
            <div class="control">
                <input class="input" type="text" name="name" required
                       value="<?php echo Security::preventXSS($menu['name'] ?? ''); ?>"
                       placeholder="Nombre del menú">
            </div>
            <p class="help">El enlace se generará automáticamente basado en este nombre</p>
        </div>

        <div class="field">
            <label class="label">Menú Padre</label>
            <div class="control">
                <div class="select">
                    <select name="parent_id">
                        <option value="">Ninguno</option>
                        <?php foreach ($menus as $menuItem): ?>
                            <?php if (!isset($menu) || $menuItem['id'] != $menu['id']): ?>
                                <option value="<?php echo $menuItem['id']; ?>"
                                    <?php echo (isset($menu) && isset($menu['parent_id']) && $menu['parent_id'] == $menuItem['id']) ? 'selected' : ''; ?>>
                                    <?php echo Security::preventXSS($menuItem['name']); ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="field">
            <label class="label">Orden</label>
            <div class="control">
                <input class="input" type="number" name="order_index" min="0"
                       value="<?php echo isset($menu) ? (int)$menu['order_index'] : '0'; ?>">
            </div>
        </div>

        <div class="field">
            <div class="control">
                <label class="checkbox">
                    <input type="checkbox" name="is_visible"
                           <?php echo (!isset($menu) || ($menu['is_visible'] ?? true)) ? 'checked' : ''; ?>>
                    Visible en el menú
                </label>
            </div>
        </div>

        <div class="field is-grouped">
            <div class="control">
                <button type="submit" class="button is-primary">Guardar</button>
            </div>
            <div class="control">
                <a href="<?php echo BASE_URL; ?>?controller=menu&action=index" class="button is-link is-light">
                    Cancelar
                </a>
            </div>
        </div>
    </form>
</div> 