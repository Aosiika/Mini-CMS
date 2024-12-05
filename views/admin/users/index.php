<div class="content">
    <h1 class="title">Gestión de Usuarios</h1>

    <?php if (isset($_SESSION['flash'])): ?>
        <div class="notification is-<?php echo $_SESSION['flash']['type'] === 'error' ? 'danger' : 'success'; ?>">
            <button class="delete"></button>
            <?php 
            echo Security::preventXSS($_SESSION['flash']['message']);
            unset($_SESSION['flash']);
            ?>
        </div>
    <?php endif; ?>

    <div class="level">
        <div class="level-left">
            <div class="level-item">
                <a href="<?php echo BASE_URL; ?>admin/users/create" class="button is-primary">
                    <span class="icon">
                        <i class="fas fa-plus"></i>
                    </span>
                    <span>Nuevo Usuario</span>
                </a>
            </div>
            <div class="level-item">
                <div class="field">
                    <p class="control has-icons-left">
                        <input class="input" type="text" id="searchUsers" placeholder="Buscar usuarios...">
                        <span class="icon is-small is-left">
                            <i class="fas fa-search"></i>
                        </span>
                    </p>
                </div>
            </div>
            <div class="level-item">
                <div class="select">
                    <select id="resultsPerPage" onchange="changeResultsPerPage()">
                        <option value="10" <?php echo $perPage == 10 ? 'selected' : ''; ?>>10 por página</option>
                        <option value="20" <?php echo $perPage == 20 ? 'selected' : ''; ?>>20 por página</option>
                        <option value="50" <?php echo $perPage == 50 ? 'selected' : ''; ?>>50 por página</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="table-container">
        <table class="table is-fullwidth is-striped">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Último acceso</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="usersTableBody">
                <?php foreach ($users as $user): ?>
                    <tr data-user-id="<?php echo $user['id']; ?>">
                        <td><?php echo Security::preventXSS($user['username']); ?></td>
                        <td><?php echo Security::preventXSS($user['email']); ?></td>
                        <td>
                            <div class="select is-small">
                                <select class="role-select" data-user-id="<?php echo $user['id']; ?>">
                                    <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>Usuario</option>
                                    <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="select is-small">
                                <select class="status-select" data-user-id="<?php echo $user['id']; ?>">
                                    <option value="active" <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>Activo</option>
                                    <option value="inactive" <?php echo $user['status'] === 'inactive' ? 'selected' : ''; ?>>Inactivo</option>
                                    <option value="blocked" <?php echo $user['status'] === 'blocked' ? 'selected' : ''; ?>>Bloqueado</option>
                                </select>
                            </div>
                        </td>
                        <td><?php echo $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'Nunca'; ?></td>
                        <td>
                            <div class="buttons are-small">
                                <a href="<?php echo BASE_URL; ?>admin/users/edit/<?php echo $user['id']; ?>" 
                                   class="button is-info" title="Editar">
                                    <span class="icon">
                                        <i class="fas fa-edit"></i>
                                    </span>
                                </a>
                                <?php if ($user['id'] !== $_SESSION['user']['id']): ?>
                                    <button class="button is-danger delete-user" 
                                            data-user-id="<?php echo $user['id']; ?>"
                                            title="Eliminar">
                                        <span class="icon">
                                            <i class="fas fa-trash"></i>
                                        </span>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <nav class="pagination is-centered" role="navigation" aria-label="pagination">
        <a class="pagination-previous" href="?page=<?php echo max(1, $page - 1); ?>&per_page=<?php echo $perPage; ?>">Anterior</a>
        <a class="pagination-next" href="?page=<?php echo min(ceil($totalUsers / $perPage), $page + 1); ?>&per_page=<?php echo $perPage; ?>">Siguiente</a>
        <ul class="pagination-list">
            <?php
            $totalPages = ceil($totalUsers / $perPage);
            $range = 2; // Número de enlaces visibles a cada lado de la página actual

            for ($i = 1; $i <= $totalPages; $i++): 
                if ($i == 1 || $i == $totalPages || ($i >= $page - $range && $i <= $page + $range)):
            ?>
                <li>
                    <a class="pagination-link <?php echo $i == $page ? 'is-current' : ''; ?>" href="?page=<?php echo $i; ?>&per_page=<?php echo $perPage; ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
            <?php elseif ($i == $page - $range - 1 || $i == $page + $range + 1): ?>
                <li><span class="pagination-ellipsis">&hellip;</span></li>
            <?php endif; endfor; ?>
        </ul>
    </nav>
</div>

<script>
const BASE_URL = '<?php echo BASE_URL; ?>';
const CSRF_TOKEN = '<?php echo $_SESSION['csrf_token']; ?>';

document.addEventListener('DOMContentLoaded', function() {
    // Búsqueda en tiempo real
    const searchInput = document.getElementById('searchUsers');
    const tbody = document.getElementById('usersTableBody');
    const rows = tbody.getElementsByTagName('tr');

    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        
        Array.from(rows).forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

    // Cambio de rol
    document.querySelectorAll('.role-select').forEach(select => {
        select.addEventListener('change', async function() {
            const userId = this.dataset.userId;
            const newRole = this.value;
            
            try {
                const response = await fetch(`${BASE_URL}admin/users/update-role`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': CSRF_TOKEN
                    },
                    body: JSON.stringify({
                        user_id: userId,
                        role: newRole
                    })
                });

                const data = await response.json();
                if (data.success) {
                    showNotification('Rol actualizado correctamente', 'success');
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                showNotification(error.message, 'error');
                this.value = this.dataset.originalValue;
            }
        });
    });

    // Cambio de estado
    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', async function() {
            const userId = this.dataset.userId;
            const newStatus = this.value;
            
            try {
                const response = await fetch(`${BASE_URL}admin/users/update-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': CSRF_TOKEN
                    },
                    body: JSON.stringify({
                        user_id: userId,
                        status: newStatus
                    })
                });

                const data = await response.json();
                if (data.success) {
                    showNotification('Estado actualizado correctamente', 'success');
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                showNotification(error.message, 'error');
                this.value = this.dataset.originalValue;
            }
        });
    });

    // Eliminar usuario
    document.querySelectorAll('.delete-user').forEach(button => {
        button.addEventListener('click', async function() {
            if (!confirm('¿Estás seguro de que quieres eliminar este usuario?')) {
                return;
            }

            const userId = this.dataset.userId;
            
            try {
                const response = await fetch(`${BASE_URL}admin/users/delete`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': CSRF_TOKEN
                    },
                    body: JSON.stringify({
                        user_id: userId
                    })
                });

                const data = await response.json();
                if (data.success) {
                    this.closest('tr').remove();
                    showNotification('Usuario eliminado correctamente', 'success');
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                showNotification(error.message, 'error');
            }
        });
    });

    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification is-${type === 'error' ? 'danger' : 'success'}`;
        notification.innerHTML = `
            <button class="delete"></button>
            ${message}
        `;
        
        document.querySelector('.content').insertBefore(notification, document.querySelector('.level'));
        
        notification.querySelector('.delete').addEventListener('click', () => {
            notification.remove();
        });

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
});

function changeResultsPerPage() {
    const perPage = document.getElementById('resultsPerPage').value;
    window.location.href = `?page=1&per_page=${perPage}`;
}
</script> 