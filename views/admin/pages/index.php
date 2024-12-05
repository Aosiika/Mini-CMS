<div class="content">
    <h1 class="title">Gestión de Páginas</h1>
    
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="notification is-<?php echo $_SESSION['flash']['type'] === 'error' ? 'danger' : 'success'; ?>">
            <button class="delete"></button>
            <?php 
            echo Security::preventXSS($_SESSION['flash']['message']);
            unset($_SESSION['flash']);
            ?>
        </div>
    <?php endif; ?>

    <div class="table-container">
        <table class="table is-fullwidth is-striped is-hoverable">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Slug</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pages as $page): ?>
                    <tr>
                        <td><?php echo Security::preventXSS($page['title']); ?></td>
                        <td><?php echo Security::preventXSS($page['slug']); ?></td>
                        <td>
                            <span class="tag <?php echo $page['is_active'] ? 'is-success' : 'is-warning'; ?>">
                                <?php echo $page['is_active'] ? 'Activa' : 'Inactiva'; ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($page['created_at'])); ?></td>
                        <td>
                            <div class="buttons are-small">
                                <a href="<?php echo BASE_URL; ?>?controller=page&action=edit&id=<?php echo $page['id']; ?>" 
                                   class="button is-info" title="Editar">
                                    <span class="icon">
                                        <i class="fas fa-edit"></i>
                                    </span>
                                </a>
                                <form method="POST" action="<?php echo BASE_URL; ?>?controller=page&action=delete" 
                                      class="is-inline" onsubmit="return confirm('¿Estás seguro?');">
                                    <input type="hidden" name="csrf_token" value="<?php echo Security::preventXSS($_SESSION['csrf_token']); ?>">
                                    <input type="hidden" name="id" value="<?php echo $page['id']; ?>">
                                    <button type="submit" class="button is-danger" title="Eliminar">
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