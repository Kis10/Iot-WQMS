<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('water_readings')->whereNull('turbidity')->update(['turbidity' => 0]);
        DB::table('water_readings')->whereNull('tds')->update(['tds' => 0]);
        DB::table('water_readings')->whereNull('humidity')->update(['humidity' => 0]);

        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            $this->rebuildSqliteTable(true);
            return;
        }

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE water_readings MODIFY turbidity FLOAT NOT NULL');
            DB::statement('ALTER TABLE water_readings MODIFY tds FLOAT NOT NULL');
            DB::statement('ALTER TABLE water_readings MODIFY humidity FLOAT NOT NULL');
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE water_readings ALTER COLUMN turbidity SET NOT NULL');
            DB::statement('ALTER TABLE water_readings ALTER COLUMN tds SET NOT NULL');
            DB::statement('ALTER TABLE water_readings ALTER COLUMN humidity SET NOT NULL');
            return;
        }

        if ($driver === 'sqlsrv') {
            DB::statement('ALTER TABLE water_readings ALTER COLUMN turbidity FLOAT NOT NULL');
            DB::statement('ALTER TABLE water_readings ALTER COLUMN tds FLOAT NOT NULL');
            DB::statement('ALTER TABLE water_readings ALTER COLUMN humidity FLOAT NOT NULL');
            return;
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            $this->rebuildSqliteTable(false);
            return;
        }

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE water_readings MODIFY turbidity FLOAT NULL');
            DB::statement('ALTER TABLE water_readings MODIFY tds FLOAT NULL');
            DB::statement('ALTER TABLE water_readings MODIFY humidity FLOAT NULL');
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE water_readings ALTER COLUMN turbidity DROP NOT NULL');
            DB::statement('ALTER TABLE water_readings ALTER COLUMN tds DROP NOT NULL');
            DB::statement('ALTER TABLE water_readings ALTER COLUMN humidity DROP NOT NULL');
            return;
        }

        if ($driver === 'sqlsrv') {
            DB::statement('ALTER TABLE water_readings ALTER COLUMN turbidity FLOAT NULL');
            DB::statement('ALTER TABLE water_readings ALTER COLUMN tds FLOAT NULL');
            DB::statement('ALTER TABLE water_readings ALTER COLUMN humidity FLOAT NULL');
            return;
        }
    }

    private function rebuildSqliteTable(bool $makeNotNull): void
    {
        Schema::rename('water_readings', 'water_readings_old');

        Schema::create('water_readings', function (Blueprint $table) use ($makeNotNull) {
            $table->id();
            $table->string('device_id');

            if ($makeNotNull) {
                $table->float('turbidity');
                $table->float('tds');
                $table->float('humidity');
            } else {
                $table->float('turbidity')->nullable();
                $table->float('tds')->nullable();
                $table->float('humidity')->nullable();
            }

            $table->float('ph');
            $table->float('temperature')->nullable();
            $table->boolean('no_water_detected')->default(false);
            $table->timestamps();
        });

        DB::statement(
            'INSERT INTO water_readings (id, device_id, turbidity, tds, ph, temperature, humidity, no_water_detected, created_at, updated_at)
             SELECT id, device_id, turbidity, tds, ph, temperature, humidity, no_water_detected, created_at, updated_at
             FROM water_readings_old'
        );

        Schema::drop('water_readings_old');
    }
};
