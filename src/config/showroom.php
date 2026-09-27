<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Customer Password
    |--------------------------------------------------------------------------
    |
    | Initial password generated when a new customer account is automatically
    | created by staff in sales invoice or appointment workflows.
    |
    */

    'default_customer_password' => env('DEFAULT_CUSTOMER_PASSWORD', 'Auto123'),

    /*
    |--------------------------------------------------------------------------
    | Media Processing
    |--------------------------------------------------------------------------
    |
    | Dimensions and compression settings for vehicle images processed
    | by CarMediaService using the GD extension.
    |
    */

    'media' => [
        'max_width' => (int) env('CAR_MEDIA_MAX_WIDTH', 1280),
        'max_height' => (int) env('CAR_MEDIA_MAX_HEIGHT', 720),
        'jpeg_quality' => (int) env('CAR_MEDIA_JPEG_QUALITY', 90),
    ],

    /*
    |--------------------------------------------------------------------------
    | View Data Cache Store
    |--------------------------------------------------------------------------
    |
    | Cache store driver used to persist showroom details and runtime
    | admin settings for frontend navigation and footer.
    |
    */

    'cache_store' => env('VIEW_DATA_CACHE_STORE', 'file'),

    /*
    |--------------------------------------------------------------------------
    | Default Pagination
    |--------------------------------------------------------------------------
    |
    | Default items per page for client-side car inventory and admin lists.
    |
    */

    'pagination' => [
        'client' => (int) env('SHOWROOM_PAGINATION_CLIENT', 12),
        'admin' => (int) env('SHOWROOM_PAGINATION_ADMIN', 12),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting (Throttling)
    |--------------------------------------------------------------------------
    |
    | Maximum request attempts per minute for public booking & lead forms.
    |
    */

    'throttle' => [
        'appointments' => (int) env('THROTTLE_APPOINTMENTS_PER_MINUTE', 10),
        'leads' => (int) env('THROTTLE_LEADS_PER_MINUTE', 15),
    ],

];
