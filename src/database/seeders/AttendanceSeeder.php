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
            'clockIn'     => '08:00',
        ];
        $Attendance = new Attendance;
        $Attendance->fill($param)->save();

        $param = [
            'employee_id' => '3',
            'status'      => '休憩中',
            'workDate'    => Carbon::now(),
            'clockIn'     => '08:00',
        ];
        $Attendance = new Attendance;
        $Attendance->fill($param)->save();

        $param = [
            'employee_id'    => '4',
            'status'         => '退勤済',
            'workDate'       => Carbon::now(),
            'clockIn'        => '08:00',
            'clockOut'       => '17:00',
            'total_restTime' => '1.0',
            'workTime'       => '8.0',

        ];
        $Attendance = new Attendance;
        $Attendance->fill($param)->save();

        $param = [
            'employee_id'    => '5',
            'status'         => '退勤済',
            'workDate'       => Carbon::yesterday(),
            'clockIn'        => '08:00',
            'clockOut'       => '17:00',
            'total_restTime' => '1.0',
            'workTime'       => '8.0',

        ];
        $Attendance = new Attendance;
        $Attendance->fill($param)->save();

        $param = [
            'employee_id'    => '2',
            'status'         => '退勤済',
            'workDate'       => '2025-05-01',
            'clockIn'        => '2025-05-01 09:00:00',
            'clockOut'       => '2025-05-01 22:00:00',
            'total_restTime' => '0.33',
            'workTime'       => '8.5',

        ];
        $Attendance = new Attendance;
        $Attendance->fill($param)->save();

        $param = [
            'employee_id'    => '2',
            'status'         => '退勤済',
            'workDate'       => '2025-06-01',
            'clockIn'        => '2025-06-01 09:00:00',
            'clockOut'       => '2025-06-01 22:00:00',
            'total_restTime' => '0.42',
            'workTime'       => '8.5',

        ];
        $Attendance = new Attendance;
        $Attendance->fill($param)->save();

        $param = [
            'employee_id'    => '2',
            'status'         => '退勤済',
            'workDate'       => '2025-06-02',
            'clockIn'        => '2025-06-02 09:00:00',
            'clockOut'       => '2025-06-02 22:00:00',
            'total_restTime' => '2.37',
            'workTime'       => '10.03',

        ];
        $Attendance = new Attendance;
        $Attendance->fill($param)->save();

        $param = [
            'employee_id'    => '2',
            'status'         => '退勤済',
            'workDate'       => '2025-07-05',
            'clockIn'        => '2025-07-05 08:00:00',
            'clockOut'       => '2025-07-05 17:00:00',
            'total_restTime' => '2.0',
            'workTime'       => '8.0',

        ];
        $Attendance = new Attendance;
        $Attendance->fill($param)->save();

        $param = [
            'employee_id'    => '2',
            'status'         => '退勤済',
            'workDate'       => '2025-08-04',
            'clockIn'        => '2025-08-04 08:00:00',
            'clockOut'       => '2025-08-04 17:00:00',
            'total_restTime' => '2.0',
            'workTime'       => '8.0',

        ];
        $Attendance = new Attendance;
        $Attendance->fill($param)->save();

        $param = [
            'employee_id'    => '2',
            'status'         => '退勤済',
            'workDate'       => '2025-07-10',
            'clockIn'        => '2025-07-10 08:00:00',
            'clockOut'       => '2025-07-10 17:00:00',
            'total_restTime' => '2.0',
            'workTime'       => '8.0',

        ];
        $Attendance = new Attendance;
        $Attendance->fill($param)->save();

        $param = [
            'employee_id'    => '2',
            'status'         => '退勤済',
            'workDate'       => '2025-07-15',
            'clockIn'        => '2025-07-15 10:00:00',
            'clockOut'       => '2025-07-15 22:00:00',
            'total_restTime' => '2.0',
            'workTime'       => '8.5',

        ];
        $Attendance = new Attendance;
        $Attendance->fill($param)->save();
    }
}
