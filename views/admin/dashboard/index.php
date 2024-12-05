<h1 class="title">Dashboard</h1>

<div class="columns is-multiline">
    <div class="column is-4">
        <div class="box">
            <h2 class="title has-text-centered">POSTS TOTALES</h2>
            <p class="title has-text-centered"><?php echo $totalPosts; ?></p>
        </div>
    </div>
    <div class="column is-4">
        <div class="box">
            <h2 class="title has-text-centered">USUARIOS</h2>
            <p class="title has-text-centered"><?php echo $totalUsers; ?></p>
        </div>
    </div>
    <div class="column is-4">
        <div class="box">
            <h2 class="title has-text-centered">COMENTARIOS</h2>
            <p class="title has-text-centered"><?php echo $totalComments; ?></p>
        </div>
    </div>
</div>

<div class="columns">
    <div class="column is-8">
        <div class="box">
            <h2 class="title">Posts Recientes</h2>
            <table class="table is-fullwidth">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentPosts as $post): ?>
                        <tr>
                            <td><?php echo Security::preventXSS($post['title']); ?></td>
                            <td>
                                <span class="tag is-<?php echo ($post['status'] ?? 'draft') === 'published' ? 'success' : 'warning'; ?>">
                                    <?php echo ($post['status'] ?? 'draft') === 'published' ? 'Publicado' : 'Borrador'; ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="column is-4">
        <div class="box">
            <h2 class="title">Actividad Reciente</h2>
            <div class="content">
                <?php foreach ($recentActivity as $activity): ?>
                    <div class="box">
                        <p>
                            <strong><?php echo Security::preventXSS($activity['user']); ?></strong>
                            <?php echo Security::preventXSS($activity['action']); ?>
                            <br>
                            <small><?php echo date('d/m/Y H:i', strtotime($activity['created_at'])); ?></small>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div> 