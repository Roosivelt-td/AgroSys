<?php

return [

    /*
    |---------------------------------------------------------------------------
    | Class Namespace
    |---------------------------------------------------------------------------
    |
    | This value sets the root namespace for Livewire component classes in
    | your application. This value is used by the various health checks
    | and internal logic that discovers your Livewire components.
    |
    */

    'class_namespace' => 'App\\Livewire',

    /*
    |---------------------------------------------------------------------------
    | View Path
    |---------------------------------------------------------------------------
    |
    | This value sets the path for Livewire component views in your
    | application. This value is used by the various health checks
    | and internal logic that discovers your Livewire views.
    |
    */

    'view_path' => resource_path('views/livewire'),

    /*
    |---------------------------------------------------------------------------
    | Layout
    |---------------------------------------------------------------------------
    |
    | The default layout view that will be used when rendering a component
    | via Route::get().
    |
    */

    'layout' => 'layouts.app',

    /*
    |---------------------------------------------------------------------------
    | Lazy Placeholder
    |---------------------------------------------------------------------------
    |
    | The default placeholder view that will be used when rendering a component
    | that is loaded lazily.
    |
    */

    'lazy_placeholder' => null,

    /*
    |---------------------------------------------------------------------------
    | Temporary File Uploads
    |---------------------------------------------------------------------------
    |
    | Livewire supports native file uploads via its "WithFileUploads" trait.
    | The following configuration options allow you to customize the
    | disk and validation rules for those temporary file uploads.
    |
    */

    'temporary_file_upload' => [
        'disk' => null,        // Defaults to "local"
        'middleware' => null,  // Defaults to "throttle:60,1"
        'preview_mimes' => [   // Supported file types for temporary previews.
            'png', 'gif', 'bmp', 'svg', 'wav', 'mp4',
            'mov', 'avi', 'wmv', 'mp3', 'm4a',
            'jpg', 'jpeg', 'mpga', 'webp', 'pdf',
        ],
        'rules' => 'max:102400', // Aumentado a 100MB (102400 KB)
    ],

    /*
    |---------------------------------------------------------------------------
    | Manifest Path
    |---------------------------------------------------------------------------
    |
    | This value sets the path to the Livewire manifest file. This file is
    | used by Livewire to keep track of where your components are.
    |
    */

    'manifest_path' => null,

    /*
    |---------------------------------------------------------------------------
    | Back Button Cache
    |---------------------------------------------------------------------------
    |
    | This value sets whether Livewire should cache the state of a component
    | when the user navigates away from it via the back button.
    |
    */

    'back_button_cache' => false,

    /*
    |---------------------------------------------------------------------------
    | Render On Redirect
    |---------------------------------------------------------------------------
    |
    | This value sets whether Livewire should render the component one final
    | time before redirecting to another page.
    |
    */

    'render_on_redirect' => false,

];
