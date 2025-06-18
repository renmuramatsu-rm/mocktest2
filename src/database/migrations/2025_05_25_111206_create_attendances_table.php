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
            $table->dateTime('clockIn')->nullable();
            $table->dateTime('clockOut')->nullable();
            $table->dateTime('breakIn')->nullable();
            $table->dateTime('breakOut')->nullable();
            $table->integer('breakTime')->nullable();
            $table->integer('workTime')->nullable();
            $table->string('remark')->nullable();
            $table->timestamps();
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
