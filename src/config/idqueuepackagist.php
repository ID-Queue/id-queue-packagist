<?php

return [
    // Database connection name
    'company' => env('IDQUEUEPACKAGIST_DB_CONNECTION', 'db_connection'),
    'admin' => env('IDQUEUEPACKAGIST_DB_ADMIN_CONNECTION', 'CC_2_DB'),
    'admin-logs' => env('IDQUEUEPACKAGIST_DB_ADMIN_LOG_CONNECTION', 'IDQ_Logs'),
    'company-demo' => env('IDQUEUEPACKAGIST_DB_DEMO_CONNECTION', 'IDQ_DB_Demo'),
    'mail-service' => env('MAIL_SERVICES', 'https://email-service.dev1.id-queue.com/'),
];
