<div class="content">
    <nav class="breadcrumb" aria-label="breadcrumbs">
        <ul>
            <li><a href="/">Inicio</a></li>
            <?php if (!empty($post['menu_name'])): ?>
                <li><a href="/<?php echo Security::preventXSS($post['menu_slug']); ?>">
                    <?php echo Security::preventXSS($post['menu_name']); ?>
                </a></li>
            <?php endif; ?>
            <li class="is-active"><a href="#" aria-current="page">
                <?php echo Security::preventXSS($post['title']); ?>
            </a></li>
        </ul>
    </nav>

    <article class="box">
        <h1 class="title is-2"><?php echo Security::preventXSS($post['title']); ?></h1>
        
        <div class="level">
            <div class="level-left">
                <div class="level-item">
                    <span class="icon">
                        <i class="fas fa-calendar"></i>
                    </span>
                    &nbsp;
                    <?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?>
                </div>
            </div>
        </div>

        <?php if (!empty($post['featured_image'])): ?>
            <figure class="image is-16by9 mb-5">
                <img src="<?php echo BASE_URL; ?>uploads/<?php echo Security::preventXSS($post['featured_image']); ?>" 
                     alt="<?php echo Security::preventXSS($post['title']); ?>">
            </figure>
        <?php endif; ?>

        <?php if (!empty($post['excerpt'])): ?>
            <div class="content is-medium has-text-grey">
                <?php echo Security::preventXSS($post['excerpt']); ?>
            </div>
        <?php endif; ?>

        <div class="content is-medium post-content">
            <?php echo $post['content']; ?>
        </div>
    </article>
</div> 