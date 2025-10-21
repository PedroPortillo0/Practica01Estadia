# Panel de Administración de Frases Diarias

## Descripción

Sistema completo de gestión de frases diarias con arquitectura hexagonal, que incluye:
- Panel web de administración para gestionar frases (CRUD completo)
- API endpoints para obtener la frase del día
- Base de datos para almacenamiento persistente
- Migración desde CSV a base de datos

## Arquitectura

El sistema sigue los principios de arquitectura hexagonal:

### Capas de Dominio
- **Entidad**: `App\Domain\Entities\DailyQuote`
- **Puerto (Interface)**: `App\Domain\Ports\DailyQuoteRepositoryInterface`

### Capas de Aplicación
- **Use Cases**:
  - `GetDailyQuote` - Obtener frase del día
  - `GetAllDailyQuotes` - Listar todas las frases
  - `CreateDailyQuote` - Crear nueva frase
  - `UpdateDailyQuote` - Actualizar frase existente
  - `DeleteDailyQuote` - Eliminar frase

### Capas de Infraestructura
- **Repositorio**: `App\Infrastructure\Persistence\EloquentDailyQuoteRepository`
- **Modelo Eloquent**: `App\Models\DailyQuote`

### Capas de Presentación
- **Controlador Web**: `App\Http\Controllers\AdminDailyQuoteController`
- **Controlador API**: `App\Http\Controllers\DailyQuoteController`

## Instalación y Configuración

### 1. Ejecutar la Migración

Primero, asegúrate de que tu servidor MySQL esté corriendo, luego ejecuta:

```bash
php artisan migrate
```

Esto creará la tabla `daily_quotes` con la siguiente estructura:
- `id` - ID único
- `quote` - Texto de la frase
- `author` - Autor de la frase
- `category` - Categoría (Motivación, Éxito, etc.)
- `day_of_year` - Día del año (1-366)
- `is_active` - Estado activo/inactivo
- `created_at` y `updated_at` - Timestamps

### 2. Datos Iniciales

Las frases iniciales ya fueron importadas a la base de datos durante la configuración inicial. Ahora todas las frases se gestionan exclusivamente desde el panel web de administración.

### 3. Iniciar el Servidor

```bash
php artisan serve
```

## Uso del Panel de Administración

### Acceder al Panel

Una vez que el servidor esté corriendo, accede al panel en:

```
http://localhost:8000/admin/daily-quotes
```

### Funcionalidades del Panel

#### 1. **Vista Principal** (`/admin/daily-quotes`)
- Muestra todas las frases configuradas
- Estadísticas del sistema:
  - Día del año actual
  - Total de frases
  - Cobertura del año
- Acciones rápidas: Editar y Eliminar

#### 2. **Crear Nueva Frase** (`/admin/daily-quotes/create`)
- Formulario para agregar una nueva frase
- Campos:
  - **Frase**: Texto de la frase motivacional (máx. 1000 caracteres)
  - **Autor**: Nombre del autor (máx. 100 caracteres)
  - **Categoría**: Selección entre: Motivación, Éxito, Perseverancia, Sabiduría, Inspiración, Liderazgo, Creatividad
  - **Día del Año**: Número entre 1 y 366
  - **Estado**: Activa/Inactiva

#### 3. **Editar Frase** (`/admin/daily-quotes/{id}/edit`)
- Formulario para editar una frase existente
- Mismos campos que crear

#### 4. **Eliminar Frase**
- Botón de eliminación con confirmación
- Acción permanente

## Endpoints de la API

### 1. Obtener Frase del Día (Simple)

```http
GET /api/daily-quote
```

**Respuesta:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "quote": "El obstáculo es el camino...",
    "author": "Marco Aurelio",
    "category": "Estoica",
    "date": "2025-10-20"
  }
}
```

### 2. Obtener Frase del Día (Detallada)

```http
GET /api/daily-quote/detail
```

**Respuesta:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "quote": "El obstáculo es el camino...",
    "author": "Marco Aurelio",
    "category": "Estoica",
    "date": "2025-10-20",
    "day_of_year": 294,
    "is_active": true
  }
}
```

