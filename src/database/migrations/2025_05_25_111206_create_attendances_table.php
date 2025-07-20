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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('users');
            $table->string('status')->default('出勤前');
            $table->date('workDate')->nullable();
            $table->time('clockIn')->nullable();
            $table->Time('clockOut')->nullable();
            $table->decimal('total_restTime',5, 2)->nullable();
            $table->decimal('workTime',5, 2)->nullable();
            $table->string('remark')->nullable();
            $table->timestamps();
            // CHECK(clockOut > clockIn);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
