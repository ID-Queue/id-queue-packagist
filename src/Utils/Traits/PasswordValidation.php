<?php

namespace IdQueue\IdQueuePackagist\Utils\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

trait PasswordValidation
{
    /**
     * Check if a temporary password is valid.
     */
    public static function checkIfTempPassword($user, $pwVal): mixed
    {
        if ($user->count == 0) {
            return 0;
        }

        $rowModDate = $user->data;

        // Check if the temporary password matches
        if ($rowModDate->password_tmp != $pwVal) {
            return response([
                'success' => false,
                'message' => 'Login Information Incorrect, Please verify username, password, and company code',
            ], 401);
        }

        // Check if the password is within 15 minutes
        $timeMod = Carbon::parse($rowModDate->Account_PW_Last_Modified)->addMinutes(15);
        if ($timeMod <= Carbon::now()) {
            return response([
                'success' => false,
                'message' => 'Login Failed, Temporary Password Expired!',
            ], 401);
        }

        return 1;
    }

    /**
     * Resolve department ID based on user password validation.
     */
    public static function resolveDeptID($user, $pwVal)
    {
        if ($user->count > 0 && self::checkPassword($user->data->password, $pwVal)) {
            return $user->data->Company_Dept_ID;
        }

        return null;
    }

    public function checkPassword($hash, $pass): bool
    {
        return Hash::check($pass, $hash);
    }
}
