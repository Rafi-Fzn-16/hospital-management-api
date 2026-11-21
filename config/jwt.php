<?php

return [
    'secret' => env('JWT_SECRET'),

    'algorithm' => 'HS256',

    'ttl' => (int) env('JWT_TTL', 3600),
];
