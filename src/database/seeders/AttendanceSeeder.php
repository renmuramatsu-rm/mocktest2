<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use Illuminate\Support\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $param = [
            'employee_id' => '2',
            'status'      => '出勤中',
            'workDate'    => Carbon::now(),
            'clockIn'     => Carbon::now(),

        ];
        $Attendance = new Attendance;
        $Attendance->fill($param)->save();

        $param = [
            'employee_id' => '3',
            'status'      => '休憩中',
            'workDate'    => Carbon::now(),
            'clockIn'     => Carbon::now(),

        ];
        $Attendance = new Attendance;
        $Attendance->fill($param)->save();

        $param = [
            'employee_id'    => '4',
            'status'         => '退勤済',
            'workDate'       => Carbon::now(),
            'clockIn'        => Carbon::now(),
            'clockOut'       => Carbon::now(),
            'total_restTime' => '2',
            'workTime'       => '8',

        ];
        $Attendance = new Attendance;
        $Attendance->fill($param)->save();

        $param = [
            'employee_id'    => '2',
            'status'         => '退勤済',
            'workDate'       => '2025-05-01',
            'clockIn'        => '2025-05-01 09:00:00',
            'clockOut'       => '2025-05-01 22:00:00',
            'total_restTime' => '2',
            'workTime'       => '8',

        ];
        $Attendance = new Attendance;
        $Attendance->fill($param)->save();

        $param = [
            'employee_id'    => '2',
            'status'         => '退勤済',
            'workDate'       => '2025-06-01',
            'clockIn'        => '2025-06-01 09:00:00',
            'clockOut'       => '2025-06-01 22:00:00',
            'total_restTime' => '2',
            'workTime'       => '8',

        ];
        $Attendance = new Attendance;
        $Attendance->fill($param)->save();

        $param = [
            'employee_id'    => '2',
            'status'         => '退勤済',
            'workDate'       => '2025-06-02',
            'clockIn'        => '2025-06-02 09:00:00',
            'clockOut'       => '2025-06-02 22:00:00',
            'total_restTime' => '2',
            'workTime'       => '8',

        ];
        $Attendance = new Attendance;
        $Attendance->fill($param)->save();
    }
}
