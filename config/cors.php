<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // '*' o'rniga aniq frontend manzillarini kiriting
    'allowed_origins' => [
        'http://localhost:3000',      // Local React/Next.js uchun
        'http://localhost:5173',      // Vite/Vue uchun
        'https://food-app-ruby-ten.vercel.app' // Sizning Vercel manzilingiz
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // Sanctum/Auth ishlatayotganingiz uchun bu true qolishi kerak
];
