<?php

namespace IdQueue\IdQueuePackagist\Traits;

trait LogsDbConnection
{
    protected $connection;

    public function __construct()
    {
        $this->connection = config('idqueuepackagist.admin-logs');
    }
}
