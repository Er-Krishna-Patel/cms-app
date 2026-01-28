<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Rich Text Editor Configuration
    |--------------------------------------------------------------------------
    |
    | Configure which rich text editor to use across your application.
    | Supported editors: 'ckeditor', 'tinymce'
    |
    */

    'primary' => env('PRIMARY_EDITOR', 'tinymce'),

    /*
    |--------------------------------------------------------------------------
    | Editor Library Paths
    |--------------------------------------------------------------------------
    |
    | Define the paths to self-hosted editor libraries. These are loaded
    | from your public directory for full control and offline capability.
    |
    */

    'ckeditor' => [
        'js' => [
            '/vendor/ckeditor/ckeditor.js',
        ],
        'config' => [
            'height' => 450,
            'toolbar' => [
                ['Bold', 'Italic', 'Underline', 'Strike'],
                ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote'],
                ['Link', 'Unlink', 'Image', 'Table'],
                ['Format', 'Font', 'FontSize'],
                ['TextColor', 'BGColor'],
                ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
                ['Undo', 'Redo'],
                ['Source', 'Maximize'],
            ],
            'removeButtons' => '',
            'allowedContent' => true,
            'extraPlugins' => 'uploadimage,image2',
        ],
    ],

    'tinymce' => [
        'js' => [
            '/vendor/tinymce/tinymce.min.js',
        ],
        'config' => [
            'license_key' => 'gpl',
            'height' => 450,
            'min_height' => 300,
            'max_height' => 1000,
            'plugins' => 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount autoresize',
            'toolbar' => 'undo redo | styles | bold italic underline forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media table | code fullscreen',
            'menubar' => false,
            'branding' => false,
            'content_css' => '/ckeditor/contents.css',
            'autoresize_min_height' => 300,
            'autoresize_max_height' => 1000,
            'autoresize_overflow_padding' => 20,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Upload Configuration
    |--------------------------------------------------------------------------
    |
    | Configure upload routes for each editor. These routes handle
    | image and file uploads from the editor.
    |
    */

    'upload_routes' => [
        'ckeditor' => 'admin.ckeditor.upload',
        'tinymce' => 'admin.tinymce.upload',
    ],

];
