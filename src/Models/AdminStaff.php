<?php

namespace IdQueue\IdQueuePackage\Models;;

use Illuminate\Database\Eloquent\Model;

class AdminStaff extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'Admin_Staff';

    protected $primaryKey = 'Acc_ID';

    protected $connection = 'db_connection';

    protected $fillable = [
        'Company_Dept_ID',
        'Service',
        'Acc_ID',
    ];

    /**
     * Define the relationship to UserAccount.
     */
    public function userAccount()
    {
        return $this->belongsTo(User::class, 'Acc_ID', 'Acc_ID');
    }
}
