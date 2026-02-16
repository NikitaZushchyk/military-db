<?php

namespace Tests\Feature;

use App\Models\Rank;
use App\Models\Soldier;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
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
            ->assertJsonCount(0, 'data');
    }

    public function test_it_cannot_list_soldiers_if_unauthorized()
    {
        $response = $this->getJson(route('soldiers.index'));

        $response->assertStatus(401);
    }

    public function test_it_can_show_single_soldier()
    {
        $soldier = Soldier::factory()->create([
            'rank_id' => $this->rank->id,
            'unit_id' => $this->unit->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('soldiers.show', $soldier->id));

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $soldier->id);
    }

    public function test_it_returns_404_if_soldier_not_found()
    {
        $response = $this->actingAs($this->user)
            ->getJson(route('soldiers.show', 99999));

        $response->assertStatus(404);
    }

    public function test_it_can_create_soldier_and_dispatch_job()
    {
        Queue::fake();

        $data = [
            'first_name' => 'Taras',
            'last_name' => 'Shevchenko',
            'rank_id' => $this->rank->id,
            'unit_id' => $this->unit->id,
            'status' => 'active',
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('soldiers.store'), $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('soldiers', [
            'last_name' => 'Shevchenko',
            'status' => 'active',
        ]);
    }

    public function test_it_cannot_create_soldier_with_invalid_data()
    {
        $data = [
            'first_name' => '',
            'last_name' => '',
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('soldiers.store'), $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['first_name', 'last_name', 'rank_id', 'unit_id']);
    }

    public function test_it_can_update_soldier()
    {
        $soldier = Soldier::factory()->create([
            'first_name' => 'OldName',
            'rank_id' => $this->rank->id,
            'unit_id' => $this->unit->id,
        ]);

        $updateData = [
            'first_name' => 'NewName',
            'last_name' => $soldier->last_name,
            'rank_id' => $this->rank->id,
            'unit_id' => $this->unit->id,
            'status' => 'active',
        ];

        $response = $this->actingAs($this->user)
            ->putJson(route('soldiers.update', $soldier->id), $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('soldiers', [
            'id' => $soldier->id,
            'first_name' => 'NewName',
        ]);
    }

    public function test_it_cannot_update_soldier_with_invalid_email_or_data()
    {
        $soldier = Soldier::factory()->create([
            'rank_id' => $this->rank->id,
            'unit_id' => $this->unit->id,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson(route('soldiers.update', $soldier->id), []);

        $response->assertStatus(422);
    }

    public function test_it_can_delete_soldier()
    {
        $soldier = Soldier::factory()->create([
            'rank_id' => $this->rank->id,
            'unit_id' => $this->unit->id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson(route('soldiers.delete', $soldier->id));

        $response->assertStatus(200);

        $this->assertDatabaseMissing('soldiers', ['id' => $soldier->id]);
    }

    public function test_it_returns_404_when_deleting_non_existent_soldier()
    {
        $response = $this->actingAs($this->user)
            ->deleteJson(route('soldiers.delete', 99999));

        $response->assertStatus(404);
    }

    public function test_it_can_find_soldier_by_fuzzy_name_search()
    {
        Soldier::factory()->create([
            'first_name' => 'Olexandr',
            'last_name' => 'Zaluzhnyi',
            'rank_id' => $this->rank->id,
            'unit_id' => $this->unit->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('soldiers.index', ['search' => 'Zaluzni']));

        $response->assertStatus(200);
    }

    public function test_it_does_not_find_soldier_if_too_many_typos()
    {
        Soldier::factory()->create([
            'last_name' => 'Petrenko',
            'rank_id' => $this->rank->id,
            'unit_id' => $this->unit->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('soldiers.index', ['search' => 'Ivanov']));

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }
}
