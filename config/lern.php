<?php

return [

    /**
     * To avoid infinite loops that generate thousands of records/notifications in an instant
     * Please make sure you use a Cache driver that is persistant such as redis, memcache, file, etc
     *
     * Value is in seconds.
     */
    'ratelimit' => 1,

    'record' => [
        /**
         * The Recorder to use
         */
        'class' => \Tylercd100\LERN\Components\Recorder::class,

        /**
         * The Model to use
         */
        'model' => \Tylercd100\LERN\Models\ExceptionModel::class,

        /**
         * Database connection to use. Null is the default connection.
         */
        'connection' => null,

        /**
         * Database table to use
         */
        'table' => 'vendor_tylercd100_lern_exceptions',

        /**
         * Information to store
         */
        'collect' => [
            'method' => false, //When true it will collect GET, POST, DELETE, PUT, etc...
            'data' => false, //When true it will collect Input data
            'status_code' => true,
            'user_id' => false,
            'url' => false,
            'ip' => false,
        ],

        /**
         * When record.collect.data is true, this will exclude certain data keys recursively
         */
        'excludeKeys' => [
            'password'
        ]
    ],


];
