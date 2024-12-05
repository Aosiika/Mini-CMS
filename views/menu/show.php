<div class="content">
    <h1 class="title"><?php echo Security::preventXSS($menu['name']); ?></h1>

    <?php if (!empty($posts)): ?>
        <div class="columns is-multiline">
            <?php foreach ($posts as $post): ?>
                <div class="column is-4">
                    <div class="card">
                        <?php if (!empty($post['featured_image'])): ?>
                            <div class="card-image">
                                <figure class="image is-16by9">
                                    <img src="<?php echo BASE_URL; ?>uploads/<?php echo Security::preventXSS($post['featured_image']); ?>" 
                                         alt="<?php echo Security::preventXSS($post['title']); ?>">
                                </figure>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-content">
                            <h2 class="title is-4"><?php echo Security::preventXSS($post['title']); ?></h2>
                            
                            <?php if (!empty($post['excerpt'])): ?>
                                <div class="content">
                                    <?php echo Security::preventXSS($post['excerpt']); ?>
                                </div>
                            <?php endif; ?>

                            <div class="has-text-grey is-size-7 mb-3">
                                Publicado: <?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?>
                            </div>

                            <a href="<?php echo BASE_URL; ?>post/<?php echo Security::preventXSS($post['slug']); ?>" 
                               class="button is-link">
                                Leer más
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="notification is-info">
            No hay posts disponibles en esta sección.
        </div>
    <?php endif; ?>
</div> 