<?php

namespace IDQueue\IDQueuePackagist\Enums;

use BenSampo\Enum\Enum;

/**
 * Enum representing various request statuses for an application.
 * Each status corresponds to a specific state in the request lifecycle,
 * where each constant can be used to check or set the current status
 * of a request, such as whether it is approved, arrived, declined, etc.
 *
 * @method static static App_Pending()
 * @method static static App_Approved()
 * @method static static App_Arrived()
 * @method static static App_Session()
 * @method static static App_Declined()
 * @method static static App_Done()
 * @method static static App_Dispatched()
 * @method static static App_Paused()
 */
class RequestStatus extends Enum
{
    const App_Pending = 'App_Pending';

    const App_Approved = 'App_Approved';

    const App_Arrived = 'App_Arrived';

    const App_Session = 'App_Session';

    const App_Declined = 'App_Declined';

    const App_Done = 'App_Done';

    const App_Dispatched = 'App_Dispatched';

    const App_Paused = 'App_Paused';
}
