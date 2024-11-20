<?php

namespace IdQueue\IdQueuePackagist\Traits;

trait CompanyDbConnection
{
    protected $connection;

    public function __construct()
    {
        $this->connection = config('idqueuepackagist.db_connection');
    }
}
