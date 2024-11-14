<?php

namespace IdQueue\IdQueuePackage\Models;;

use Illuminate\Database\Eloquent\Model;

class AllowedAutoReqLocation extends Model
{
    public $incrementing = true;

    public $timestamps = false;

    protected $table = 'Allowed_Auto_Req_Location';

    protected $primaryKey = 'ID';

    protected $connection = 'db_connection';

    protected $fillable = [
        'ID',
        'Start_IP',
        'End_IP',
    ];
}
