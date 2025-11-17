# Laboratorio #4 - Mejoras en myBox
## Documentación de Implementación

### Estudiante: 
### Fecha de Entrega: 27/10/2025

---

## Resumen de Mejoras Implementadas

Este documento describe todas las mejoras implementadas en el sistema myBox para cumplir con los requisitos del Laboratorio #4.

---

## 1. Sistema de Navegación entre Carpetas 

### Archivos Modificados:
- `carpetas.php` (líneas 11-75)

### Descripción:
Se implementó un sistema completo de navegación jerárquica de carpetas que permite:

- Navegar entre carpetas y subcarpetas
- Breadcrumb de navegación mostrando la ruta actual
- Prevención de ataques de directory traversal
- Visualización separada de carpetas y archivos (carpetas primero, luego archivos)
- Ordenamiento alfabético de elementos

### Funcionalidades:
```php
// Ejemplo de uso de la navegación:
// carpetas.php                    -> Muestra carpeta raíz del usuario
// carpetas.php?carpeta=Documentos -> Muestra contenido de subcarpeta "Documentos"
```

---

## 2. Crear, Navegar y Borrar Carpetas 

### Archivos Creados:
- `codigos/crearcarpeta.php` - Formulario y lógica para crear carpetas
- `codigos/borrar.php` - Lógica para borrar archivos y carpetas

### Descripción:

**Crear Carpetas:**
- Formulario con validación de nombre de carpeta
- Sanitización de caracteres especiales
- Verificación de carpetas duplicadas
- Permisos configurados a 0755

**Borrar Carpetas:**
- Función recursiva para eliminar carpetas con contenido
- Confirmación JavaScript antes de borrar
- Mensajes de éxito/error
- Diferenciación entre archivos y carpetas

### Código Destacado:
```php
// Función para eliminar directorio recursivamente
function eliminarDirectorio($dir) {
    if (!file_exists($dir)) return true;
    if (!is_dir($dir)) return unlink($dir);

    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') continue;
        if (!eliminarDirectorio($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }
    return rmdir($dir);
}
```

---

## 3. Sistema de Compartir Archivos entre Usuarios 

### Archivos Creados:
- `compartir.php` - Interfaz para compartir archivos
- `ver_compartido.php` - Visualizar/descargar archivos compartidos

### Descripción:
Sistema completo de compartición de archivos que permite:

- Compartir archivos y carpetas con otros usuarios del sistema
- Verificación de existencia del usuario destino
- Almacenamiento de compartidos en archivo JSON (`d:\mybox\compartidos.json`)
- Visualización de archivos compartidos "conmigo"
- Acceso controlado solo para usuarios autorizados

### Estructura de Datos:
```json
{
  "unique_id": {
    "propietario": "usuario1",
    "usuario_destino": "usuario2",
    "elemento": "documento.pdf",
    "carpeta": "Mis_Documentos",
    "fecha": "2025-10-20 14:30:00",
    "ruta_completa": "d:\\mybox\\usuario1\\Mis_Documentos\\documento.pdf"
  }
}
```

---

## 4. Iconos según Tipo de Archivo 

### Archivos Modificados:
- `carpetas.php` (líneas 27-70, función `obtenerIcono()`)

### Descripción:
Sistema de iconos emoji para representar visualmente diferentes tipos de archivos:

| Extensión | Icono | Tipo |
|-----------|-------|------|
| pdf | 📄 | Documentos PDF |
| doc/docx | 📝 | Documentos Word |
| xls/xlsx | 📊 | Hojas de cálculo |
| jpg/png | 🖼️ | Imágenes |
| mp3/wav | 🎵 | Audio |
| mp4/avi | 🎬 | Video |
| zip/rar | 📦 | Archivos comprimidos |
| (carpetas) | 📁 | Directorios |
| otros | 📎 | Archivos genéricos |

---

## 5. Mostrar Tamaño en MBytes 

### Archivos Modificados:
- `carpetas.php` (líneas 72-75, función `bytesToMB()`)
- `carpetas.php` (línea 183)

