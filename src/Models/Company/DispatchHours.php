<?php

namespace IdQueue\IdQueuePackagist\Models\Company;

use Exception;
use IdQueue\IdQueuePackagist\Traits\CompanyDbConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class DispatchHours extends Model
{
    use CompanyDbConnection;

    public $timestamps = false;  // Explicitly set this to false

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Dispatch_Hours';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Company_Dept_ID',
        'Sun_Open_Time',
        'Sun_Close_Time',
        'Mon_Open_Time',
        'Mon_Close_Time',
        'Tues_Open_Time',
        'Tues_Close_Time',
        'Wed_Open_Time',
        'Wed_Close_Time',
        'Thur_Open_Time',
        'Thur_Close_Time',
        'Fri_Open_Time',
        'Fri_Close_Time',
        'Sat_Open_Time',
        'Sat_Close_Time',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'Sun_Open_Time' => 'datetime:H:i:s',
        'Sun_Close_Time' => 'datetime:H:i:s',
        'Mon_Open_Time' => 'datetime:H:i:s',
        'Mon_Close_Time' => 'datetime:H:i:s',
        'Tues_Open_Time' => 'datetime:H:i:s',
        'Tues_Close_Time' => 'datetime:H:i:s',
        'Wed_Open_Time' => 'datetime:H:i:s',
        'Wed_Close_Time' => 'datetime:H:i:s',
        'Thur_Open_Time' => 'datetime:H:i:s',
        'Thur_Close_Time' => 'datetime:H:i:s',
        'Fri_Open_Time' => 'datetime:H:i:s',
        'Fri_Close_Time' => 'datetime:H:i:s',
        'Sat_Open_Time' => 'datetime:H:i:s',
        'Sat_Close_Time' => 'datetime:H:i:s',
    ];


    /**
     * Define the relationship to the DispatchDepartment model.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(DispatchDepartment::class, 'Company_Dept_ID', 'ID');
    }

    /**
     * @throws Exception
     */
    public static function isDispatchMode($day, $dept_ID): bool
    {
        // Fetch dispatch hours for the given department
        $dispatchHours = self::where('Company_Dept_ID', $dept_ID)->first();

        if (!$dispatchHours) {
            return false;
        }

        // Define day mapping
        $dayMapping = [
            1 => ['Mon_Open_Time', 'Mon_Close_Time'],
            2 => ['Tues_Open_Time', 'Tues_Close_Time'],
            3 => ['Wed_Open_Time', 'Wed_Close_Time'],
            4 => ['Thur_Open_Time', 'Thur_Close_Time'],
            5 => ['Fri_Open_Time', 'Fri_Close_Time'],
            6 => ['Sat_Open_Time', 'Sat_Close_Time'],
            7 => ['Sun_Open_Time', 'Sun_Close_Time'],
        ];

        // Ensure the day exists in mapping
        if (!isset($dayMapping[$day])) {
            throw new Exception("Invalid day value: $day.");
        }

        [$openTimeField, $closeTimeField] = $dayMapping[$day];

        // Convert times to Carbon objects
        $openTime = $dispatchHours->$openTimeField ? Carbon::parse($dispatchHours->$openTimeField) : null;
        $closeTime = $dispatchHours->$closeTimeField ? Carbon::parse($dispatchHours->$closeTimeField) : null;
        $currentTime = Carbon::now();

        // Debugging
        // dd($openTime, $closeTime, $currentTime);

        // Ensure times are not null
        if (!$openTime || !$closeTime) {
            return false;
        }

        return $currentTime->between($openTime, $closeTime);
    }


}
