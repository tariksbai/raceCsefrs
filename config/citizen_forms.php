<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Citizen Forms Platform Configuration
    |--------------------------------------------------------------------------
    */

    // Upload configuration
    'uploads' => [
        'max_file_size' => env('UPLOAD_MAX_FILE_SIZE', 10240), // KB
        'allowed_mimes' => env('UPLOAD_ALLOWED_MIMES', 'pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif'),
        'storage_disk' => env('UPLOAD_DISK', 'local'),
    ],

    // Form settings
    'forms' => [
        'allow_public_access' => env('FORMS_ALLOW_PUBLIC', true),
        'allow_anonymous_responses' => env('FORMS_ALLOW_ANONYMOUS', true),
        'max_fields_per_form' => env('FORMS_MAX_FIELDS', 50),
    ],

    // Security
    'security' => [
        'iframe_allowed_domains' => env('IFRAME_ALLOWED_DOMAINS', 'forms.office.com,forms.microsoft.com'),
        'rate_limit_per_minute' => env('RATE_LIMIT_PER_MINUTE', 60),
        'x_frame_options' => env('X_FRAME_OPTIONS', 'SAMEORIGIN'),
    ],

    // Pagination
    'pagination' => [
        'per_page' => env('PAGINATION_PER_PAGE', 15),
    ],

    // Export
    'export' => [
        'csv_delimiter' => ';',
        'csv_encoding' => 'UTF-8',
    ],
];