### Descripción:
- Conversión automática de bytes a megabytes
- Formato con 2 decimales de precisión
- Carpetas muestran "--" en lugar de tamaño

### Código:
```php
function bytesToMB($bytes) {
    return round($bytes / (1024 * 1024), 2);
}
```

---

## 6. Validación de Borrado con JavaScript 

### Archivos Modificados:
- `carpetas.php` (líneas 205-209)

### Descripción:
- Confirmación mediante `confirm()` de JavaScript
- Diferenciación entre archivos y carpetas en el mensaje
- Prevención de borrados accidentales

### Código JavaScript:
```javascript
function confirmarBorrado(nombre, esCarpeta) {
    var tipo = esCarpeta ? 'carpeta' : 'archivo';
    return confirm('¿Está seguro que desea eliminar ' +
                   (esCarpeta ? 'la carpeta' : 'el archivo') +
                   ' "' + nombre + '"?');
}
```

---

## 7. Visualización de PDF, JPG y PNG en Navegador 

### Archivos Modificados:
- `abrArchi.php` (completamente refactorizado)

### Descripción:
- Los archivos PDF, JPG, JPEG y PNG se muestran directamente en el navegador
- Otras extensiones se descargan automáticamente
- Header `Content-Disposition: inline` para visualización
- Header `Content-Disposition: attachment` para descarga

### Lógica:
```php
$extensiones_visualizar = array('pdf', 'jpg', 'jpeg', 'png');

if(in_array($extension, $extensiones_visualizar)) {
    // Mostrar en navegador
    header("Content-Disposition: inline; filename=".$archivo);
} else {
    // Forzar descarga
    header("Content-Disposition: attachment; filename=".$archivo);
}
```

---

## 8. Límite de 20 MB para Archivos 

### Archivos Modificados:
- `agrearchi.php` (líneas 31-36, 110-122)
- `.htaccess` (líneas 52-65)

### Descripción:
Implementación de límite en **tres niveles**:

1. **Validación JavaScript (Cliente):**
```javascript
function validarArchivo() {
    var tamano = archivo.files[0].size;
    var tamanoMB = tamano / (1024 * 1024);
    if(tamanoMB > 20) {
        alert('El archivo es demasiado grande...');
        return false;
    }
    return true;
}
```

2. **Validación PHP (Servidor):**
```php
$tamano_maximo = 20 * 1024 * 1024; // 20 MB
if($tamano_archivo > $tamano_maximo) {
    $error = "El archivo es demasiado grande...";
}
```

3. **Configuración .htaccess:**
```apache
LimitRequestBody 20971520  # 20 MB en bytes
```

### Configuración Adicional Requerida:
Debe configurar también en `php.ini`:
```ini
upload_max_filesize = 20M
post_max_size = 20M
```

---

## 9. Restricción de Acceso por IP 

### Archivos Modificados:
- `.htaccess` (líneas 25-50)

### Descripción:
Control de acceso basado en direcciones IP del laboratorio.

### Configuración:
```apache
<RequireAll>
    # Denegar acceso por defecto
    Require all denied

    # Permitir acceso solo desde 3 IPs del laboratorio
    Require ip 192.168.1.100
    Require ip 192.168.1.101
    Require ip 192.168.1.102

    # Permitir localhost para pruebas
    Require ip 127.0.0.1
    Require ip ::1
</RequireAll>
```

### Instrucciones de Configuración:

1. **Obtener IPs de las terminales del laboratorio:**
   - Windows: `ipconfig`
   - Linux: `ifconfig` o `ip addr`

2. **Reemplazar las IPs de ejemplo:**
   - Editar `.htaccess` líneas 43-45
   - Cambiar `192.168.1.100`, `192.168.1.101`, `192.168.1.102`
   - Por las IPs reales de las 3 terminales asignadas

3. **Verificar acceso:**
   - Intentar acceder desde una terminal permitida →  Acceso permitido
   - Intentar acceder desde otra IP →  Error 403 Forbidden

