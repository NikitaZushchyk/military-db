<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\DutyType;
use App\Models\EquipmentType;
use App\Models\Rank;
use App\Models\Soldier;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $ranks = [
            'Солдат', 'Старший солдат', 'Молодший сержант', 'Сержант',
            'Головний сержант', 'Лейтенант', 'Капітан', 'Майор'
        ];
        foreach ($ranks as $rank) {
            Rank::create(['name' => $rank]);
        }

        $units = ['1-ша рота', '2-га рота', 'Взвод забезпечення', 'Розвідвзвод', 'Штаб'];
        foreach ($units as $unit) {
            Unit::create(['name' => $unit]);
        }

        $weaponTypes = ['АК-74', 'ПМ', 'СВД', 'РПГ-7', 'Бронежилет Корсар', 'Шолом'];
        foreach ($weaponTypes as $type) {
            EquipmentType::create(['name' => $type]);
        }

        $duties = ['КПП', 'Патруль території', 'Черговий по роті', 'Кухня'];
        foreach ($duties as $duty) {
            DutyType::create(['name' => $duty]);
        }

        Soldier::factory(50)->make()->each(function ($soldier) {
            $soldier->rank_id = Rank::inRandomOrder()->first()->id;
            $soldier->unit_id = Unit::inRandomOrder()->first()->id;
            $soldier->save();
        });

        Warehouse::factory(100)->make()->each(function ($item) {
            $item->equipment_type_id = EquipmentType::inRandomOrder()->first()->id;
            $item->save();
        });

        $soldiers = Soldier::take(10)->get();
        $items = Warehouse::where('status', 'in_stock')->take(10)->get();

        foreach ($soldiers as $index => $soldier) {
            if (isset($items[$index])) {
                Assignment::create([
                    'soldier_id' => $soldier->id,
                    'warehouse_id' => $items[$index]->id,
                    'issue_date' => now(),
                ]);
            }
        }

        User::factory()->create([
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
    }
}
