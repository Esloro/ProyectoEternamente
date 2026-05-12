<?php

// CORS para que el frontend Angular (puerto 4200) pueda llamar al backend
// (puerto 8000). Como usamos Sanctum por tokens (no cookies), no es
// necesario `supports_credentials`.
return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // Origenes permitidos (hardcodeados para evitar lios con el caching de config).
    // En dev se permite localhost:4200, en prod los dos dominios de Vercel.
    'allowed_origins' => [
        'http://localhost:4200',
        'https://eternamente.tech',
        'https://www.eternamente.tech',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
