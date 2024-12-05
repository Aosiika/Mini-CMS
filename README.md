# Mini-CMS

Este proyecto es una aplicación de gestión de usuarios desarrollada en PHP, utilizando Bulma CSS para el diseño de la interfaz. La aplicación incluye características de seguridad para proteger contra ataques comunes.

## Características

- **Gestión de Usuarios:** Crear, editar, eliminar y listar usuarios.
- **Roles y Permisos:** Asignación de roles de usuario y administración.
- **Seguridad:** Protección contra ataques CSRF y XSS.
- **Interfaz de Usuario:** Diseño responsivo utilizando Bulma CSS.

## Requisitos

- PHP 7.4 o superior
- Servidor web (Apache, Nginx, etc.)
- Base de datos MySQL

## Instalación

1. Clona el repositorio:

   ```bash
   git clone https://github.com/tu-usuario/tu-repositorio.git
   ```

2. Configura la base de datos:

   - Crea una base de datos en MySQL.
   - Importa el archivo `database.sql` para crear las tablas necesarias.

3. Configura el archivo de conexión a la base de datos:

   - Edita el archivo `config/database.php` con tus credenciales de base de datos.

4. Inicia el servidor:

   - Puedes usar el servidor embebido de PHP para pruebas locales:

     ```bash
     php -S localhost:8000
     ```

5. Accede a la aplicación:

   - Abre tu navegador y ve a `http://localhost:8000`.

## Seguridad

El proyecto incluye un archivo de seguridad que maneja la sanitización de entradas y la generación de tokens CSRF para proteger contra ataques comunes.

## Estilos

La aplicación utiliza Bulma CSS para un diseño limpio y moderno. Puedes personalizar los estilos editando los archivos CSS en la carpeta `assets/css`.

## Contribuciones

Si deseas contribuir al proyecto, por favor sigue estos pasos:

1. Haz un fork del repositorio.
2. Crea una rama para tu nueva funcionalidad (`git checkout -b feature/nueva-funcionalidad`).
3. Realiza tus cambios y haz commit (`git commit -m 'Añadir nueva funcionalidad'`).
4. Sube tus cambios a tu fork (`git push origin feature/nueva-funcionalidad`).
5. Abre un Pull Request en el repositorio original.

## Licencia

Este proyecto está licenciado bajo la Licencia MIT. Consulta el archivo `LICENSE` para más detalles.
