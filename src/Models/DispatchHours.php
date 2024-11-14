<?php

namespace IdQueue\IdQueuePackage\Models;;

use Illuminate\Database\Eloquent\Model;

class DispatchHours extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Dispatch_Hours';

    protected $connection = 'db_connection';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Company_Dept_ID',
        'Sun_Open_Time',
        'Sun_Close_Time',
        'Mon_Open_Time',
        'Mon_Close_Time',
        'Tues_Open_Time',
        'Tues_Close_Time',
        'Wed_Open_Time',
        'Wed_Close_Time',
        'Thur_Open_Time',
        'Thur_Close_Time',
        'Fri_Open_Time',
        'Fri_Close_Time',
        'Sat_Open_Time',
        'Sat_Close_Time',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'Sun_Open_Time' => 'time',
        'Sun_Close_Time' => 'time',
        'Mon_Open_Time' => 'time',
        'Mon_Close_Time' => 'time',
        'Tues_Open_Time' => 'time',
        'Tues_Close_Time' => 'time',
        'Wed_Open_Time' => 'time',
        'Wed_Close_Time' => 'time',
        'Thur_Open_Time' => 'time',
        'Thur_Close_Time' => 'time',
        'Fri_Open_Time' => 'time',
        'Fri_Close_Time' => 'time',
        'Sat_Open_Time' => 'time',
        'Sat_Close_Time' => 'time',
    ];
}
