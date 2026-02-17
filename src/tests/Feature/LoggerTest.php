<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LoggerTest extends TestCase
{
    public function test_it_can_connect_to_real_logger_service()
    {
        if (env('CI')) {
            $this->markTestSkipped('Skipping real connection test in CI environment (no logger service available).');
        }
        $url = config('services.logger.url').'/logs';

        $response = Http::timeout(3)->post($url, [
            'service' => 'core-test-suite',
            'action' => 'TEST_CONNECTION_CHECK',
            'description' => 'Integration test ping '.now()->toDateTimeString(),
        ]);

        if ($response->failed()) {
            dump('Connection Failed. Status: '.$response->status());
            dump('Body: '.$response->body());
        }

        $this->assertTrue(
            $response->successful(),
            'Expected a successful response (2xx), but got '.$response->status()
        );
    }

    public function test_it_sends_correct_data_structure()
    {
        Http::fake([
            config('services.logger.url').'/*' => Http::response(['id' => 999], 201),
        ]);

        Http::post(config('services.logger.url').'/logs', [
            'service' => 'test',
            'action' => 'fake_action',
            'description' => 'fake_desc',
        ]);

        Http::assertSent(function ($request) {
            return $request->url() == config('services.logger.url').'/logs' &&
                $request['service'] == 'test' &&
                $request['action'] == 'fake_action';
        });
    }
}
