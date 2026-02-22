<?php

namespace App\Observers;

use App\Jobs\SendAnalyzeJob;
use App\Models\Assignment;
use App\Services\LoggerClient;

class AssignmentObserver
{
    public function __construct(protected LoggerClient $logger) {}

    public function created(Assignment $assignment): void
    {
        $assignment->load(['soldier', 'item']);

        $soldierName = $assignment->soldier ? $assignment->soldier->last_name : 'Unknown';
        $itemSerial = $assignment->item ? $assignment->item->serial_number : 'Unknown';

        $this->logger->log(
            'WEAPON_ISSUED',
            "Soldier {$soldierName} (ID: {$assignment->soldier_id}) took Item {$itemSerial} (ID: {$assignment->warehouse_id})"
        );
    }

    public function updated(Assignment $assignment): void
    {
        if ($assignment->isDirty('return_date') &&
            is_null($assignment->getOriginal('return_date')) &&
            ! is_null($assignment->return_date)) {

            $assignment->load(['soldier', 'item']);

            $soldierName = $assignment->soldier ? $assignment->soldier->last_name : 'Unknown';
            $itemSerial = $assignment->item ? $assignment->item->serial_number : 'Unknown';

            $this->logger->log(
                'WEAPON_RETURNED',
                "Soldier {$soldierName} returned Item {$itemSerial}"
            );
        }
    }

    public function deleted(Assignment $assignment): void
    {
        SendAnalyzeJob::dispatch($assignment->soldier->id);
    }

    public function saved(Assignment $assignment): void
    {
        SendAnalyzeJob::dispatch($assignment->soldier->id);
    }
}
