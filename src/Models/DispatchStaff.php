<?php

namespace IdQueue\IdQueuePackage\Models;;

use Illuminate\Database\Eloquent\Model;

class DispatchStaff extends Model
{
    // Specify the table name if it doesn't follow Laravel's naming conventions
    public $timestamps = false;

    // Set the primary key if it's not the default 'id'
    protected $table = 'Dispatch_Staff';

    // Disable automatic timestamp management since the table doesn't have 'created_at' and 'updated_at'
    protected $primaryKey = 'ID';

    protected $connection = 'db_connection';

    // Define the columns that are mass assignable
    protected $fillable = [
        'Company_Dept_ID',
        'Service',
        'Acc_ID',
        'name',
        'Acc_GUID',
    ];

    // Define relationships
    public function userAccount()
    {
        // Assuming there's a 'User_Account' model with 'Acc_GUID' as the foreign key
        return $this->belongsTo(User::class, 'Acc_GUID');
    }
}
