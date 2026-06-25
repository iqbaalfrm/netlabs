<?php

/**
 * Konfigurasi AI / Gemini API untuk fitur Quiz Generator.
 * Dibaca dari .env agar mudah diganti tanpa edit kode.
 */
return [
    'gemini_api_key' => env('GEMINI_API_KEY'),
    'gemini_model'   => env('GEMINI_MODEL', 'gemini-1.5-flash'),
    'gemini_url'     => 'https://generativelanguage.googleapis.com/v1beta/models/',
];