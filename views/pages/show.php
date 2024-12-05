<div class="content">
    <?php if (isset($page) && $page): ?>
        <h1 class="title"><?php echo Security::preventXSS($page['title']); ?></h1>
        
        <?php if (isset($page['featured_image'])): ?>
            <div class="image is-3by1 mb-5">
                <img src="<?php echo BASE_URL; ?>/uploads/<?php echo Security::preventXSS($page['featured_image']); ?>" 
                     alt="<?php echo Security::preventXSS($page['title']); ?>">
            </div>
        <?php endif; ?>

        <div class="content">
            <?php echo $page['content']; // No usamos preventXSS aquí porque el contenido viene del editor ?>
        </div>

        <?php if (isset($page['menu_name'])): ?>
            <div class="tags">
                <span class="tag is-info">
                    <?php echo Security::preventXSS($page['menu_name']); ?>
                </span>
            </div>
        <?php endif; ?>

        <div class="has-text-grey is-size-7">
            Publicado: <?php echo date('d/m/Y H:i', strtotime($page['created_at'])); ?>
        </div>
    <?php else: ?>
        <div class="notification is-warning">
            <p>La página que buscas no existe o no está disponible.</p>
        </div>
    <?php endif; ?>
</div> 