<?php

namespace IdQueue\IdQueuePackagist\Models\Admin;

use IdQueue\IdQueuePackagist\Traits\AdminDbConnection;
use Illuminate\Database\Eloquent\Model;

class CC2DB extends Model
{
    use AdminDbConnection;

    protected $table = 'CC2DB';

    protected $primaryKey = 'ID';

    public $timestamps = false;  // Disable timestamps if they are not used

    protected $fillable = [
        'Company_Code',
        'Dept_Lic_Num',
        'Company_DB',
        'Company_Name',
        'Enable_Public_Access',
        'Is_Enterprise',
        'two_step_auth',
        'is_mobile_app',
        'notification_alert',
        'notification_alert_options',
        'Status',
        'is_doximity',
        'config_val',
    ];

    protected $casts = [
        'Enable_Public_Access' => 'boolean',
        'Is_Enterprise' => 'boolean',
        'two_step_auth' => 'boolean',
        'is_mobile_app' => 'boolean',
        'notification_alert' => 'boolean',
        'is_doximity' => 'boolean',
        'config_val' => 'object',
    ];

    public function getConfigValue($key)
    {
        $config = $this->config_val; // This will already be an array due to casting

        return $config->$key ?? null; // Return the value or null if not found
    }
}
