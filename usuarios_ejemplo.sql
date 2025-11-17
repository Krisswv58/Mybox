-- =====================================================================
-- Script SQL para Crear Usuarios de Prueba - myBox
-- =====================================================================
-- Este script es opcional y sirve para crear usuarios de prueba
-- para demostrar la funcionalidad de compartir archivos
-- =====================================================================

-- NOTA: Ajustar el nombre de la base de datos según su configuración
USE mybox;

-- Verificar la estructura de la tabla de usuarios
-- (Descomentar para ver la estructura actual)
-- DESCRIBE usuarios;

-- =====================================================================
-- EJEMPLO 1: Crear usuarios de prueba
-- =====================================================================
-- IMPORTANTE: Ajustar los campos según la estructura real de su tabla

-- Usuario 1: estudiante1
INSERT INTO usuarios (usuario, contrasena, nombre, email)
VALUES ('estudiante1', MD5('password123'), 'Juan Pérez', 'juan@example.com');

-- Usuario 2: estudiante2
INSERT INTO usuarios (usuario, contrasena, nombre, email)
VALUES ('estudiante2', MD5('password123'), 'María García', 'maria@example.com');

-- Usuario 3: estudiante3
INSERT INTO usuarios (usuario, contrasena, nombre, email)
VALUES ('estudiante3', MD5('password123'), 'Carlos López', 'carlos@example.com');

-- =====================================================================
-- EJEMPLO 2: Si la tabla usa otro tipo de encriptación
-- =====================================================================
-- Si usan SHA1 en lugar de MD5:
-- INSERT INTO usuarios (usuario, contrasena, nombre, email)
-- VALUES ('estudiante1', SHA1('password123'), 'Juan Pérez', 'juan@example.com');

-- Si usan PASSWORD() (MySQL antiguo):
-- INSERT INTO usuarios (usuario, contrasena, nombre, email)
-- VALUES ('estudiante1', PASSWORD('password123'), 'Juan Pérez', 'juan@example.com');

-- Si almacenan contraseñas en texto plano (NO RECOMENDADO):
-- INSERT INTO usuarios (usuario, contrasena, nombre, email)
-- VALUES ('estudiante1', 'password123', 'Juan Pérez', 'juan@example.com');

-- =====================================================================
-- Verificar que los usuarios se crearon correctamente
-- =====================================================================
SELECT * FROM usuarios;

-- =====================================================================
-- CREAR DIRECTORIOS PARA LOS USUARIOS
-- =====================================================================
-- Después de crear los usuarios en la base de datos, debe crear
-- sus directorios correspondientes en el sistema de archivos:

-- Windows (ejecutar en CMD):
-- mkdir d:\mybox\estudiante1
-- mkdir d:\mybox\estudiante2
-- mkdir d:\mybox\estudiante3

-- Linux (ejecutar en Terminal):
-- mkdir -p /var/www/mybox/estudiante1
-- mkdir -p /var/www/mybox/estudiante2
-- mkdir -p /var/www/mybox/estudiante3
-- chmod 755 /var/www/mybox/estudiante*

-- =====================================================================
-- LIMPIAR USUARIOS DE PRUEBA (si es necesario)
-- =====================================================================
-- Descomentar estas líneas para eliminar los usuarios de prueba:

-- DELETE FROM usuarios WHERE usuario IN ('estudiante1', 'estudiante2', 'estudiante3');

-- =====================================================================
-- CONSULTAS ÚTILES
-- =====================================================================

-- Ver todos los usuarios registrados:
-- SELECT usuario, nombre, email FROM usuarios;

-- Buscar usuario específico:
-- SELECT * FROM usuarios WHERE usuario = 'estudiante1';

-- Cambiar contraseña de un usuario:
-- UPDATE usuarios SET contrasena = MD5('nuevapassword') WHERE usuario = 'estudiante1';

-- Contar usuarios registrados:
-- SELECT COUNT(*) as total_usuarios FROM usuarios;

-- =====================================================================
