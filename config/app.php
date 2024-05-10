<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),
    'society_name' => '',
    // 'routes' =>  ['home', 'product-profil', 'admin', 'category'],
    'routes' =>  [],
    'payment_type_default' => 'Mobile',
    'admin_key' => 'ah07Xw68ee',

    'marks_type' => ['epe', 'devoir', 'participation'],

    'related_marks_type' => ['bonus', 'minus'],
    
    'admin_key' => 'ah07Xw68ee',

    'min_weeks_to_consider_that_teacher_has_worked_in_classe' => 2,

    'teacher_classes_limit' => 4,

    // 'min_mark_value_for_participation_calculation' 
    'MMVFPC' => 8,

    'local_events' => [
        'semestre-trimestre' => 'Semstres / Trimestres',
        'devoir' => 'Devoirs',
        'examen-bepc' => 'Examens BEPC',
        'examen-cap' => 'Examens CAP',
        'examen-dti' => 'Examens DTI',
        'examen-dt' => 'Examens DT',
        'examen-bac' => 'Examens BAC',
        'conge' => 'Congé',
        'ferie' => 'Jours Fériés',
        'vacances' => 'Vacances',
        'fete' => 'Fêtes',
        'conseil' => 'Conseil',
        'autres' => 'Autres',
    ],

    'local_roles' => [
        'Default',
        'Fondateur',
        'Directeur',
        'Directeur Adjoint',
        'Censeur',
        'Censeur Adjoint',
        'Surveillant Général',
        'Surveillant Général Adjoint',
        'Teacher',
        'Secretaire',
        'Secretaire Adjoint',
        'Agent de maintenance informatique',
        'Délégué',
        'Délégué Adjoint',
        'Assistant',
        'Agent entretien',
        "Agent d'entretien",
        "Agent de garde matinal",
        "Agent de garde nocturne",
        "Aide",
        "Parents",
        "Conseiller Pédagogique",
        "Autres",
    ],
    'admin_abilities' => [
        'admin' => 'Administrateur standart',
        'master' => 'Administrateur master',
        'default' => 'Administrateur'
    ],
    'classes_by_number' => [
        '6' => 'Sixième',
        '5' => 'Cinquième',
        '4' => 'Quatrième',
        '3' => 'Troisième',
        '2' => 'Seconde',
        '1' => 'Première',
        '7' => 'Terminale',
    ],

    'professions' => [
        'Enseignant',
        'Directeur',
        'Promoteur',
        'Directeur Adjoint',
        'Censeur',
        'Censeur Adjoint',
        'Surveillant Général',
        'Surveillant Général Adjoint',
        'Enseignant',
        'Secretaire',
        'Secretaire Adjoint',
        'Agent de maintenance informatique',
        'Délégué',
        'Délégué Adjoint',
        'Assistant',
        'Agent entretien',
        "Agent d'entretien",
        "Agent de garde matinal",
        "Agent de garde nocturne",
        "Medecin",
        "Sage femme",
        "Ministre",
        "Député",
        "Agent de police",
        "Militaire",
        "Géomètre",
        "Athlète",
        "Coiffeur",
        "Inspecteur",
        "Commerçant",
        "Cultivateur",
        "Pêcheur",
        "Conseiller",
        "Aide",
        "Conseiller Pédagogique",
        "Aucun",
        "Autre",
    ],

    'parentale_relations' => [
        'Père',
        'Mère',
        'Grand-père',
        'Grande-mère',
        'Oncle',
        'Tante',
        'Tuteur',
        'Tuteuse',
        'Frère',
        'Soeur',
        'Neveux',
        'Beau-frère',
        'Belle-soeur',
        'Belle-mère',
        'Beau-père',
        'Ami parental',
        'Arrière parent',
        'Autre',
    ],

    'local_epreuves_targets' => [
        'devoir' => 'Devoir',
        'epe' => 'Interrogation', 
        'bac' => "Examen BAC", 
        'dt' => "Examen DT", 
        'cap' => "Examen CAP", 
        // 'licence' => "Examen Licence", 
        'bepc' => "Examen BEPC",
        null => "Juste une épreuve",
    ],

    'users_displaying_sections' => [
        null => "Tous les utilisateurs",
        'admins' => "Tous les utilisateurs administrateurs",
        'admins_keys' => "Tous les utilisateurs administrateurs ayant une clé active",
        'connecteds' => "Tous les utilisateurs connectés",
        'blockeds' => "Tous les utilisateurs Bloqués",
        'not_blockeds' => "Tous les utilisateurs Non Bloqués",
        'confirmeds' => "Tous les utilisateurs Confirmés",
        'unconfirmeds' => "Tous les utilisateurs Non Confirmés",
        'blockeds_unconfirmeds' => "Tous les utilisateurs Bloqués Non Confirmés",
        'blockeds_confirmeds' => "Tous les utilisateurs Bloqués Confirmé",
        'parents' => "Tous les parents",
        'teachers' => "Tous les Enseignants",

    ],


    'epreuvesFolder' => "epreuvesFolder/",

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL', null),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'fr',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'fr',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,
        RealRashid\SweetAlert\SweetAlertServiceProvider::class,
        Barryvdh\DomPDF\ServiceProvider::class,
        Maatwebsite\Excel\ExcelServiceProvider::class,
        // App\Providers\UserProvider::class,

        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\FortifyServiceProvider::class,
        App\Providers\JetstreamServiceProvider::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [

        'App' => Illuminate\Support\Facades\App::class,
        'Arr' => Illuminate\Support\Arr::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
        'Bus' => Illuminate\Support\Facades\Bus::class,
        'Cache' => Illuminate\Support\Facades\Cache::class,
        'Config' => Illuminate\Support\Facades\Config::class,
        'Cookie' => Illuminate\Support\Facades\Cookie::class,
        'Crypt' => Illuminate\Support\Facades\Crypt::class,
        'Date' => Illuminate\Support\Facades\Date::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Http' => Illuminate\Support\Facades\Http::class,
        'Js' => Illuminate\Support\Js::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'RateLimiter' => Illuminate\Support\Facades\RateLimiter::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        'Redis' => Illuminate\Support\Facades\Redis::class,
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'Str' => Illuminate\Support\Str::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,
        'alert' => RealRashid\SweetAlert\Facades\Alert::class,
        'PDF' => Barryvdh\DomPDF\Facade::class,
        'Excel' => Maatwebsite\Excel\Facades\Excel::class,
        // 'Ztwen' => App\Helpers\UserTraits\UserTrait::class,

    ],

];