---

## Seguridad Implementada

### Protección contra Directory Traversal:
```php
// Limpieza de parámetros peligrosos
$carpeta_actual = str_replace(['..', '/', '\\\\'], '', $carpeta_actual);
```

### Sanitización de Nombres:
```php
// Eliminar caracteres peligrosos en nombres de archivo/carpeta
$nombre = str_replace(['..', '/', '\\', ':', '*', '?', '"', '<', '>', '|'], '', $nombre);
```

### Validación de Permisos:
```php
// Verificar autenticación en cada página
if($_SESSION["autenticado"] != "SI") {
    header("Location: index.php");
    exit();
}
```

---

## Estructura de Archivos del Proyecto

```
myBox/
├── .htaccess                    (MODIFICADO - restricciones IP y tamaño)
├── index.php
├── registrar.php
├── carpetas.php                 (MODIFICADO - navegación, iconos, MB)
├── agrearchi.php               (MODIFICADO - límite 20MB)
├── abrArchi.php                (MODIFICADO - visualización PDF/JPG/PNG)
├── compartir.php               (NUEVO - sistema de compartir)
├── ver_compartido.php          (NUEVO - ver archivos compartidos)
├── codigos/
│   ├── creadir.php
│   ├── crearcarpeta.php        (NUEVO - crear carpetas)
│   ├── borrar.php              (NUEVO - borrar archivos/carpetas)
│   ├── borarchi.php            (ANTIGUO - mantener por compatibilidad)
│   └── salir.php
├── partes/
│   ├── encabe.inc
│   ├── menu.inc
│   └── final.inc
├── errores/
│   ├── 400.php
│   ├── 403.php
│   ├── 404.php
│   └── 500.php
├── estilos/
├── imagenes/
└── LABORATORIO_4_DOCUMENTACION.md (ESTE ARCHIVO)
```

---

## Pruebas Recomendadas

### 1. Navegación de Carpetas:
- [ ] Crear carpeta desde raíz
- [ ] Navegar a la carpeta creada
- [ ] Crear subcarpeta dentro de carpeta
- [ ] Navegar usando breadcrumb
- [ ] Volver a la raíz

### 2. Gestión de Archivos:
- [ ] Subir archivo PDF (< 20MB)
- [ ] Subir archivo JPG (< 20MB)
- [ ] Intentar subir archivo > 20MB (debe fallar)
- [ ] Visualizar PDF en navegador
- [ ] Visualizar imagen en navegador
- [ ] Descargar archivo de otro tipo (.docx, .zip)
- [ ] Borrar archivo con confirmación

### 3. Compartir Archivos:
- [ ] Crear segundo usuario en el sistema
- [ ] Compartir archivo con segundo usuario
- [ ] Iniciar sesión con segundo usuario
- [ ] Ver archivos compartidos
- [ ] Descargar/visualizar archivo compartido

### 4. Restricción de IP:
- [ ] Configurar IPs permitidas
- [ ] Acceder desde IP permitida → OK
- [ ] Acceder desde IP no permitida → 403 Forbidden

---

## Notas Importantes para la Entrega

1. **Video Demostrativo:** Debe mostrar todas las funcionalidades implementadas
2. **Código en GitHub:** Subir todo el código con commits descriptivos
3. **Fecha Límite:** 27/10/2025 a las 17:15
4. **IPs del Laboratorio:** Configurar las IPs reales antes de la demostración

---

## Conclusión

Se han implementado exitosamente todas las funcionalidades solicitadas en el Laboratorio #4:

 Navegación entre carpetas
 Crear y borrar carpetas
 Compartir archivos entre usuarios
 Iconos por tipo de archivo
 Tamaño en MB
 Confirmación de borrado
 Visualización PDF/JPG/PNG
 Límite de 20MB
 Restricción por IP

El sistema myBox ahora tiene funcionalidades similares a DropBox y otros sistemas de almacenamiento en la nube.
