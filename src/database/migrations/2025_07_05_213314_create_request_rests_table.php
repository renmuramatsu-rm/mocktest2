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
        Schema::create('request_rests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_correction_request_id')->constrained('attendance_correction_requests')->onDelete('cascade');
            $table->date('workDate')->nullable();
            $table->Time('request_restIn')->nullable();
            $table->Time('request_restOut')->nullable();
            $table->decimal('request_restTime', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_rests');
    }
};
