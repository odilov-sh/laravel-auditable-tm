<?php

return [

    'service_id' => env('AUTH_SERVICE_ID'),
    'enabled' => env('AUDIT_TM_ENABLED', true),
    'receiver_url' => env('AUDIT_TM_RECEIVER_URL'),
    'secret_key' => env('AUDIT_TM_SECRET_KEY'),
    'user_id_resolver' => \OdilovSh\LaravelAuditTm\Resolvers\UserIdResolver::class,
    'getters' => [
        'created' => \OdilovSh\LaravelAuditTm\Getters\CreatedEventGetter::class,
        'updated' => \OdilovSh\LaravelAuditTm\Getters\UpdatedEventGetter::class,
        'deleted' => \OdilovSh\LaravelAuditTm\Getters\DeletedEventGetter::class,
    ],
    'events' => [
        'created',
        'updated',
        'deleted',
    ],

    /*
    |--------------------------------------------------------------------------
    | Global exclude
    |--------------------------------------------------------------------------
    |
    | Have something you always want to exclude by default? - add it here.
    | Note that this is overwritten (not merged) with local exclude
    |
    */

    'exclude' => ['created_at', 'updated_at', 'deleted_at', 'id'],

    /*
    |--------------------------------------------------------------------------
    | Empty Values
    |--------------------------------------------------------------------------
    |
    | Should Audit records be stored when the recorded old_values & new_values
    | are both empty?
    |
    | Some events may be empty on purpose. Use allowed_empty_values to exclude
    | those from the empty values check. For example when auditing
    | model retrieved events which will never have new and old values.
    |
    |
    */

    'audit_empty_values' => false,
    'allowed_empty_values' => [],

    /*
    |--------------------------------------------------------------------------
    | Audit Console
    |--------------------------------------------------------------------------
    |
    | Whether console events should be audited (e.g. php artisan db:seed).
    |
    */

    'console' => false,
];
