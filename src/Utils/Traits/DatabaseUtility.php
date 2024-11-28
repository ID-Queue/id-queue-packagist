<?php

namespace IdQueue\IdQueuePackagist\Utils\Traits;

use IdQueue\IdQueuePackagist\Models\Admin\CC2DB;

trait DatabaseUtility
{
    // In DatabaseUtility trait
    public static function getDoximityStatus($Company_Code)
    {
        $is_doximity = CC2DB::where('Company_Code', $Company_Code)
            ->value('is_doximity');

        return $is_doximity ?? null;
    }
}
