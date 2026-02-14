<?php

namespace Tests\Feature;

use App\Models\Rank;
use App\Models\Soldier;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SoldierTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rank = Rank::firstOrCreate(['name' => 'Soldier']);
        $this->unit = Unit::firstOrCreate(['name' => '1-ша рота']);
        $this->user = User::factory()->create();
    }

    public function test_it_can_list_soldiers_successfully()
    {
        Soldier::factory()->count(3)->create([
            'rank_id' => $this->rank->id,
            'unit_id' => $this->unit->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('soldiers.index'));

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_it_cannot_list_soldiers_if_unauthorized()
    {
        $response = $this->getJson(route('soldiers.index'));

        $response->assertStatus(401);
    }
}
