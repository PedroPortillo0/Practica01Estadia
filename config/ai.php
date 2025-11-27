<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Provider
    |--------------------------------------------------------------------------
    |
    | Especifica el proveedor de IA a utilizar.
    | Opciones: 'gemini', 'openai', 'claude', etc.
    |
    */
    'provider' => env('AI_PROVIDER', 'gemini'),

    /*
    |--------------------------------------------------------------------------
    | AI API Key
    |--------------------------------------------------------------------------
    |
    | La clave API para autenticarse con el proveedor de IA.
    |
    */
    'api_key' => env('AI_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | AI Base URL
    |--------------------------------------------------------------------------
    |
    | URL base de la API del proveedor de IA.
    |
    */
    'base_url' => env('AI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),

    /*
    |--------------------------------------------------------------------------
    | AI Project ID
    |--------------------------------------------------------------------------
    |
    | ID del proyecto (opcional, principalmente para Google Cloud).
    |
    */
    'project_id' => env('AI_PROJECT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Default Options
    |--------------------------------------------------------------------------
    |
    | Opciones por defecto para las generaciones de IA.
    |
    */
    'default_options' => [
        'temperature' => 0.9,
        'max_tokens' => 1024,
        'top_p' => 0.95,
        'top_k' => 40,
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenAI Specific Config
    |--------------------------------------------------------------------------
    |
    | Configuración específica para OpenAI.
    |
    */
    'openai_model' => env('AI_OPENAI_MODEL', 'gpt-3.5-turbo'),
];

