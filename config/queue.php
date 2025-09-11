<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Queue Connection Name
    |--------------------------------------------------------------------------
    |
    */
    'default' => env('QUEUE_CONNECTION', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection options for every queue backend
    | used by your application. 
    |
    */

    'connections' => [
        'database' => [
            'driver' => 'mongodb',
            'connection' => 'mongodb',
            'table' => 'jobs',
            'queue' => 'default',
            'retry_after' => (int) env('DB_QUEUE_RETRY_AFTER', 90),
            'after_commit' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Job Batching
    |--------------------------------------------------------------------------
    |
    | The following options configure the database and table that store job
    | batching information. 
    |
    */

    'batching' => [
        'driver' => 'mongodb',
        'database' => 'mongodb',
        'table' => 'job_batches',
    ],

    /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs
    |--------------------------------------------------------------------------
    |
    | These options configure the behavior of failed queue job logging so you
    | can control how and where failed jobs are stored.
    |
    */

    'failed' => [
        'driver' => 'mongodb',
        'database' => 'mongodb',
        'table' => 'failed_jobs',
    ],

];
