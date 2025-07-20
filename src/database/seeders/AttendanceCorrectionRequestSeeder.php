<?php

namespace Database\Seeders;

use App\Models\AttendanceCorrectionRequest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttendanceCorrectionRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $param = [
            'attendance_id' => '10',
            'user_id' => '2',
            'workDate' => '2025-07-10',
            'requested_clockIn'        => '2025-07-10 08:00:00',
            'requested_clockOut'       => '2025-07-10 17:00:00',
            'remark' => '休憩のため',
            'status' => 'pending',
        ];

        $AttendanceCorrectionRequest = new AttendanceCorrectionRequest;
        $AttendanceCorrectionRequest->fill($param)->save();

        $param = [
            'attendance_id' => '11',
            'user_id' => '2',
            'workDate' => '2025-07-15',
            'requested_clockIn'        => '2025-07-15 10:00:00',
            'requested_clockOut'       => '2025-07-15 22:00:00',
            'remark' => '休憩のため',
            'status' => 'approved',
        ];

        $AttendanceCorrectionRequest = new AttendanceCorrectionRequest;
        $AttendanceCorrectionRequest->fill($param)->save();
    }
}
