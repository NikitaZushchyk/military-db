<?php

namespace App\Jobs;

use App\Models\Soldier;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class SendAnalyzeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $timeout = 10;

    public function __construct(
        protected int $soldierId
    ) {}

    public function handle(): void
    {
        $soldier = Soldier::with(['assignments', 'duties'])->find($this->soldierId);

        if (! $soldier) {
            return;
        }

        $activeEquipmentCount = $soldier->assignments->whereNull('return_date')->count();

        $lastWeek = Carbon::now()->subDays(7);
        $dutyHours = $soldier->duties
            ->where('start_time', '>=', $lastWeek)
            ->sum(function ($roster) {
                $start = Carbon::parse($roster->start_time);
                $end = $roster->end_time ? Carbon::parse($roster->end_time) : Carbon::now();

                return $start->diffInHours($end);
            });

        $stats = [
            'status' => $soldier->status,
            'rank_id' => $soldier->rank_id,
            'equipment_count' => $activeEquipmentCount,
            'duty_hours' => $dutyHours,
        ];

        $body = json_encode([
            'soldierId' => $soldier->id,
            'stats' => $stats,
        ]);

        $rabbitConfig = config('queue.connections.rabbitmq.hosts.0');

        $connection = new AMQPStreamConnection(
            $rabbitConfig['host'],
            $rabbitConfig['port'],
            $rabbitConfig['user'],
            $rabbitConfig['password'],
            $rabbitConfig['vhost']
        );
        $channel = $connection->channel();

        $channel->exchange_declare('military_events', 'direct', false, true, false);

        $msg = new AMQPMessage($body, ['content_type' => 'application/json']);

        $headers = new AMQPTable([
            'type' => 'App\Message\AnalyzeDataMessage',
        ]);
        $msg->set('application_headers', $headers);

        $channel->basic_publish($msg, 'military_events', 'analytics_queue');

        $channel->close();
        $connection->close();
    }
}
