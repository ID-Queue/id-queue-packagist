<?php

namespace IdQueue\IdQueuePackagist\Services;

use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB as Database;

class ConnectionService
{
    /**
     * Execute a raw database query.
     */
    public static function query(string $query): bool
    {
        return Database::statement($query);
    }

    /**
     * Connect to the default database connection.
     */
    public static function connect(): Connection
    {
        return Database::connection(config('idqueuepackagist.company'));
    }

    /**
     * Set a dynamic database connection using provided data.
     */
    public static function setConnection(object $data): void
    {
        Config::set('database.connections.db_connection', [
            'driver' => 'sqlsrv',
            'url' => '',
            'host' => env('DB_HOST'),
            'port' => env('DB_PORT'),
            'database' => $data->Company_DB,
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
            'encrypt' => 'yes',
            'trust_server_certificate' => true,
        ]);
    }

    /**
     * Connect to a predefined database for CC_2.
     */
    public static function CC_2_DB(): Connection
    {
        return Database::connection(config('idqueuepackagist.admin'));
    }

    /**
     * Connect to a predefined database for IDQ Logs.
     */
    public static function IDQ_Logs(): Connection
    {
        return Database::connection(config('idqueuepackagist.admin-logs'));
    }

    /**
     * Connect to a predefined demo database.
     */
    public static function IDQ_DB_Demo(): Connection
    {
        return Database::connection(config('idqueuepackagist.company-demo'));
    }
}
