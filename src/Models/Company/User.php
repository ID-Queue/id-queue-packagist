<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use IdQueue\IdQueuePackagist\Enums\UserStatus;
use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use LaravelIdea\Helper\IdQueue\IdQueuePackagist\Models\Company\_IH_User_C;

class User extends Authenticatable
{
    use CompanyDbConnection, HasApiTokens, HasFactory,Notifiable;

    const moduleList = ['advanced', 'master', 'root'];

    const SUPER_ADMIN = 'superadmin';

    protected $table = 'User_Accounts';

    public $timestamps = false;  // Explicitly set this to false

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
     * Define the relationship to AdminStaff.
     */
    public function adminStaff(): BelongsTo
    {
        return $this->belongsTo(AdminStaff::class, 'GUID', 'Acc_ID');
    }

    public function services(): HasMany
    {
        return $this->hasMany(DispatchStaff::class, 'Acc_GUID', 'GUID');
    }

    /**
     * Define a one-to-one relationship with StaffStation.
     */
    public function staffStation(): HasMany
    {
        return $this->HasMany(StaffStation::class, 'Staff_GUID', 'GUID');
    }

    public function lastLocation(): HasOne
    {
        return $this->hasOne(LastLocation::class, 'Staff_GUID', 'GUID');
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

        $currentStatus = ActiveQueue::returnStaffCurrentStatus($this->GUID, $this->Company_Dept_ID);

        if ($currentStatus > 0) {
            return $this->mapStatus($currentStatus);
        }

        if (ActiveQueue::returnIfDispatchedToStaff($this->GUID, $this->Company_Dept_ID)) {
            return UserStatus::Dispatched;
        }
        if ((int) $this->Staff_Login_State === 1) {
            return UserStatus::Available;
        }

        return UserStatus::LoggedOut;
    }

    /**
     * Map current status values to meaningful values.
     */
    private function mapStatus(int $currentStatus): int
    {
        $statusMapping = [
            7 => UserStatus::Paused, // Paused
            4 => UserStatus::InProgress, // In session
            3 => UserStatus::Arrived, // SW
            2 => UserStatus::Accepted, // Thumbs
        ];

        // Return the mapped status or default to the current staff login state
        return $statusMapping[$currentStatus] ?? $this->Staff_Login_State; // Default to current state if no match
    }

    /**
     * Get users by status, but apply the logic of the getStatus method.
     */
    public static function getUsersByStatus(UserStatus $status): Collection|_IH_User_C|array
    {
        // Get all users that could match the status.
        $users = self::where('isStationed', false)->get();

        if ($status->value === UserStatus::Stationed()->value) {
            return self::where(['isStationed' => true, 'Staff_Login_State' => 1])->get();
        }

        // Filter users using the getStatus logic.
        return $users->filter(function ($user) use ($status) {
            return $user->getStatus() === $status->value;
        });
    }

    /**
     * Get users by multiple statuses, applying the getStatus method logic.
     */
    public static function getUsersByMultipleStatus(array $statuses): Collection
    {
        // Get all users that could match the statuses.
        $users = self::all();

        // Filter users using the getStatus logic.
        return $users->filter(function ($user) use ($statuses) {
            return in_array($user->getStatus(), $statuses);
        });
    }

    public function isDirRequestor(): bool
    {
        return ! $this->type_admin && ! $this->type_staff && $this->type_req && $this->type_req_dir_access && ! empty($this->req_short_url);
    }
}
