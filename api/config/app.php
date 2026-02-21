<?php

return [
    'name' => $_ENV['APP_NAME'] ?? 'Data Annotation Platform',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'url' => $_ENV['APP_URL'] ?? 'http://localhost',
    'jwt_secret' => $_ENV['JWT_SECRET'] ?? '',
    'cors_allowed_origins' => explode(',', $_ENV['CORS_ALLOWED_ORIGINS'] ?? '*'),
];
