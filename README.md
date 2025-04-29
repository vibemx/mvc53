# MVC53 - Plantilla MVC en PHP 5.3

Este proyecto es una plantilla basada en el patrón de diseño Modelo-Vista-Controlador (MVC) para PHP 5.3. La estructura facilita la organización del código y promueve la separación de responsabilidades.

## Estructura del Proyecto

```
MVC53/ 
├── app/
│   ├── controllers/       # Controladores principales
│   |   └── api/           # Controladores dedicados a la API
│   ├── helpers/           # Funciones de ayuda reutilizables
│   ├── models/            # Modelos que gestionan la lógica de negocio y acceso a datos
│   ├── views/             # Vistas para la representación visual de los datos
├── config/                # Archivos de configuración (base de datos, entorno, etc.)
├── core/                  # Clases base y núcleo del framework
├── public/                # Carpeta pública accesible desde el navegador
│   ├── assets/            # Recursos estáticos (CSS, JS, imágenes)
│   └── index.php          # Punto de entrada principal
├── routes/                # Rutas del sistema
└── README.md              # Documentación del proyecto
```

## Requisitos
- **Servidor Web:** Apache o NGINX.
- **PHP:** Versión 5.3.
- **Base de Datos:** SQL Server 2008.
- **Extensiones PHP:** dblib, mbstring (para conexión con SQL Server en entornos Linux).
- **Extensión para el htaccess**

## Configuración
Debes definir las siguientes variables en el archivo `config`:
```php
define('NAME_SYSTEM', 'Template MVC PHP 5.3');
define('DB_HOST', 'localhost');
define('DB_USER', 'user');
define('DB_PASS', 'pasword');
define('DB_NAME', 'database');
define('URL_BASE', 'http://localhost/mvc_53/');
```

## Instalación
1. Clonar el repositorio:
    ```bash
    git clone https://github.com/victorbeltranmx/MVC53.git
    ```
2. Configurar la base de datos en el archivo `config/config.php`.
3. Asegurarse de que el servidor web apunte a la carpeta `public/`.
4. Verificar permisos de escritura si es necesario.
5. En el archivo `public/index.php`, configurar la ruta a la que se redireccionará cuando inicie el sitio.
6. En el archivo `.htaccess`, actualizar la línea de `RewriteBase` con el nombre de la carpeta actual del proyecto.  
   Por ejemplo, si originalmente aparece como:
     ```htaccess
    RewriteBase /mvc_53/
    ```
    Y tu proyecto está en una carpeta llamada `miproyecto`, deberías cambiarla por:
     ```htaccess
    RewriteBase /miproyecto/
    ```

##Definición de Rutas
Las rutas del sistema se definen en el archivo routes/routes.php.
La sintaxis general es:
```php
Copiar
Editar
Route::metodo('uri', 'Controlador@metodo', 'tipo');
```
-El campo tipo puede ser vacío o 'api'.
-Si la URI contiene {}, se interpreta que recibirá un parámetro dinámico.
-Las rutas se consideran como API si comienzan con api/ o si se especifica el tipo como 'api'.

## Uso
- Los controladores manejan la lógica de la aplicación.
- Los modelos gestionan la interacción con la base de datos.
- Las vistas presentan la información al usuario.

### Tecnologías Utilizadas
- Bootstrap v5.3.0: Se utiliza por defecto en la plantilla. Si se desea modificar, se debe hacer en `views/template/footer.php` para los JavaScripts y en `views/template/header.php` para los estilos.
- Font Awesome Free 6.7.2
- sweetalert2 v11.15.3

## Contribución
1. Hacer un fork del repositorio.
2. Crear una rama para tu nueva función o corrección:
    ```bash
    git checkout -b feature/nueva-funcionalidad
    ```
3. Enviar un Pull Request.

## Licencia
Este proyecto se encuentra bajo la licencia MIT.

---

_Hecho con amor por Victor Beltrán._ 💛

