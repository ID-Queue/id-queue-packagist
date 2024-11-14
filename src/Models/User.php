<?php

namespace IdQueue\IdQueuePackage\Models;;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const moduleList = ['advanced', 'master', 'root'];

    const SUPER_ADMIN = 'superadmin';

    public $timestamps = false;

    protected $table = 'user_accounts';

    protected $dates = ['account_pw_last_modified'];

    protected $connection = 'db_connection'; // Disabling timestamps if not present in the table

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'guid',
        'Company_Dept_ID',
        'company_code',
        'first_name',
        'last_name',
        'username',
        'password',
        'password_tmp',
        'password_tmp_enabled',
        'type_admin',
        'type_of_admin',
        'type_staff',
        'type_of_staff',
        'staff_salary',
        'staff_login_state',
        'staff_last_action',
        'staff_login_location',
        'type_req',
        'type_req_dir_access',
        'req_short_url',
        'email',
        'email_when_req',
        'login_fails',
        'account_locked',
        'account_deleted',
        'account_created',
        'account_reset_pw',
        'account_pw_last_modified',
        'verify_code',
        'last_login',
        'encrypt_password',
    ];

    public function getAuthPassword()
    {
        return $this->encrypt_password;
    }

    public function staffStation()
    {
        return $this->hasOne(StaffStation::class, 'Staff_GUID', 'GUID');
    }

    public function dispatchBuilding()
    {
        return $this->belongsTo(DispatchBuilding::class, 'Staff_Login_Location', 'id');
    }

    public function getCheckInAtAttribute($value)
    {
        return Carbon::parse($value)->toDateTimeString();
    }
}
