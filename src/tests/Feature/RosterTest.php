<?php

namespace Tests\Feature;

use App\Models\DutyRoster;
use App\Models\DutyType;
use App\Models\Rank;
use App\Models\Soldier;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RosterTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $soldier;
    private $dutyType;

    protected function setUp(): void
    {
        parent::setUp();

        $rank = Rank::firstOrCreate(['name' => 'Солдат']);
        $unit = Unit::firstOrCreate(['name' => '1-ша рота']);

        $this->dutyType = DutyType::firstOrCreate(['name' => 'Варта']);

        $this->user = User::factory()->create();
        $this->soldier = Soldier::factory()->create([
            'rank_id' => $rank->id,
            'unit_id' => $unit->id,
        ]);
    }

    public function test_it_can_list_duty_roster()
    {
        DutyRoster::create([
            'soldier_id' => $this->soldier->id,
            'duty_type_id' => $this->dutyType->id,
            'start_time' => now(),
            'end_time' => now()->addHours(4),
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('roster.index'));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_it_can_create_duty_roster_entry()
    {
        $start = now()->addDay()->setHour(8)->setMinute(0);

        $data = [
            'soldier_id' => $this->soldier->id,
            'duty_type_id' => $this->dutyType->id,
            'start_time' => $start->toDateTimeString(),
            'end_time' => $start->copy()->addHours(4)->toDateTimeString(),
            'notes' => 'Пост №1',
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('roster.store'), $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('duty_rosters', [
            'soldier_id' => $this->soldier->id,
            'duty_type_id' => $this->dutyType->id,
        ]);
    }

    public function test_it_cannot_create_duty_roster_entry_with_invalid_data()
    {
        $data = [
            'soldier_id' => 99999,
            'duty_type_id' => '', // Пусто
            'start_time' => 'not-a-date',
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('roster.store'), $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['soldier_id', 'duty_type_id', 'start_time']);
    }

    public function test_it_cannot_access_roster_if_unauthorized()
    {
        $response = $this->getJson(route('roster.index'));
        $response->assertStatus(401);
    }
}
