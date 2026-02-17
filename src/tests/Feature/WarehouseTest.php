<?php

namespace Tests\Feature;

use App\Models\EquipmentType;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WarehouseTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    private $type;

    protected function setUp(): void
    {
        parent::setUp();

        $this->type = EquipmentType::firstOrCreate(['name' => 'Rifle']);

        $this->user = User::factory()->create();
    }

    public function test_it_can_list_warehouse_items()
    {
        Warehouse::factory()->count(3)->create([
            'equipment_type_id' => $this->type->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('warehouses.index'));

        $response->assertStatus(200);
    }

    public function test_it_cannot_list_warehouse_if_unauthorized()
    {
        $response = $this->getJson(route('warehouses.index'));
        $response->assertStatus(401);
    }

    public function test_it_can_show_single_warehouse_item()
    {
        $item = Warehouse::factory()->create(['equipment_type_id' => $this->type->id]);

        $response = $this->actingAs($this->user)
            ->getJson(route('warehouses.show', $item->id));

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $item->id);
    }

    public function test_it_returns_404_if_warehouse_item_not_found()
    {
        $response = $this->actingAs($this->user)
            ->getJson(route('warehouses.show', 99999));
        $response->assertStatus(404);
    }

    public function test_it_can_create_warehouse_item()
    {
        $data = [
            'equipment_type_id' => $this->type->id,
            'status' => 'in_stock',
            'serial_number' => 'ak-12345',
            'quantity' => 10,
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('warehouses.store'), $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('warehouses', [
            'serial_number' => 'ak-12345',
            'status' => 'in_stock',
        ]);
    }

    public function test_it_cannot_create_item_with_invalid_data()
    {
        $data = [
            'status' => 'invalid_status',
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('warehouses.store'), $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['equipment_type_id']);
    }

    public function test_it_can_update_warehouse_item()
    {
        $item = Warehouse::factory()->create([
            'equipment_type_id' => $this->type->id,
            'status' => 'in_stock',
        ]);

        $updateData = [
            'equipment_type_id' => $this->type->id,
            'status' => 'broken',
            'serial_number' => $item->serial_number,
            'quantity' => 5,
        ];

        $response = $this->actingAs($this->user)
            ->putJson(route('warehouses.update', $item->id), $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('warehouses', [
            'id' => $item->id,
            'status' => 'broken',
        ]);
    }

    public function test_it_cannot_update_with_invalid_data()
    {
        $item = Warehouse::factory()->create(['equipment_type_id' => $this->type->id]);

        $response = $this->actingAs($this->user)
            ->putJson(route('warehouses.update', $item->id), ['equipment_type_id' => null]);

        $response->assertStatus(422);
    }

    public function test_it_can_delete_warehouse_item()
    {
        $item = Warehouse::factory()->create(['equipment_type_id' => $this->type->id]);

        $response = $this->actingAs($this->user)
            ->deleteJson(route('warehouses.destroy', $item->id));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('warehouses', ['id' => $item->id]);
    }

    public function test_it_returns_404_when_deleting_missing_item()
    {
        $response = $this->actingAs($this->user)
            ->deleteJson(route('warehouses.destroy', 99999));
        $response->assertStatus(404);
    }

    public function test_it_can_find_item_by_fuzzy_serial_or_name()
    {
        $specialType = EquipmentType::firstOrCreate(['name' => 'Kalashnikov']);

        $item = Warehouse::factory()->create([
            'equipment_type_id' => $specialType->id,
            'serial_number' => 'AK74-SPECIAL',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('warehouses.index', ['search' => 'Kalashnikv']));

        $response->assertStatus(200);

        $this->actingAs($this->user)
            ->getJson(route('warehouses.index', ['search' => 'AK74-SPECAL']));
    }

    public function test_it_does_not_find_item_if_search_query_is_irrelevant()
    {
        Warehouse::factory()->create([
            'equipment_type_id' => $this->type->id,
            'serial_number' => 'SN-12345',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('warehouses.index', ['search' => 'Javelin']));

        $response->assertStatus(200);
    }
}
