# Motor de IA - Documentación

## Descripción

Motor de IA genérico implementado siguiendo la arquitectura hexagonal del proyecto. Permite integrar cualquier proveedor de IA (Google Gemini, OpenAI, Claude, etc.) de forma intercambiable.

## Configuración

### Variables de Entorno

Agrega las siguientes variables a tu archivo `.env`:

```env
# Proveedor de IA (gemini, openai, claude, etc.)
AI_PROVIDER=gemini

# API Key del proveedor de IA
AI_API_KEY=tu_api_key_aqui

# URL base de la API
AI_BASE_URL=https://generativelanguage.googleapis.com/v1beta

# Project ID (opcional, principalmente para Google Cloud)
AI_PROJECT_ID=tu_project_id
```

### Ejemplo con Google Gemini

```env
AI_PROVIDER=gemini
AI_API_KEY=sk-tu_api_key_de_openai
AI_BASE_URL=https://api.openai.com/v1
AI_OPENAI_MODEL=gpt-3.5-turbo
```

**Nota:** `AI_PROJECT_ID` es opcional para Gemini cuando se usa API key directamente.

### Ejemplo con OpenAI

```env
AI_PROVIDER=openai
AI_API_KEY=sk-tu_api_key_de_openai
AI_BASE_URL=https://api.openai.com/v1
AI_OPENAI_MODEL=gpt-3.5-turbo
```

**Nota:** `AI_PROJECT_ID` no es necesario para OpenAI, solo para Google Gemini.

## Estructura del Motor

```
app/
├── Domain/
│   └── Ports/
│       └── AIServiceInterface.php          # Interfaz del servicio de IA
├── Infrastructure/
│   └── Services/
│       └── AIService.php                   # Implementación genérica
├── Application/
│   └── UseCases/
│       └── GenerateDailyQuotesWithAI.php   # Caso de uso para generar frases
└── Console/
    └── Commands/
        └── GenerateDailyQuotesCommand.php  # Comando artisan
```

## Uso

### Generar Frases Diarias con IA

#### Opción 1: Comando Manual

Para generar 365 o 366 frases diarias (según si el año es bisiesto):

```bash
php artisan ai:generate-quotes
```

Para generar frases para un año específico:

```bash
php artisan ai:generate-quotes --year=2025
```

El comando:
- Genera todas las frases usando IA
- Las guarda automáticamente en la tabla `daily_quotes`
- Asigna cada frase a un día del año (1-365 o 1-366)
- Omite días que ya tienen frases asignadas

#### Opción 3: Generación Automática (Programada)

El sistema está configurado para generar automáticamente las frases del año siguiente el **31 de diciembre a las 23:59** de cada año.

**Configuración del Cron Job:**

Para que funcione la generación automática, necesitas agregar esta línea al crontab de tu servidor:

```bash
* * * * * cd /ruta/a/tu/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

Esto ejecutará el scheduler de Laravel cada minuto, y Laravel decidirá qué tareas ejecutar según su programación.

**Nota:** Ajusta la zona horaria en `routes/console.php` según tu ubicación.

## Cambiar de Proveedor de IA

Para cambiar de proveedor, simplemente:

1. Crea una nueva implementación de `AIServiceInterface` (si es necesario)
2. O modifica `AIService.php` para agregar soporte al nuevo proveedor
3. Actualiza las variables de entorno en `.env`
4. Cambia el binding en `AppServiceProvider.php` si creaste una nueva implementación

### Ejemplo: Agregar soporte para OpenAI

En `AIService.php`, agrega un método:

```php
private function makeOpenaiRequest(string $prompt, array $options = []): array
{
    // Implementación específica para OpenAI
}
```

Y actualiza `.env`:

```env
AI_PROVIDER=openai
AI_API_KEY=sk-...
AI_BASE_URL=https://api.openai.com/v1
```

## Arquitectura

El motor sigue los principios de arquitectura hexagonal:

- **Domain/Ports**: Define la interfaz que debe cumplir cualquier servicio de IA
- **Infrastructure/Services**: Implementación concreta que se conecta a APIs externas
- **Application/UseCases**: Lógica de negocio que usa el servicio de IA
- **Dependency Injection**: El servicio se inyecta mediante interfaces, permitiendo fácil intercambio

## Próximas Funcionalidades

- [ ] Análisis de reflexiones con IA
- [ ] Recomendaciones personalizadas de frases
- [ ] Generación de preguntas reflexivas
- [ ] Análisis de progreso del usuario

