<div class="content">
    <h1 class="title"><?php echo isset($user) ? 'Editar Usuario' : 'Crear Usuario'; ?></h1>

    <?php if (isset($_SESSION['flash'])): ?>
        <div class="notification is-<?php echo $_SESSION['flash']['type'] === 'error' ? 'danger' : 'success'; ?>">
            <button class="delete"></button>
            <?php 
            echo Security::preventXSS($_SESSION['flash']['message']);
            unset($_SESSION['flash']);
            ?>
        </div>
    <?php endif; ?>

    <div class="box">
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="field">
                <label class="label">Nombre de Usuario</label>
                <div class="control has-icons-left">
                    <input class="input" type="text" name="username" 
                           value="<?php echo isset($user) ? Security::preventXSS($user['username']) : ''; ?>" required>
                    <span class="icon is-small is-left">
                        <i class="fas fa-user"></i>
                    </span>
                </div>
            </div>

            <div class="field">
                <label class="label">Email</label>
                <div class="control has-icons-left">
                    <input class="input" type="email" name="email" 
                           value="<?php echo isset($user) ? Security::preventXSS($user['email']) : ''; ?>" required>
                    <span class="icon is-small is-left">
                        <i class="fas fa-envelope"></i>
                    </span>
                </div>
            </div>

            <div class="field">
                <label class="label">Contraseña <?php echo isset($user) ? '(dejar en blanco para mantener la actual)' : ''; ?></label>
                <div class="control has-icons-left">
                    <input class="input" type="password" name="password" 
                           <?php echo !isset($user) ? 'required' : ''; ?>>
                    <span class="icon is-small is-left">
                        <i class="fas fa-lock"></i>
                    </span>
                </div>
            </div>

            <div class="field">
                <label class="label">Rol</label>
                <div class="control">
                    <div class="select">
                        <select name="role">
                            <option value="user" <?php echo (isset($user) && $user['role'] === 'user') ? 'selected' : ''; ?>>Usuario</option>
                            <option value="admin" <?php echo (isset($user) && $user['role'] === 'admin') ? 'selected' : ''; ?>>Administrador</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="field">
                <label class="label">Estado</label>
                <div class="control">
                    <div class="select">
                        <select name="status">
                            <option value="active" <?php echo (isset($user) && $user['status'] === 'active') ? 'selected' : ''; ?>>Activo</option>
                            <option value="inactive" <?php echo (isset($user) && $user['status'] === 'inactive') ? 'selected' : ''; ?>>Inactivo</option>
                            <option value="blocked" <?php echo (isset($user) && $user['status'] === 'blocked') ? 'selected' : ''; ?>>Bloqueado</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="field is-grouped">
                <div class="control">
                    <button type="submit" class="button is-primary">
                        <?php echo isset($user) ? 'Actualizar' : 'Crear'; ?>
                    </button>
                </div>
                <div class="control">
                    <a href="<?php echo url('admin/users'); ?>" class="button is-light">
                        Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Manejo de notificaciones
    const deleteButtons = document.querySelectorAll('.notification .delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', () => {
            button.parentElement.remove();
        });
    });
});
</script> 