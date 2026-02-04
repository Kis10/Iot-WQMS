<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('water_readings')->whereNull('turbidity')->update(['turbidity' => 0]);
        DB::table('water_readings')->whereNull('tds')->update(['tds' => 0]);
        DB::table('water_readings')->whereNull('humidity')->update(['humidity' => 0]);

        DB::statement('ALTER TABLE water_readings MODIFY turbidity FLOAT NOT NULL');
        DB::statement('ALTER TABLE water_readings MODIFY tds FLOAT NOT NULL');
        DB::statement('ALTER TABLE water_readings MODIFY humidity FLOAT NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE water_readings MODIFY turbidity FLOAT NULL');
        DB::statement('ALTER TABLE water_readings MODIFY tds FLOAT NULL');
        DB::statement('ALTER TABLE water_readings MODIFY humidity FLOAT NULL');
    }
};
