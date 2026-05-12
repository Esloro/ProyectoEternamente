<?php

// CORS para que el frontend Angular (puerto 4200) pueda llamar al backend
// (puerto 8000). Como usamos Sanctum por tokens (no cookies), no es
// necesario `supports_credentials`.
return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // FRONTEND_URL puede ser una sola URL o una lista separada por comas
    // (ej: "https://eternamente.tech,https://www.eternamente.tech").
    // Filtramos vacios por si la env queda sin definir.
    'allowed_origins' => array_filter(
        array_map('trim', explode(',', env('FRONTEND_URL', 'http://localhost:4200')))
    ),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
