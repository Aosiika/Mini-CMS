<div class="content">
    <h1 class="title"><?php echo isset($post) ? 'Editar' : 'Crear'; ?> Post</h1>
    
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="notification is-<?php echo $_SESSION['flash']['type'] === 'error' ? 'danger' : 'success'; ?>">
            <button class="delete"></button>
            <?php 
            echo Security::preventXSS($_SESSION['flash']['message'] ?? '');
            unset($_SESSION['flash']);
            ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo BASE_URL; ?>admin/posts/<?php echo isset($post) ? 'edit/'.$post['id'] : 'create'; ?>" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo Security::preventXSS($_SESSION['csrf_token'] ?? ''); ?>">
        
        <div class="field">
            <label class="label">Menú</label>
            <div class="control">
                <div class="select">
                    <select name="menu_id">
                        <option value="">Ninguno</option>
                        <?php foreach ($menus as $menu): ?>
                            <option value="<?php echo $menu['id']; ?>"
                                <?php echo (isset($post) && $post['menu_id'] == $menu['id']) ? 'selected' : ''; ?>>
                                <?php echo Security::preventXSS($menu['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="field">
            <label class="label">Título</label>
            <div class="control">
                <input class="input" type="text" name="title" required
                       value="<?php echo isset($post) ? Security::preventXSS($post['title']) : ''; ?>"
                       placeholder="Título del post">
            </div>
        </div>

        <div class="field">
            <label class="label">Contenido</label>
            <div class="control">
                <textarea id="editor" name="content" style="min-height: 400px;">
                    <?php echo isset($post) ? $post['content'] : ''; ?>
                </textarea>
            </div>
        </div>

        <div class="field">
            <label class="label">Extracto</label>
            <div class="control">
                <textarea class="textarea" name="excerpt" rows="3" placeholder="Breve descripción del post">
                    <?php echo isset($post) ? Security::preventXSS($post['excerpt']) : ''; ?>
                </textarea>
            </div>
        </div>

        <div class="field">
            <label class="label">Imagen Destacada</label>
            <?php if (isset($post) && !empty($post['featured_image'])): ?>
                <div class="mb-4">
                    <img src="<?php echo BASE_URL . '/uploads/' . $post['featured_image']; ?>" 
                         alt="Imagen destacada actual" 
                         style="max-width: 300px; height: auto;">
                    <button type="button" class="button is-small is-danger mt-2" id="remove-featured-image">
                        <span class="icon">
                            <i class="fas fa-trash"></i>
                        </span>
                        <span>Eliminar imagen</span>
                    </button>
                    <input type="hidden" name="remove_featured_image" id="remove-featured-image-input" value="0">
                </div>
            <?php endif; ?>
            <div class="control">
                <div class="file has-name">
                    <label class="file-label">
                        <input class="file-input" type="file" name="featured_image" accept="image/*">
                        <span class="file-cta">
                            <span class="file-icon">
                                <i class="fas fa-upload"></i>
                            </span>
                            <span class="file-label">
                                Elegir archivo...
                            </span>
                        </span>
                        <span class="file-name">
                            No se ha seleccionado archivo
                        </span>
                    </label>
                </div>
            </div>
        </div>

        <div class="field">
            <label class="label">Estado</label>
            <div class="control">
                <div class="select">
                    <select name="status">
                        <option value="draft" <?php echo (isset($post) && $post['status'] == 'draft') ? 'selected' : ''; ?>>
                            Borrador
                        </option>
                        <option value="published" <?php echo (isset($post) && $post['status'] == 'published') ? 'selected' : ''; ?>>
                            Publicado
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <div class="field is-grouped">
            <div class="control">
                <button type="submit" class="button is-primary">Guardar</button>
            </div>
            <div class="control">
                <a href="<?php echo BASE_URL; ?>?controller=post&action=index" class="button is-link is-light">
                    Cancelar
                </a>
            </div>
        </div>
    </form>
</div>

<script>
ClassicEditor
    .create(document.querySelector('#editor'), {
        toolbar: {
            items: [
                'undo', 'redo',
                '|', 'heading',
                '|', 'bold', 'italic',
                '|', 'link', 'uploadImage', 'blockQuote', 'insertTable', 'mediaEmbed',
                '|', 'bulletedList', 'numberedList', 'outdent', 'indent'
            ]
        },
        language: 'es',
        image: {
            toolbar: [
                'imageTextAlternative',
                'imageStyle:inline',
                'imageStyle:block',
                'imageStyle:side'
            ],
            upload: {
                types: ['jpeg', 'png', 'gif', 'jpg'],
                url: '<?php echo BASE_URL; ?>?controller=post&action=uploadImage'
            }
        },
        table: {
            contentToolbar: [
                'tableColumn',
                'tableRow',
                'mergeTableCells'
            ]
        },
        height: '500px',
        // Configurar el estilo del editor
        style: {
            'min-height': '500px',
            'max-height': '800px',
            'overflow-y': 'auto'
        }
    })
    .then(editor => {
        console.log('Editor inicializado:', editor);
    })
    .catch(error => {
        console.error('Error al inicializar el editor:', error);
    });

// Código para manejar la imagen destacada
document.addEventListener('DOMContentLoaded', () => {
    const fileInput = document.querySelector('.file-input');
    const fileName = document.querySelector('.file-name');
    const removeButton = document.getElementById('remove-featured-image');
    const removeInput = document.getElementById('remove-featured-image-input');
    
    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            fileName.textContent = fileInput.files[0].name;
        } else {
            fileName.textContent = 'No se ha seleccionado archivo';
        }
    });

    if (removeButton) {
        removeButton.addEventListener('click', () => {
            const imageContainer = removeButton.parentElement;
            imageContainer.style.display = 'none';
            removeInput.value = '1';
        });
    }
});
</script> 