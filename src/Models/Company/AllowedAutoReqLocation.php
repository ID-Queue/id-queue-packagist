<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Model;

class AllowedAutoReqLocation extends Model
{
    use CompanyDbConnection;

    public $incrementing = true;

    public $timestamps = false;

    protected $table = 'Allowed_Auto_Req_Location';

    protected $primaryKey = 'ID';

    protected $fillable = [
        'ID',
        'Start_IP',
        'End_IP',
    ];
}
