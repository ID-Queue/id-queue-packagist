<?php

namespace IdQueue\IdQueuePackagist\Utils\Traits;

use Illuminate\Support\Facades\DB;

trait BuildingUtility
{
    public static function checkIfBuildZoneLoc_Exists(
        string $servBuild, string $zone, bool $appZoneShow,
        string $loc, bool $appLocShow, int $dept_ID
    ): bool {
        if (! ($appZoneShow || $appLocShow)) {
            return true;  // If neither is enabled, return true early
        }

        $query = DB::table('Dispatch_Building AS db')
            ->leftJoin('Dispatch_Zone AS dz', 'db.Building_GUID', '=', 'dz.Building_ID')
            ->leftJoin('Dispatch_Location AS dl', 'dz.Zone_GUID', '=', 'dl.Zone_ID')
            ->whereNull('dz.Zone_Enabled')
            ->orWhere('dz.Zone_Enabled', 0)
            ->whereNull('db.Building_Enabled')
            ->orWhere('db.Building_Enabled', 0)
            ->whereNull('dl.Location_Enabled')
            ->orWhere('dl.Location_Enabled', 0)
            ->where('db.Building_GUID', $servBuild)
            ->where('dz.Zone_GUID', $zone)
            ->where('dz.Company_Dept_ID', $dept_ID)
            ->where('db.Company_Dept_ID', $dept_ID)
            ->where('dl.Company_Dept_ID', $dept_ID);

        if ($appZoneShow && ! $appLocShow && $zone && ! $loc) {
            return $query->exists(); // Check only Zone
        }

        if ($appLocShow && $loc) {
            return $query->where('dl.Location_GUID', $loc)->exists(); // Check both Zone and Location
        }

        return true; // If no conditions matched, return true
    }

    public static function return_BuildingByID(int $dept_ID, string $bldID): ?string
    {
        return DB::table('Dispatch_Building')
            ->where('Company_Dept_ID', $dept_ID)
            ->where('Building_GUID', $bldID)
            ->value('name'); // Using `value` for a single field
    }

    public static function return_ZoneByBuidlingID(int $dept_ID, string $bldID): \Illuminate\Support\Collection
    {
        return DB::table('Dispatch_Zone')
            ->where(function ($q) {
                $q->whereNull('Zone_Enabled')
                    ->orWhere('Zone_Enabled', 0);
            })
            ->where('Company_Dept_ID', $dept_ID)
            ->where('Building_ID', $bldID)
            ->orderBy('name', 'asc')
            ->select(['Zone_GUID as value', 'name as label'])
            ->get();
    }

    public static function return_ZoneByID($dept_ID, $tmpZone)
    {
        return DB::table('Dispatch_Zone')
            ->where('Company_Dept_ID', $dept_ID)
            ->where('Zone_GUID', $tmpZone)
            ->value('name'); // Use `value()` for a single column value
    }

    public static function return_LocationByID($dept_ID, $locID)
    {
        return DB::table('Dispatch_Location')
            ->where('Company_Dept_ID', $dept_ID)
            ->where('Location_GUID', $locID)
            ->value('name'); // Use `value()` for a single column value
    }
}
