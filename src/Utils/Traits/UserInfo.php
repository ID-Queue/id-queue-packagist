<?php

namespace IdQueue\IdQueuePackagist\Utils\Traits;

use Exception;
use IdQueue\IdQueuePackagist\Models\Company\DeptPreSetting;
use IdQueue\IdQueuePackagist\Models\Company\User;

trait UserInfo
{
    /**
     * Get the first and last name of a user by department ID and username.
     */
    public static function getUserFirstLastName(int $deptId, string $username): array|false
    {
        $user = User::where('Company_Dept_ID', $deptId)
            ->where('username', $username)
            ->select(['First_name', 'Last_name'])
            ->first();

        return $user ? [$user->First_name, $user->Last_name] : false;
    }

    /**
     * Get department values for a given department ID.
     */
    public static function getDeptValue(int $deptId): array
    {
        $data = DeptPreSetting::where('Company_Dept_ID', $deptId)
            ->select([
                'Company_Dept',
                'Service_Single',
                'Staff_Single',
                'Location_Single',
                'Zone_Single',
                'Building_Single',
                'Person_ID',
                'Second_Person_ID',
                'Requester_ID',
            ])
            ->first();

        return $data ? (array) $data : [];
    }

    public static function checkIfEmailExists(string $email, string $ccVal): int
    {
        $recordExists = User::where('Company_Code', $ccVal)
            ->where('email', $email)
            ->where(function ($q) {
                $q->whereNull('Account_Deleted')
                    ->orWhere('Account_Deleted', 0);
            })
            ->exists(); // Using exists() for better performance

        return $recordExists ? 1 : 0;
    }

    /**
     * @throws Exception
     */
    public static function randomPassword(): string
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = '';
        $alphaLength = strlen($alphabet) - 1;

        // Use random_int for better randomness (cryptographically secure)
        for ($i = 0; $i < 12; $i++) {
            $n = random_int(0, $alphaLength);
            $pass .= $alphabet[$n];
        }

        return $pass;
    }

    public static function returnDisplayNameFromGUID($strGUID, $dept_ID): ?string
    {
        $user = User::where('Company_Dept_ID', $dept_ID)
            ->where('GUID', $strGUID)
            ->first(['First_name', 'Last_name']); // Use `first()` for single record retrieval

        if ($user) {
            return $user->Last_name.', '.$user->First_name;
        }

        return null; // Return null if no matching user is found
    }
}
