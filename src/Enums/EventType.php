<?php
namespace IdQueue\IdQueuePackagist\Enums;

use BenSampo\Enum\Enum;

final class EventType extends Enum
{
    const INTERPRETER_UPDATED =   'interpreter-updated';
    const DISPATCH_UPDATED =      'dispatch-updated';
    const REQUEST_UPDATED =       'request-updated';
}