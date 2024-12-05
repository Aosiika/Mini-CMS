<div class="section">
    <div class="container">
        <h1 class="title"><?php echo Security::preventXSS($menu['name']); ?></h1>

        <?php if (!empty($posts)): ?>
            <div class="columns is-multiline">
                <?php foreach ($posts as $post): ?>
                    <div class="column is-4">
                        <div class="card">
                            <?php if (!empty($post['featured_image'])): ?>
                                <div class="card-image">
                                    <figure class="image is-3by2">
                                        <img src="/uploads/<?php echo Security::preventXSS($post['featured_image']); ?>" 
                                             alt="<?php echo Security::preventXSS($post['title']); ?>">
                                    </figure>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-content">
                                <div class="media">
                                    <div class="media-content">
                                        <p class="title is-4">
                                            <a href="/post/<?php echo Security::preventXSS($post['slug']); ?>" class="has-text-dark">
                                                <?php echo Security::preventXSS($post['title']); ?>
                                            </a>
                                        </p>
                                    </div>
                                </div>

                                <?php if (!empty($post['excerpt'])): ?>
                                    <div class="content">
                                        <?php echo Security::preventXSS($post['excerpt']); ?>
                                    </div>
                                <?php endif; ?>

                                <div class="content">
                                    <time datetime="<?php echo $post['created_at']; ?>">
                                        <small class="has-text-grey">
                                            Publicado: <?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?>
                                        </small>
                                    </time>
                                </div>
                            </div>
                            
                            <footer class="card-footer">
                                <a href="/post/<?php echo Security::preventXSS($post['slug']); ?>" class="card-footer-item">
                                    Leer más
                                </a>
                            </footer>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="notification is-info">
                <p>No hay posts disponibles en esta sección.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (isset($_SESSION['flash'])): ?>
    <div class="notification is-<?php echo $_SESSION['flash']['type'] === 'error' ? 'danger' : 'success'; ?>">
        <button class="delete"></button>
        <?php 
        echo Security::preventXSS($_SESSION['flash']['message']);
        unset($_SESSION['flash']);
        ?>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Delete button functionality
    const deleteButtons = document.querySelectorAll('.notification .delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', () => {
            button.parentElement.remove();
        });
    });
});
</script> 