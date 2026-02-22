<?php

namespace App\Observers;

use App\Jobs\SendAnalyzeJob;
use App\Models\DutyRoster;

class DutyRosterObserver
{
    public function saved(DutyRoster $dutyRoster): void
    {
        SendAnalyzeJob::dispatch($dutyRoster->soldier->id);
    }

    public function deleted(DutyRoster $dutyRoster): void
    {
        SendAnalyzeJob::dispatch($dutyRoster->soldier->id);
    }
}
