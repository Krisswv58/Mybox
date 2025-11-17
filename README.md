# myBox - Sistema de Almacenamiento en la Nube

## Laboratorio #4 - Programación Web

Sistema de almacenamiento de archivos en la nube similar a DropBox, desarrollado en PHP con funcionalidades avanzadas de gestión de archivos y carpetas.

---

## Características Principales

###  Gestión de Archivos
- Subir archivos (límite 20 MB)
- Descargar archivos
- Borrar archivos con confirmación
- Visualización de PDF, JPG y PNG en navegador
- Descarga automática de otros formatos

###  Gestión de Carpetas
- Crear carpetas y subcarpetas
- Navegación jerárquica entre carpetas
- Borrar carpetas (recursivamente)
- Breadcrumb de navegación

###  Sistema de Compartir
- Compartir archivos entre usuarios
- Ver archivos compartidos por otros
- Control de acceso a archivos compartidos

###  Interfaz Mejorada
- Iconos visuales según tipo de archivo
- Tamaño mostrado en MB
- Ordenamiento (carpetas primero, luego archivos)
- Diseño responsive con Bootstrap

###  Seguridad
- Autenticación de usuarios
- Restricción de acceso por IP
- Protección contra directory traversal
- Límite de tamaño de archivos

---

## Requisitos del Sistema

- **Servidor Web:** Apache 2.4+
- **PHP:** 7.0 o superior
- **Base de Datos:** MySQL 5.6+
- **Espacio en Disco:** Según necesidades de almacenamiento

---

## Instalación

### 1. Clonar el Repositorio
```bash
git clone https://github.com/[tu-usuario]/myBox.git
cd myBox
```

### 2. Configurar Base de Datos
```sql
CREATE DATABASE mybox;
USE mybox;
-- Importar el esquema de la base de datos
SOURCE schema.sql;
```

### 3. Crear Directorio de Almacenamiento

**Windows:**
```cmd
mkdir d:\mybox
```

**Linux:**
```bash
sudo mkdir -p /var/www/mybox
sudo chmod 755 /var/www/mybox
sudo chown www-data:www-data /var/www/mybox
```

### 4. Configurar PHP
Editar `php.ini`:
```ini
upload_max_filesize = 20M
post_max_size = 20M
max_execution_time = 300
max_input_time = 300
```

### 5. Configurar IPs Permitidas
Editar `.htaccess` (líneas 43-45):
```apache
Require ip [IP_TERMINAL_1]
Require ip [IP_TERMINAL_2]
Require ip [IP_TERMINAL_3]
```

### 6. Reiniciar Apache
**Windows:**
```cmd
net stop Apache2.4
net start Apache2.4
```

**Linux:**
```bash
sudo service apache2 restart
```

---

## Estructura del Proyecto

```
myBox/
├── .htaccess                      # Configuración Apache
├── index.php                      # Página de login
├── registrar.php                  # Registro de usuarios
├── carpetas.php                   # Gestor principal de archivos
├── agrearchi.php                  # Subir archivos
├── abrArchi.php                   # Abrir/descargar archivos
├── compartir.php                  # Compartir archivos
├── ver_compartido.php             # Ver archivos compartidos
│
├── codigos/                       # Scripts de procesamiento
│   ├── crearcarpeta.php          # Crear carpetas
│   ├── borrar.php                # Borrar archivos/carpetas
│   ├── creadir.php               # Crear directorio usuario
│   └── salir.php                 # Cerrar sesión
│
├── partes/                        # Componentes reutilizables
│   ├── encabe.inc                # Encabezado HTML
│   ├── menu.inc                  # Menú de navegación
│   └── final.inc                 # Scripts finales
│
├── errores/                       # Páginas de error
│   ├── 400.php
│   ├── 403.php
│   ├── 404.php
│   └── 500.php
│
├── estilos/                       # Archivos CSS
├── imagenes/                      # Imágenes del sitio
│
└── documentacion/
    ├── README.md                  # Este archivo
    ├── LABORATORIO_4_DOCUMENTACION.md
    ├── INSTRUCCIONES_CONFIGURACION.txt
    └── usuarios_ejemplo.sql
```

---

## Uso del Sistema

### 1. Registro e Inicio de Sesión
1. Acceder a `index.php`
2. Crear cuenta en `registrar.php`
3. Iniciar sesión con sus credenciales

