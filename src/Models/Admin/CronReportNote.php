<?php

namespace IdQueue\IdQueuePackagist\Models\Admin;

use IdQueue\IdQueuePackagist\Traits\AdminDbConnection;
use Illuminate\Database\Eloquent\Model;

class CronReportNote extends Model
{
    use AdminDbConnection;

    protected $table = 'Cron_Report_Notes';

    protected $primaryKey = 'ID';

    public $timestamps = false;  // Disable timestamps if they are not used

    protected $fillable = [
        'notes',
        'created',
        'token_id',
    ];
}
