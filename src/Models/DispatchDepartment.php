<?php

namespace IdQueue\IdQueuePackage\Models;;

use Illuminate\Database\Eloquent\Model;

class DispatchDepartment extends Model
{
    // Table associated with the model
    public $incrementing = false;

    // Primary key for the table
    public $timestamps = false;

    // Disable auto-incrementing of the primary key (since the ID is not auto-incremented in your table)
    protected $table = 'Dispatch_Departments';

    // Disable timestamps if your table does not have 'created_at' and 'updated_at' columns
    protected $primaryKey = 'ID';

    // Specify which attributes are mass assignable
    protected $fillable = [
        'ID',
        'Company_Code',
        'Company_Dept',
    ];
}
