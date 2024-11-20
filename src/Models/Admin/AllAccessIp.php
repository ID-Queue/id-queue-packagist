<?php

namespace IdQueue\IdQueuePackagist\Models\Admin;

use IdQueue\IdQueuePackagist\Traits\AdminDbConnection;
use Illuminate\Database\Eloquent\Model;

class AllAccessIp extends Model
{
    use AdminDbConnection;

    protected $table = 'All_AccessIp';

    protected $primaryKey = 'ID';

    public $timestamps = false;  // Disable timestamps if they are not used

    protected $fillable = [
        'Start_IP',
        'End_IP',
        'Comment',
    ];
}
