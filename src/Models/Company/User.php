<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use CompanyDbConnection, HasApiTokens, HasFactory,Notifiable;

    const moduleList = ['advanced', 'master', 'root'];

    const SUPER_ADMIN = 'superadmin';

    protected $table = 'User_Accounts';

    /**
     * Define casts for datetime attributes.
     */
    protected $casts = [
        'Account_PW_Last_Modified' => 'datetime',
        'Account_Created' => 'datetime',
        'Staff_Last_Action' => 'datetime',
        'Last_Login' => 'datetime',
        'Check_In_At' => 'datetime',
    ];

    // Disabling timestamps if not present in the table

    protected $fillable = [
        'GUID',
        'Company_Dept_ID',
        'Company_Code',
        'First_name',
        'Last_name',
        'username',
        'password',
        'password_tmp',
        'password_tmp_enabled',
        'Type_Admin',
        'Type_Of_Admin',
        'Type_Staff',
        'Type_Of_Staff',
        'Staff_Salary',
        'Staff_Login_State',
        'Staff_Last_Action',
        'Staff_Login_Location',
        'Type_Req',
        'Type_Req_Dir_Access',
        'Req_Short_Url',
        'email',
        'email_when_req',
        'Login_Fails',
        'Account_Locked',
        'Account_Deleted',
        'Account_Created',
        'Account_Reset_PW',
        'Account_PW_Last_Modified',
        'Check_In_At',
        'verify_code',
        'Last_Login',
        'encrypt_password',
        'isStationed',
        'is_mobile',
        'device_token',
        'device_type',
    ];

    // Hidden attributes (e.g., passwords)
    protected $hidden = [
        'password',
        'encrypt_password',
        'password_tmp',
        'verify_code',
    ];

    /**
     * Get the password for authentication.
     */
    public function getAuthPassword()
    {
        return $this->encrypt_password;
    }

    /**
     * Define a one-to-one relationship with StaffStation.
     */
    public function staffStation(): HasOne
    {
        return $this->hasOne(StaffStation::class, 'Staff_GUID', 'GUID');
    }

    /**
     * Define a belongs-to relationship with DispatchBuilding.
     */
    public function dispatchBuilding(): BelongsTo
    {
        return $this->belongsTo(DispatchBuilding::class, 'Staff_Login_Location', 'id');
    }

    /**
     * Format the `Check_In_At` attribute as a string.
     */
    public function getCheckInAtAttribute($value): string
    {
        return Carbon::parse($value)->toDateTimeString();
    }

    /**
     * Get the current status of the staff member based on conditions.
     */
    public function getStatus(): int
    {
        // Optimize Staff_Login_State handling by returning early
        if ($this->Staff_Login_State === 1) {
            return 0; // If logged in, reset state
        }

        if ($this->Staff_Login_State === 0) {
            return 8; // Change state to something else
        }

        // Cache the status query to reduce database calls
        $currentStatus = ActiveQueue::returnStaffCurrentStatus($this->GUID, $this->Company_Dept_ID);
        if ($currentStatus > 0) {
            return $this->mapStatus($currentStatus); // Map status based on the currentStatus value
        }

        // Check if dispatched to staff
        if (ActiveQueue::return_IfDispatchedToStaff($this->GUID, $this->Company_Dept_ID) === 1) {
            return 1; // Dispatched state
        }

        return $this->Staff_Login_State; // Default state if none of the above conditions match
    }

    /**
     * Map current status values to meaningful values.
     */
    private function mapStatus(int $currentStatus): int
    {
        $statusMapping = [
            7 => 4, // Paused
            4 => 5, // In session
            3 => 6, // SW
            2 => 7, // Thumbs
        ];

        return $statusMapping[$currentStatus] ?? $this->Staff_Login_State; // Default to current state if no match
    }
}
