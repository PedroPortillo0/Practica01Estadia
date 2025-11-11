# Guía para Configurar la Base de Datos Localmente

Esta guía explica cómo configurar y ejecutar las migraciones de base de datos de este proyecto Laravel en tu entorno local usando XAMPP y MySQL Workbench.

## 📋 Requisitos Previos

### 1. Software Necesario

Ya tienes:
- ✅ **XAMPP** (incluye Apache y MySQL)
- ✅ **MySQL Workbench**

Necesitas instalar además:

- **PHP 8.2 o superior**
  - Si XAMPP incluye PHP 8.2+, úsalo
  - Si no, descarga PHP desde [php.net](https://www.php.net/downloads.php)
  - Asegúrate de que PHP esté en el PATH del sistema

- **Composer** (Gestor de dependencias de PHP)
  - Descarga desde [getcomposer.org](https://getcomposer.org/download/)
  - Sigue las instrucciones de instalación para Windows

### 2. Verificar Instalación

Abre una terminal (PowerShell o CMD) y ejecuta:

```bash
php -v          # Debe mostrar PHP 8.2 o superior
composer -v     # Debe mostrar la versión de Composer
```

## 🗄️ Configuración de la Base de Datos

### Paso 1: Iniciar MySQL en XAMPP

1. Abre el **Panel de Control de XAMPP**
2. Inicia el servicio **MySQL** (haz clic en "Start")
3. Verifica que MySQL esté corriendo (el botón debe decir "Stop")

### Paso 2: Crear la Base de Datos

**Opción A: Usando MySQL Workbench**

1. Abre **MySQL Workbench**
2. Conecta a tu servidor MySQL (generalmente `localhost` o `127.0.0.1`, puerto `3306`)
   - Usuario: `root`
   - Contraseña: (la que configuraste en XAMPP, puede estar vacía)
3. Ejecuta este comando SQL para crear la base de datos:

```sql
CREATE DATABASE actividad_estadia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Opción B: Usando la línea de comandos**

Abre PowerShell/CMD y ejecuta:

```bash
# Conectar a MySQL (ajusta usuario y contraseña según tu configuración)
mysql -u root -p

# Luego ejecuta:
CREATE DATABASE actividad_estadia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;
```

## 📥 Obtener las Migraciones

### Opción 1: Clonar el Repositorio Completo

Si tienes acceso al repositorio Git:

```bash
git clone [URL_DEL_REPOSITORIO]
cd ActividadEstadia
```

### Opción 2: Copiar Solo las Migraciones

Si solo necesitas las migraciones, copia la carpeta `database/migrations/` del proyecto.

La ruta completa debería ser:
```
database/migrations/
```

Dentro encontrarás archivos como:
- `2025_09_09_233206_create_users_hexagonal_table.php`
- `2025_09_10_034442_create_verification_codes_table.php`
- `2025_10_01_204359_update_users_table_structure_v2.php`
- ... y más archivos de migración

## ⚙️ Configuración del Proyecto

### Paso 1: Instalar Dependencias

Si tienes el proyecto completo, en la raíz del proyecto ejecuta:

```bash
composer install
```

Esto instalará todas las dependencias de Laravel necesarias.

### Paso 2: Crear Archivo de Configuración

Necesitas crear un archivo `.env` en la raíz del proyecto. 

**Si no tienes el proyecto completo**, crea un archivo `.env` con este contenido mínimo:

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:VALUE_AQUI  # (Generar con: php artisan key:generate)
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=actividad_estadia
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

**Importante:** Ajusta `DB_PASSWORD` si tu MySQL en XAMPP tiene contraseña.

### Paso 3: Generar Clave de Aplicación

Si tienes el proyecto completo, ejecuta:

```bash
php artisan key:generate
```

Esto generará la clave `APP_KEY` que necesitas en el `.env`.

## 🚀 Ejecutar las Migraciones

### Opción 1: Si Tienes el Proyecto Laravel Completo

En la raíz del proyecto, ejecuta:

```bash
php artisan migrate
```

Este comando ejecutará todas las migraciones en orden y creará todas las tablas en la base de datos.

### Opción 2: Si Solo Tienes las Migraciones

Si solo tienes los archivos de migración pero no el proyecto completo, necesitarás:

1. **Crear un proyecto Laravel mínimo:**
   ```bash
   composer create-project laravel/laravel proyecto_temp
   cd proyecto_temp
   ```

2. **Copiar las migraciones** a `database/migrations/`

3. **Configurar el `.env`** como se explicó arriba

4. **Ejecutar las migraciones:**
   ```bash
   php artisan migrate
   ```

### Opción 3: Ejecutar Manualmente con MySQL Workbench

Si prefieres ejecutar las migraciones manualmente:

1. Lee cada archivo de migración en `database/migrations/`
2. Cada migración tiene un método `up()` que contiene el código SQL
3. Ejecuta el SQL manualmente en MySQL Workbench

**Ejemplo:** Para la migración `create_users_hexagonal_table.php`, el SQL sería:

```sql
CREATE TABLE users_hexagonal (
    id VARCHAR(255) PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    apellido_paterno VARCHAR(255) NOT NULL,
    apellido_materno VARCHAR(255) NOT NULL,
    telefono VARCHAR(10) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email_verificado BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

⚠️ **Nota:** Este método es más laborioso porque tendrías que ejecutar cada migración en orden cronológico (según la fecha en el nombre del archivo).

## ✅ Verificar que Funcionó

### Usando MySQL Workbench:

1. Abre MySQL Workbench
2. Conecta a tu base de datos `actividad_estadia`
3. Deberías ver múltiples tablas creadas:
   - `users_hexagonal`
   - `verification_codes`
   - `user_quiz_responses`
   - `daily_quotes`
   - `sessions`
   - `cache`
   - `cache_locks`
   - `jobs`
   - `job_batches`
   - `failed_jobs`
   - `migrations` (tabla que registra qué migraciones se ejecutaron)

### Usando la Terminal:

```bash
# Si tienes el proyecto Laravel completo:
php artisan migrate:status

# O verifica directamente en MySQL:
mysql -u root -p -e "USE actividad_estadia; SHOW TABLES;"
```

## 🔧 Solución de Problemas

### Error: "SQLSTATE[HY000] [2002] No connection could be made"

- Verifica que MySQL esté corriendo en XAMPP
- Revisa que `DB_HOST` en `.env` sea `127.0.0.1` o `localhost`
- Verifica que el puerto sea `3306`

### Error: "Access denied for user 'root'@'localhost'"

- Revisa la contraseña de MySQL en `DB_PASSWORD` del `.env`
- Si no tiene contraseña, deja `DB_PASSWORD=` vacío

### Error: "Database 'actividad_estadia' doesn't exist"

- Crea la base de datos primero (ver Paso 2 arriba)

### Error: "Class 'Illuminate\Database\...' not found"

- Necesitas instalar las dependencias: `composer install`
- Asegúrate de tener el proyecto Laravel completo

### Las migraciones no se ejecutan en orden

- Laravel ejecuta las migraciones automáticamente en orden cronológico según la fecha en el nombre del archivo
- No ejecutes migraciones manualmente fuera de orden

## 📝 Resumen Rápido

1. ✅ Instala PHP 8.2+ y Composer
2. ✅ Inicia MySQL en XAMPP
3. ✅ Crea la base de datos `actividad_estadia`
4. ✅ Obtén las migraciones del proyecto
5. ✅ Configura el archivo `.env`
6. ✅ Ejecuta `php artisan migrate`

## 🆘 ¿Necesitas Ayuda?

Si encuentras algún problema:
- Verifica que todas las extensiones de PHP estén habilitadas (pdo_mysql, mbstring, etc.)
- Revisa los logs en `storage/logs/laravel.log`
- Asegúrate de que XAMPP tenga PHP 8.2 o superior

---

**¡Listo!** Una vez que ejecutes las migraciones, tendrás toda la estructura de la base de datos creada localmente y podrás empezar a trabajar con ella.

