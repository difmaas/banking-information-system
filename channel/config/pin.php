<?php

return [
    'idle_minutes' => (int) env('PIN_IDLE_MINUTES', 5),
    'verification_ttl_minutes' => (int) env('PIN_TTL_MINUTES', 10),
    'max_attempts' => (int) env('PIN_MAX_ATTEMPTS', 5),
    'lock_minutes' => (int) env('PIN_LOCK_MINUTES', 15),
    'weak_pins' => [
        '000000',
        '111111',
        '123456',
        '654321',
        '123123',
    ],
];