### 3. Obtener Todas las Frases

```http
GET /api/daily-quote/all
```

**Respuesta:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "quote": "...",
      "author": "...",
      "category": "...",
      "day_of_year": 1,
      "is_active": true
    }
  ],
  "total": 10
}
```

## Lógica de Negocio

### Asignación de Frases por Día

El sistema utiliza el campo `day_of_year` para determinar qué frase mostrar cada día:
- Se calcula el día del año actual (1-366)
- Se busca la frase con ese `day_of_year`
- Si no hay frase para ese día, se retorna un mensaje de error

### Ventajas del Sistema

1. **Persistencia**: Las frases se almacenan en base de datos
2. **Flexibilidad**: Puedes tener frases para todos los 366 días del año
3. **Control**: Panel de administración intuitivo
4. **Escalabilidad**: Arquitectura hexagonal permite cambiar fácilmente la implementación
5. **Estado**: Las frases pueden estar activas o inactivas

## Próximas Mejoras

### Autenticación Admin
Actualmente, el panel está abierto para desarrollo. Para producción, debes implementar autenticación:

1. Edita `app/Http/Middleware/AdminAuth.php`
2. Descomenta y ajusta el código de autenticación
3. Agrega el campo `is_admin` a la tabla `users`
4. Registra el middleware en `bootstrap/app.php`

### Motor de IA para Generar Frases
Como mencionaste, más adelante puedes implementar:
- Integración con API de OpenAI o similar
- Generación automática de 365 frases únicas
- Validación de duplicados
- Categorización automática

### Mejoras Adicionales
- Búsqueda y filtrado de frases
- Importación/exportación masiva
- Historial de cambios
- Programación de frases futuras
- Estadísticas de visualización

## Estructura de Archivos

```
app/
├── Application/
│   └── UseCases/
│       ├── CreateDailyQuote.php
│       ├── UpdateDailyQuote.php
│       ├── DeleteDailyQuote.php
│       ├── GetAllDailyQuotes.php
│       └── GetDailyQuote.php
├── Domain/
│   ├── Entities/
│   │   └── DailyQuote.php
│   └── Ports/
│       └── DailyQuoteRepositoryInterface.php
├── Http/
│   ├── Controllers/
│   │   ├── AdminDailyQuoteController.php
│   │   └── DailyQuoteController.php
│   └── Middleware/
│       └── AdminAuth.php
├── Infrastructure/
│   └── Persistence/
│       └── EloquentDailyQuoteRepository.php
└── Models/
    └── DailyQuote.php

database/
├── migrations/
│   └── 2025_10_20_232811_create_daily_quotes_table.php
└── seeders/
    └── DailyQuotesSeeder.php

resources/
└── views/
    └── admin/
        └── daily-quotes/
            ├── layout.blade.php
            ├── index.blade.php
            ├── create.blade.php
            └── edit.blade.php

routes/
├── api.php (endpoints API)
└── web.php (rutas del panel)
```

## Notas Técnicas

- El sistema utiliza **Dependency Injection** para todos los use cases
- Los repositorios están registrados en `AppServiceProvider`
- Las vistas utilizan **Bootstrap 5** para el diseño
- El middleware `AdminAuth` está preparado pero sin autenticación por ahora
- Todas las frases se almacenan y gestionan directamente en la base de datos MySQL

## Soporte

Para cualquier duda o problema, revisa:
1. Logs de Laravel: `storage/logs/laravel.log`
2. Verificar que MySQL esté corriendo
3. Asegurarte de que las migraciones se ejecutaron correctamente
4. Comprobar que el seeder importó los datos

---

**Fecha de Creación**: 20 de Octubre, 2025  
**Versión**: 1.0

