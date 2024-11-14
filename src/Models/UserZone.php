<?php

namespace IdQueue\IdQueuePackage\Models;;

use Illuminate\Database\Eloquent\Model;

class UserZone extends Model
{
    public $incrementing = false;

    public $timestamps = false; // Since there's no primary key

    protected $table = 'User_Zones'; // Disabling auto-incrementing since there is no primary key

    protected $primaryKey = null;

    protected $fillable = [
        'User_ID',
        'Zone_ID',
    ];

    // Specify the database connection if needed
    protected $connection = 'db_connection'; // Disabling timestamps if not present in the table
}
