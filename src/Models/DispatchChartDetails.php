<?php

namespace IdQueue\IdQueuePackage\Models;;

use Illuminate\Database\Eloquent\Model;

class DispatchChartDetails extends Model
{
    public $incrementing = true;

    public $timestamps = false;

    protected $table = 'Dispatch_Chart_Details';

    protected $primaryKey = 'ID';

    protected $connection = 'db_connection';

    protected $fillable = [
        'Company_Dept_ID',
        'name',
        'Action_Time',
        'Action_Taken',
        'Request_ID',
    ];

    protected $casts = [
        'Action_Time' => 'string',
        'Action_Taken' => 'integer',
        'Request_ID' => 'integer',
        'Company_Dept_ID' => 'integer',
    ];
}