### 2. Subir Archivos
1. En `carpetas.php`, clic en  Subir Archivo"
2. Seleccionar archivo (máx. 20 MB)
3. Clic en "Cargar Archivo"

### 3. Crear Carpetas
1. En `carpetas.php`, clic en " Nueva Carpeta"
2. Ingresar nombre de la carpeta
3. Clic en "Crear Carpeta"

### 4. Navegar entre Carpetas
- Clic en el nombre de una carpeta para entrar
- Usar el breadcrumb para volver a carpetas superiores
- Clic en " Inicio" para volver a la raíz

### 5. Compartir Archivos
1. Clic en " Compartir"
2. Seleccionar elemento a compartir
3. Ingresar nombre del usuario destino
4. Clic en "Compartir"

### 6. Ver Archivos Compartidos
1. Ir a " Compartir"
2. Sección "Archivos Compartidos Conmigo"
3. Clic en "Ver/Descargar"

---

## Tipos de Archivo y Visualización

| Extensión | Comportamiento | Icono |
|-----------|----------------|-------|
| .pdf | Visualización en navegador 
| .jpg, .jpeg, .png | Visualización en navegador 
| .doc, .docx | Descarga automática 
| .xls, .xlsx | Descarga automática 
| .zip, .rar | Descarga automática 
| Otros | Descarga automática 

---

## Seguridad

### Autenticación
- Sistema de sesiones PHP
- Verificación en cada página protegida
- Cierre de sesión seguro

### Protección de Archivos
- Cada usuario solo accede a sus propios archivos
- Validación de permisos en archivos compartidos
- Prevención de directory traversal

### Restricción por IP
- Solo 3 IPs permitidas (configurables)
- Bloqueo automático de otras IPs
- Localhost permitido para desarrollo

### Límite de Tamaño
- Validación en cliente (JavaScript)
- Validación en servidor (PHP)
- Restricción en Apache (.htaccess)

---

## Configuración Avanzada

### Cambiar Directorio de Almacenamiento
Editar la variable `$base_ruta` en:
- `carpetas.php`
- `agrearchi.php`
- `abrArchi.php`
- `compartir.php`
- `codigos/crearcarpeta.php`
- `codigos/borrar.php`

### Modificar Límite de Tamaño
1. `.htaccess`: Cambiar `LimitRequestBody`
2. `agrearchi.php`: Modificar `$tamano_maximo`
3. `php.ini`: Ajustar `upload_max_filesize` y `post_max_size`

### Agregar Nuevos Tipos de Archivo para Visualización
Editar en `abrArchi.php` y `ver_compartido.php`:
```php
$extensiones_visualizar = array('pdf', 'jpg', 'jpeg', 'png', 'svg', 'gif');
```

---

## Solución de Problemas

### No se pueden subir archivos
- Verificar permisos del directorio `d:\mybox` (755)
- Revisar configuración de `php.ini`
- Comprobar espacio en disco

### Error 403 al acceder
- Verificar configuración de IPs en `.htaccess`
- Asegurar que su IP está en la lista permitida
- Revisar que Apache tenga `AllowOverride All`

### PDF no se visualiza en navegador
- Verificar header `Content-Disposition: inline`
- Comprobar tipo MIME del archivo
- Probar en diferente navegador

### Archivos compartidos no aparecen
- Verificar que existe el archivo `compartidos.json`
- Comprobar permisos de escritura en `d:\mybox`
- Revisar que el usuario destino existe

---

## Desarrollo y Contribuciones

### Stack Tecnológico
- **Backend:** PHP 7+
- **Frontend:** HTML5, CSS3, JavaScript
- **Framework CSS:** Bootstrap 3
- **Base de Datos:** MySQL
- **Servidor:** Apache

### Próximas Mejoras
- [ ] Sistema de cuotas de almacenamiento por usuario
- [ ] Vista previa de imágenes en miniatura
- [ ] Editor de texto online
- [ ] Papelera de reciclaje
- [ ] Historial de versiones
- [ ] Búsqueda de archivos
- [ ] Etiquetas y categorías

---




## Referencias

- [PHP Manual](https://www.php.net/manual/es/)
- [Apache HTTP Server Documentation](https://httpd.apache.org/docs/)
- [Bootstrap 3 Documentation](https://getbootstrap.com/docs/3.4/)
- [MySQL Documentation](https://dev.mysql.com/doc/)







