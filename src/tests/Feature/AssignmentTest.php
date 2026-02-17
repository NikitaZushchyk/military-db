<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\EquipmentType;
use App\Models\Rank;
use App\Models\Soldier;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignmentTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    private $soldier;

    private $warehouseItem;

    protected function setUp(): void
    {
        parent::setUp();
        $rank = Rank::firstOrCreate(['name' => 'Солдат']);
        $unit = Unit::firstOrCreate(['name' => '1-ша рота']);
        $equipType = EquipmentType::firstOrCreate(['name' => 'AK-74']);

        $this->user = User::factory()->create();
        $this->soldier = Soldier::factory()->create([
            'rank_id' => $rank->id,
            'unit_id' => $unit->id,
            'status' => 'active',
        ]);
        $this->warehouseItem = Warehouse::factory()->create([
            'equipment_type_id' => $equipType->id,
            'status' => 'in_stock',
        ]);
    }

    public function test_it_can_issue_equipment_successfully()
    {
        $data = [
            'soldier_id' => $this->soldier->id,
            'warehouse_id' => $this->warehouseItem->id,
            'issue_date' => now()->toDateTimeString(),
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('assignments.issue'), $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('assignments', [
            'soldier_id' => $this->soldier->id,
            'warehouse_id' => $this->warehouseItem->id,
        ]);

        $this->assertDatabaseHas('warehouses', [
            'id' => $this->warehouseItem->id,
            'status' => 'issued',
        ]);
    }

    public function test_it_can_return_equipment_successfully()
    {
        Assignment::create([
            'soldier_id' => $this->soldier->id,
            'warehouse_id' => $this->warehouseItem->id,
            'issue_date' => now(),
        ]);

        $this->warehouseItem->update(['status' => 'issued']);

        $data = [
            'warehouse_id' => $this->warehouseItem->id,
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('assignments.return'), $data);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('assignments', [
            'warehouse_id' => $this->warehouseItem->id,
            'return_date' => null,
        ]);

        $this->assertDatabaseHas('warehouses', [
            'id' => $this->warehouseItem->id,
            'status' => 'in_stock',
        ]);
    }

    public function test_it_can_list_assignment_history()
    {
        Assignment::create([
            'soldier_id' => $this->soldier->id,
            'warehouse_id' => $this->warehouseItem->id,
            'issue_date' => now(),
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('assignments.index'));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_it_can_list_active_assignments_only()
    {
        Assignment::create([
            'soldier_id' => $this->soldier->id,
            'warehouse_id' => $this->warehouseItem->id,
            'issue_date' => now(),
            'return_date' => null,
        ]);

        $anotherItem = Warehouse::factory()->create(['equipment_type_id' => $this->warehouseItem->equipment_type_id]);

        Assignment::create([
            'soldier_id' => $this->soldier->id,
            'warehouse_id' => $anotherItem->id,
            'issue_date' => now()->subDays(2),
            'return_date' => now()->subDay(),
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('assignments.active'));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_it_cannot_issue_equipment_with_invalid_data()
    {
        $data = [
            'soldier_id' => 99999,
            'warehouse_id' => '',
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('assignments.issue'), $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['soldier_id', 'warehouse_id']);
    }

    public function test_it_cannot_return_equipment_that_was_not_issued()
    {
        $data = [
            'warehouse_id' => $this->warehouseItem->id,
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('assignments.return'), $data);

        $response->assertStatus(422);
    }

    public function test_it_cannot_issue_non_existent_equipment()
    {
        $data = [
            'soldier_id' => $this->soldier->id,
            'warehouse_id' => 999999,
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('assignments.issue'), $data);

        $response->assertStatus(422);
    }

    public function test_it_cannot_access_assignments_if_unauthorized()
    {
        $response = $this->getJson(route('assignments.index'));
        $response->assertStatus(401);
    }
}
