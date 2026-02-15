<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WarehouseTest extends TestCase
{
//    use RefreshDatabase;
//
//    private $user;
//    private $type;
//
//    protected function setUp(): void
//    {
//        parent::setUp();
//
//        // Створюємо тип спорядження (АК-74, Бронежилет тощо)
//        $this->type = EquipmentType::firstOrCreate(['name' => 'Rifle']);
//
//        // Створюємо юзера
//        $this->user = User::factory()->create();
//    }
//
//    // --- 1. INDEX (Список) ---
//
//    /** @test */
//    public function it_can_list_warehouse_items()
//    {
//        Warehouse::factory()->count(3)->create([
//            'equipment_type_id' => $this->type->id
//        ]);
//
//        $response = $this->actingAs($this->user)
//            ->getJson(route('warehouses.index'));
//
//        $response->assertStatus(200)
//            ->assertJsonCount(3, 'warehouses.data'); // Зверни увагу: у контролері ключ 'warehouses', а не 'data'
//    }
//
//    /** @test */
//    public function it_cannot_list_warehouse_if_unauthorized()
//    {
//        $response = $this->getJson(route('warehouses.index'));
//        $response->assertStatus(401);
//    }
//
//    // --- 2. SHOW (Перегляд одного) ---
//
//    /** @test */
//    public function it_can_show_single_warehouse_item()
//    {
//        $item = Warehouse::factory()->create(['equipment_type_id' => $this->type->id]);
//
//        $response = $this->actingAs($this->user)
//            ->getJson(route('warehouses.show', $item->id));
//
//        $response->assertStatus(200)
//            ->assertJsonPath('data.id', $item->id);
//    }
//
//    /** @test */
//    public function it_returns_404_if_warehouse_item_not_found()
//    {
//        $response = $this->actingAs($this->user)
//            ->getJson(route('warehouses.show', 99999));
//        $response->assertStatus(404);
//    }
//
//    // --- 3. STORE (Створення) ---
//
//    /** @test */
//    public function it_can_create_warehouse_item()
//    {
//        $data = [
//            'equipment_type_id' => $this->type->id,
//            'status' => 'in_stock',
//            // Додай сюди інші поля, які є у твоїй міграції (serial_number, quantity тощо)
//            // Припускаю, що serial_number є унікальним
//            'serial_number' => 'AK-12345',
//            'quantity' => 10,
//        ];
//
//        $response = $this->actingAs($this->user)
//            ->postJson(route('warehouses.store'), $data);
//
//        $response->assertStatus(201); // Або 200, залежить від контролера
//
//        $this->assertDatabaseHas('warehouses', [
//            'serial_number' => 'AK-12345',
//            'status' => 'in_stock'
//        ]);
//    }
//
//    /** @test */
//    public function it_cannot_create_item_with_invalid_data()
//    {
//        $data = [
//            'status' => 'invalid_status', // Невірний статус
//        ];
//
//        $response = $this->actingAs($this->user)
//            ->postJson(route('warehouses.store'), $data);
//
//        $response->assertStatus(422)
//            ->assertJsonValidationErrors(['equipment_type_id']); // Перевіряємо, що вимагає тип
//    }
//
//    // --- 4. UPDATE (Оновлення) ---
//
//    /** @test */
//    public function it_can_update_warehouse_item()
//    {
//        $item = Warehouse::factory()->create([
//            'equipment_type_id' => $this->type->id,
//            'status' => 'in_stock'
//        ]);
//
//        $updateData = [
//            'equipment_type_id' => $this->type->id,
//            'status' => 'broken', // Змінюємо статус
//            'serial_number' => $item->serial_number,
//            'quantity' => 5
//        ];
//
//        $response = $this->actingAs($this->user)
//            ->putJson(route('warehouses.update', $item->id), $updateData);
//
//        $response->assertStatus(200);
//
//        $this->assertDatabaseHas('warehouses', [
//            'id' => $item->id,
//            'status' => 'broken'
//        ]);
//    }
//
//    /** @test */
//    public function it_cannot_update_with_invalid_data()
//    {
//        $item = Warehouse::factory()->create(['equipment_type_id' => $this->type->id]);
//
//        $response = $this->actingAs($this->user)
//            ->putJson(route('warehouses.update', $item->id), ['equipment_type_id' => null]);
//
//        $response->assertStatus(422);
//    }
//
//    // --- 5. DELETE (Видалення) ---
//
//    /** @test */
//    public function it_can_delete_warehouse_item()
//    {
//        $item = Warehouse::factory()->create(['equipment_type_id' => $this->type->id]);
//
//        $response = $this->actingAs($this->user)
//            ->deleteJson(route('warehouses.destroy', $item->id));
//
//        $response->assertStatus(200); // або 204
//        $this->assertDatabaseMissing('warehouses', ['id' => $item->id]);
//    }
//
//    /** @test */
//    public function it_returns_404_when_deleting_missing_item()
//    {
//        $response = $this->actingAs($this->user)
//            ->deleteJson(route('warehouses.destroy', 99999));
//        $response->assertStatus(404);
//    }
//
//    // --- 6. SEARCH (Elasticsearch) ---
//
//    /** @test */
//    public function it_can_find_item_by_fuzzy_serial_or_name()
//    {
//        // Створюємо айтем з типом "Kalashnikov" (припустимо, ім'я береться з типу або поля name)
//        // Якщо у Warehouse є поле 'name' або воно шукає по 'equipment_type.name', адаптуй це.
//        // Припускаю, що шукаємо по серійнику або назві типу.
//
//        $specialType = EquipmentType::firstOrCreate(['name' => 'Kalashnikov']);
//
//        $item = Warehouse::factory()->create([
//            'equipment_type_id' => $specialType->id,
//            'serial_number' => 'AK74-SPECIAL'
//        ]);
//
//        // Чекаємо індексації
//        sleep(2);
//
//        // Шукаємо з помилкою: "Kalashnikv" (без 'o')
//        // Твій сервіс шукає по 'searchQuery', який будується з input
//        $response = $this->actingAs($this->user)
//            ->getJson(route('warehouses.index', ['search' => 'Kalashnikv']));
//
//        $response->assertStatus(200);
//
//        // Тут ми перевіряємо, чи повернувся наш запис.
//        // Важливо: Scout може шукати по Type Name, якщо ти налаштував toSearchableArray().
//        // Якщо ні - шукай по серійнику: 'AK74-SPECAL' (без I)
//
//        // Для надійності, давай перевіримо серійник, бо він точно в таблиці Warehouse
//        $response = $this->actingAs($this->user)
//            ->getJson(route('warehouses.index', ['search' => 'AK74-SPECAL'])); // Помилка в серійнику
//
//        $response->assertJsonCount(1, 'warehouses.data')
//            ->assertJsonPath('warehouses.data.0.id', $item->id);
//    }
//
//    /** @test */
//    public function it_does_not_find_item_if_search_query_is_irrelevant()
//    {
//        Warehouse::factory()->create([
//            'equipment_type_id' => $this->type->id,
//            'serial_number' => 'SN-12345'
//        ]);
//
//        sleep(2);
//
//        $response = $this->actingAs($this->user)
//            ->getJson(route('warehouses.index', ['search' => 'Javelin']));
//
//        $response->assertStatus(200)
//            ->assertJsonCount(0, 'warehouses.data');
//    }
}
