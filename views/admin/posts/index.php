<div class="content">
    <h1 class="title">Gestión de Posts</h1>
    
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="notification is-<?php echo $_SESSION['flash']['type'] === 'error' ? 'danger' : 'success'; ?>">
            <button class="delete"></button>
            <?php 
            echo Security::preventXSS($_SESSION['flash']['message']);
            unset($_SESSION['flash']);
            ?>
        </div>
    <?php endif; ?>

    <a href="<?php echo BASE_URL; ?>admin/posts/create" class="button is-primary mb-4">
        <span class="icon">
            <i class="fas fa-plus"></i>
        </span>
        <span>Nuevo Post</span>
    </a>

    <div class="table-container">
        <table class="table is-fullwidth is-striped">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Menú</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td><?php echo Security::preventXSS($post['title']); ?></td>
                            <td>
                                <?php 
                                if (!empty($post['menu_id']) && !empty($post['menu_name'])) {
                                    echo Security::preventXSS($post['menu_name']);
                                } else {
                                    echo '<span class="tag is-light">Sin menú</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <span class="tag is-<?php echo ($post['status'] ?? 'draft') === 'published' ? 'success' : 'warning'; ?>">
                                    <?php echo ($post['status'] ?? 'draft') === 'published' ? 'Publicado' : 'Borrador'; ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?></td>
                            <td>
                                <div class="buttons are-small">
                                    <a href="<?php echo BASE_URL; ?>admin/posts/edit/<?php echo $post['id']; ?>" 
                                       class="button is-info" title="Editar">
                                        <span class="icon">
                                            <i class="fas fa-edit"></i>
                                        </span>
                                    </a>
                                    <form method="POST" action="<?php echo BASE_URL; ?>admin/posts/delete" 
                                          class="is-inline" onsubmit="return confirm('¿Estás seguro de eliminar este post?');">
                                        <input type="hidden" name="csrf_token" value="<?php echo Security::preventXSS($_SESSION['csrf_token']); ?>">
                                        <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
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
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="has-text-centered">No hay posts disponibles</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div> 