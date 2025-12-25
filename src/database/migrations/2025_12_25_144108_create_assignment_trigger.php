<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new  class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared('
        CREATE TRIGGER after_assignment_insert
        AFTER INSERT ON assignments
        FOR EACH ROW
        BEGIN
            UPDATE warehouses
            SET status = "issued"
            WHERE id = NEW.warehouse_id;

            INSERT INTO logs (action, description, created_at)
            VALUES (
                "WEAPON_ISSUED",
                CONCAT("Soldier ID ", NEW.soldier_id, " took Item ID ", NEW.warehouse_id),
                NOW()
            );
        END
    ');
        DB::unprepared('
            CREATE TRIGGER after_assignment_update
            AFTER UPDATE ON assignments
            FOR EACH ROW
            BEGIN
                IF OLD.return_date IS NULL AND NEW.return_date IS NOT NULL THEN

                    UPDATE warehouses
                    SET status = "in_stock"
                    WHERE id = NEW.warehouse_id;

                    INSERT INTO logs (action, description, created_at)
                    VALUES (
                        "WEAPON_RETURNED",
                        CONCAT("Soldier ID ", NEW.soldier_id, " returned Item ID ", NEW.warehouse_id),
                        NOW()
                    );
                END IF;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS after_assignment_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS after_assignment_update');
    }
};
