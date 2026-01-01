<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\DutyRoster;
use App\Models\DutyType;
use App\Models\EquipmentType;
use App\Models\Rank;
use App\Models\Soldier;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;

class   DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $ranks = ['Солдат', 'Старший солдат', 'Молодший сержант', 'Сержант', 'Головний сержант', 'Лейтенант', 'Капітан', 'Майор'];
        foreach ($ranks as $rank) Rank::firstOrCreate(['name' => $rank]);

        $units = ['1-ша рота', '2-га рота', 'Взвод забезпечення', 'Розвідвзвод', 'Штаб'];
        foreach ($units as $unit) Unit::firstOrCreate(['name' => $unit]);

        $weaponTypes = ['АК-74', 'ПМ', 'СВД', 'РПГ-7', 'Бронежилет Корсар', 'Шолом', 'Рація Motorola'];
        foreach ($weaponTypes as $type) EquipmentType::firstOrCreate(['name' => $type]);

        $duties = ['КПП', 'Патруль території', 'Черговий по роті', 'Кухня', 'Варта'];
        foreach ($duties as $duty) DutyType::firstOrCreate(['name' => $duty]);

        $soldiers = Soldier::factory(50)
            ->state(new Sequence(
                ['status' => 'active'],
                ['status' => 'active'],
                ['status' => 'hospital'],
                ['status' => 'vacation'],
                ['status' => 'fired'],
            ))
            ->create([
                'rank_id' => fn() => Rank::inRandomOrder()->first()->id,
                'unit_id' => fn() => Unit::inRandomOrder()->first()->id,
            ]);

        $items = Warehouse::factory(100)
            ->state(new Sequence(
                ['status' => 'in_stock'],
                ['status' => 'in_stock'],
                ['status' => 'issued'],
                ['status' => 'broken'],
            ))
            ->create([
                'equipment_type_id' => fn() => EquipmentType::inRandomOrder()->first()->id,
            ]);

        $activeSoldiers = $soldiers->where('status', 'active')->take(30)->values();
        $activeItems = $items->where('status', 'in_stock')->take(30)->values();

        foreach ($activeSoldiers as $index => $soldier) {
            $item = $activeItems[$index];

            Assignment::create([
                'soldier_id' => $soldier->id,
                'warehouse_id' => $item->id,
                'issue_date' => now()->subDays(rand(1, 30)),
            ]);
            $item->update(['status' => 'issued']);
        }

        $dutyTypes = DutyType::all();
        foreach ($soldiers->where('status', 'active') as $soldier) {
            if (rand(1, 5) == 1) {
                $start = now()->addDays(rand(-5, 5))->setHour(rand(8, 20))->setMinute(0);
                DutyRoster::create([
                    'soldier_id' => $soldier->id,
                    'duty_type_id' => $dutyTypes->random()->id,
                    'start_time' => $start,
                    'end_time' => $start->copy()->addHours(4),
                ]);
            }
        }

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@military.com',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
    }
}
