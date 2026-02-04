<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('water_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('water_reading_id')->constrained()->onDelete('cascade');
            $table->string('analysis_type'); // 'safety', 'quality', 'trend'
            $table->text('ai_insight');
            $table->string('risk_level'); // 'low', 'medium', 'high', 'critical'
            $table->json('recommendations');
            $table->decimal('confidence_score', 5, 2); // AI confidence percentage
            $table->timestamp('analyzed_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_analyses');
    }
};
