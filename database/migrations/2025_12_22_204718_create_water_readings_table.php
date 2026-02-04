<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('water_readings', function (Blueprint $table) {
            $table->id();
            $table->string('device_id');
            $table->float('turbidity');
            $table->float('tds');
            $table->float('ph');
            $table->float('temperature')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('water_readings');
    }
};

