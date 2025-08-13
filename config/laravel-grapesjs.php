<?php

return [

    // 'routes' => [
    //     'middleware' => ['auth', 'verified', 'auth.admin']
    // ],
    /*
    |--------------------------------------------------------------------------
    | Expose API
    |--------------------------------------------------------------------------
    |
    | This will expose the editor variable. 
    | It can be accessed via a window.gjsEditor
    |
    */

    'expose_api' => false,

    /*
    |--------------------------------------------------------------------------
    | Force Class
    |--------------------------------------------------------------------------
    |
    | @See https://github.com/artf/grapesjs/issues/546
    |
    */
    
    'force_class' => false,

    /*
    |--------------------------------------------------------------------------
    | Global Styles
    |--------------------------------------------------------------------------
    |
    | Global Styles for the editor blade file.
    */

    'styles' => [
        'vendor/laravel-grapesjs/assets/editor.css',
        'vendor/laravel-grapesjs/assets/plugins/tailwind/tailwind-menu-components.css'
    ],

    /*
    |--------------------------------------------------------------------------
    | Global Scripts
    |--------------------------------------------------------------------------
    |
    | Global scripts for the editor blade file.
    */

    'scripts' => [
        'vendor/laravel-grapesjs/assets/plugins/tailwind/enable-tailwind.js',
        'vendor/laravel-grapesjs/assets/editor.js',
        // 'vendor/laravel-grapesjs/assets/plugins/form-component/add-form-type.js',
    ],

    /*
    |--------------------------------------------------------------------------
    | Canvas styles and scripts
    |--------------------------------------------------------------------------
    |
    | The styles and scripts for the editor content.
    | You need to add these also to your layout.
    | e.g the bootstrap files, etc
    |
    */

    'canvas' => [
        'styles' => [
            'css/app.css'
        ],
        'scripts' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Assets Manager
    |--------------------------------------------------------------------------
    |
    | Here you can configure the disk and custom upload URL for your asset
    | manager.
    |
    */

    'assets' => [
        'disk' => 'public', //Default: local
        'path' => 'img/media', //Default: 'laravel-grapesjs/media',
        'upload_url' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Style Manager
    |--------------------------------------------------------------------------
    |
    | Enable/Disable selectors.
    | @see https://grapesjs.com/docs/api/style_manager.html#stylemanager
    |
    */

    'style_manager' => [
        'limited_selectors' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Manager
    |--------------------------------------------------------------------------
    |
    | Enable/Disable the autosave function for your editor.
    |
    */

    'storage_manager' => [
        'autosave' => false,
        'steps_before_save' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugin Manager
    |--------------------------------------------------------------------------
    |
    | You can enable/disable built-in plugins or can add any custom plugin from
    | this config. Formats for custom plugins are as below.
    |
    | 1. Simplest way
    |   'plugin-name' => 'https://url_to_plugin_script.com'
    |    
    | 2. Simple with options (Plugin script will be added to global scrips above)
    |   'plugin-name' => [
    |       //plugin options goes here
    |     ]
    |
    | 3. Advanced way
    |   [
    |       'enabled => true,
    |       'name' => 'plugin-name',
    |       'styles' => [
    |           'https://url_to_plugin_styles.com',
    |       ],
    |       'scripts' => [
    |           'https://url_to_plugin_script.com',
    |       ],
    |       'options' => [
    |           //plugin options goes here
    |       ],
    |   ]
    |
    */

    'plugins' => [
        'default' => [
            'basic_blocks' => true,
            'bootstrap4_blocks' => false,
            'code_editor' => true,
            'image_editor' => false,
            'custom_fonts' => [],
            'templates' => true,
        ],
        'custom' => [
            'grapesjs-custom-code' => 'https://unpkg.com/grapesjs-custom-code',
            // [
            //     'enabled' => true,
            //     'name' => 'gjs-plugin-ckeditor5',
            //     'options' => [],
            //     'scripts' => [
            //         env('APP_URL').'/vendor/laravel-grapesjs/assets/plugins/ckeditor/ckeditor5/build/ckeditor.js',
            //         env('APP_URL').'/vendor/laravel-grapesjs/assets/plugins/ckeditor/grapesjs-plugin-ckeditor5.js',
            //     ],
            // ],
            [
                'enabled' => true,
                'name' => 'grapesjs-tailwind',
                'options' => [],
                'scripts' => [
                    // 'https://cdn.tailwindcss.com',
                    env('APP_URL').'/vendor/laravel-grapesjs/assets/plugins/tailwind/tailwind-components.js',
                ],
            ],
            [
                'enabled' => true,
                'name' => 'grapesjs-plugin-forms',
                'options' => [],
                'scripts' => [
                    'https://unpkg.com/grapesjs-plugin-forms',
                ],
            ],
            [
                'enabled' => true,
                'name' => 'grapesjs-plugin-tailwind-forms',
                'options' => [],
                'scripts' => [
                    env('APP_URL').'/vendor/laravel-grapesjs/assets/plugins/form-component/add-form-type.js',
                ],
            ],
        ],
    ],
];