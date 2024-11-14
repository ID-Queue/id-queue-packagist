<?php

namespace IdQueue\IdQueuePackage\Models;;

use Illuminate\Database\Eloquent\Model;

class SequelizeMeta extends Model
{
    // Table associated with the model
    public $incrementing = false;

    // Disable auto-incrementing as 'name' is the primary key, and it is not auto-incremented
    public $timestamps = false;

    // Disable timestamps as the table does not have created_at and updated_at columns
    protected $table = 'SequelizeMeta';

    // Specify which attributes are mass assignable
    protected $fillable = [
        'name',
    ];
}
