<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sensor_readings', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->nullable();
            $table->decimal('voltage', 10, 2);
            $table->decimal('current', 10, 3);
            $table->decimal('power', 10, 2);
            $table->decimal('energy', 12, 3);
            $table->decimal('frequency', 8, 2)->nullable();
            $table->decimal('power_factor', 5, 2)->nullable();
            $table->string('status')->default('normal');
            $table->timestamp('recorded_at')->index();
            $table->json('raw_payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sensor_readings');
    }
};
