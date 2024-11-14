<?php

namespace IdQueue\IdQueuePackage\Models;;

use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'Access_Token';

    protected $connection = 'db_connection';

    protected $fillable = [
        'ID',
        'User_ID',
        'Created_At',
    ];

    protected $casts = [
        'Created_At' => 'datetime',
    ];
}
