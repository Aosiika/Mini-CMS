<div class="content">
    <h1 class="title">Dashboard</h1>

    <!-- Estadísticas -->
    <div class="columns is-multiline">
        <div class="column is-3">
            <div class="box has-text-centered">
                <p class="heading">POSTS TOTALES</p>
                <p class="title"><?php echo $stats['posts_total']; ?></p>
            </div>
        </div>
        <div class="column is-3">
            <div class="box has-text-centered">
                <p class="heading">PÁGINAS</p>
                <p class="title"><?php echo $stats['pages_total']; ?></p>
            </div>
        </div>
        <div class="column is-3">
            <div class="box has-text-centered">
                <p class="heading">USUARIOS</p>
                <p class="title"><?php echo $stats['users_total']; ?></p>
            </div>
        </div>
        <div class="column is-3">
            <div class="box has-text-centered">
                <p class="heading">COMENTARIOS</p>
                <p class="title"><?php echo $stats['comments_total']; ?></p>
            </div>
        </div>
    </div>

    <div class="columns">
        <!-- Posts Recientes -->
        <div class="column is-8">
            <div class="box">
                <h2 class="title is-4">Posts Recientes</h2>
                <?php if (!empty($recent_posts)): ?>
                    <div class="table-container">
                        <table class="table is-fullwidth">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_posts as $post): ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo BASE_URL; ?>?controller=post&action=edit&id=<?php echo $post['id']; ?>">
                                                <?php echo Security::preventXSS($post['title']); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="tag <?php echo $post['status'] === 'published' ? 'is-success' : 'is-warning'; ?>">
                                                <?php echo $post['status'] === 'published' ? 'Publicado' : 'Borrador'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>No hay posts recientes</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Actividad Reciente -->
        <div class="column is-4">
            <div class="box">
                <h2 class="title is-4">Actividad Reciente</h2>
                <?php if (!empty($recent_activity)): ?>
                    <div class="content">
                        <?php foreach ($recent_activity as $activity): ?>
                            <div class="notification is-light">
                                <p>
                                    <span class="icon">
                                        <i class="fas <?php echo $activity['type'] === 'post' ? 'fa-file-alt' : 'fa-user'; ?>"></i>
                                    </span>
                                    <strong><?php echo Security::preventXSS($activity['title']); ?></strong>
                                    <br>
                                    <small class="has-text-grey">
                                        <?php echo $activity['type'] === 'post' ? 'Post creado' : 'Usuario registrado'; ?>
                                        el <?php echo date('d/m/Y H:i', strtotime($activity['date'])); ?>
                                    </small>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No hay actividad reciente</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div> 