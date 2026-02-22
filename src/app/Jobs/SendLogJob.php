<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log as SystemLog;

class SendLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $timeout = 10;

    public function __construct(
        protected string $action,
        protected string $description,
        protected string $service,
        protected string $timestamp
    ) {}

    public function handle(): void
    {
        try {
            $response = Http::timeout(5)
                ->post(config('services.logger.url').'/logs', [
                    'service' => $this->service,
                    'action' => $this->action,
                    'description' => $this->description,
                    'created_at' => $this->timestamp,
                ]);

            if ($response->failed()) {
                throw new \Exception('Logger Service returned '.$response->status());
            }

        } catch (\Exception $e) {
            SystemLog::error('Failed to send log to service: '.$e->getMessage());
            $this->release(10);
        }
    }
}
