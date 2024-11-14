<?php

namespace IdQueue\IdQueuePackage\Models;;

use Illuminate\Database\Eloquent\Model;

class DeleteReason extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'Dispatch_Delete_Reason';

    protected $primaryKey = 'ID';

    protected $connection = 'db_connection';

    protected $fillable = [
        'ID',
        'Company_Dept_ID',
        'name',
    ];
}
