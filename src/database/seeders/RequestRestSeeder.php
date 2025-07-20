<?php

namespace Database\Seeders;

use App\Models\RequestRest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RequestRestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $param = [
            'attendance_correction_request_id' => '1',
            'workDate' => '2025-07-10',
            'request_restIn'        => '2025-07-10 12:00:00',
            'request_restOut'       => '2025-07-10 13:00:00',
        ];

        $AttendanceCorrectionRequest = new RequestRest;
        $AttendanceCorrectionRequest->fill($param)->save();

        $param = [
            'attendance_correction_request_id' => '2',
            'workDate' => '2025-07-15',
            'request_restIn'        => '2025-07-15 18:00:00',
            'request_restOut'       => '2025-07-15 19:00:00',
        ];

        $AttendanceCorrectionRequest = new RequestRest;
        $AttendanceCorrectionRequest->fill($param)->save();
    }

}
