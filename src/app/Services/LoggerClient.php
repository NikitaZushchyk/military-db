<?php

namespace App\Services;

use App\Jobs\SendLogJob;

class LoggerClient
{
    public function log(string $action, string $description, string $service = 'core-app'): void
    {
        SendLogJob::dispatch(
            $action,
            $description,
            $service,
            now()->toDateTimeString()
        );
    }
}
