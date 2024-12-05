<div class="content">
    <h1 class="title">Gestión de Menús</h1>
    
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="notification is-<?php echo $_SESSION['flash']['type'] === 'error' ? 'danger' : 'success'; ?>">
            <button class="delete"></button>
            <?php 
            echo Security::preventXSS($_SESSION['flash']['message']);
            unset($_SESSION['flash']);
            ?>
        </div>
    <?php endif; ?>

    <div class="level">
        <div class="level-left">
            <div class="level-item">
                <a href="<?php echo BASE_URL; ?>?controller=menu&action=create" class="button is-primary">
                    <span class="icon">
                        <i class="fas fa-plus"></i>
                    </span>
                    <span>Nuevo Menú</span>
                </a>
            </div>
        </div>
    </div>

    <div class="table-container">
        <table class="table is-fullwidth is-striped is-hoverable">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>URL</th>
                    <th>Padre</th>
                    <th>Orden</th>
                    <th>Visible</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($menus as $menu): ?>
                    <tr>
                        <td><?php echo Security::preventXSS($menu['name']); ?></td>
                        <td><?php echo Security::preventXSS($menu['url']); ?></td>
                        <td>
                            <?php 
                            if ($menu['parent_id']) {
                                foreach ($menus as $parentMenu) {
                                    if ($parentMenu['id'] == $menu['parent_id']) {
                                        echo Security::preventXSS($parentMenu['name']);
                                        break;
                                    }
                                }
                            }
                            ?>
                        </td>
                        <td><?php echo $menu['order_index']; ?></td>
                        <td>
                            <span class="tag <?php echo $menu['is_visible'] ? 'is-success' : 'is-danger'; ?>">
                                <?php echo $menu['is_visible'] ? 'Sí' : 'No'; ?>
                            </span>
                        </td>
                        <td>
                            <div class="buttons are-small">
                                <a href="<?php echo BASE_URL; ?>?controller=menu&action=edit&id=<?php echo $menu['id']; ?>" 
                                   class="button is-info">
                                    <span class="icon">
                                        <i class="fas fa-edit"></i>
                                    </span>
                                </a>
                                <form method="POST" action="<?php echo BASE_URL; ?>?controller=menu&action=delete" 
                                      class="is-inline" onsubmit="return confirm('¿Estás seguro?');">
                                    <input type="hidden" name="csrf_token" value="<?php echo Security::preventXSS($_SESSION['csrf_token']); ?>">
                                    <input type="hidden" name="id" value="<?php echo $menu['id']; ?>">
                                    <button type="submit" class="button is-danger">
                                        <span class="icon">
                                            <i class="fas fa-trash"></i>
                                        </span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div> 