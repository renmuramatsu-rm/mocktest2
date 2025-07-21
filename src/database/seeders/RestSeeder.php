<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rest;
use Illuminate\Support\Carbon;

class RestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $param = [
            'attendance_id' => '2',
            'workDate'      => Carbon::now(),
            'restIn'        => Carbon::now(),
        ];
        $Rest = new Rest;
        $Rest->fill($param)->save();

        $param = [
            'attendance_id' => '5',
            'workDate'      => '2025-05-01',
            'restIn'        => '2025-05-01 12:00:00',
            'restOut'       => '2025-05-01 14:00:00',
            'restTime'      => '2.0',
        ];
        $Rest = new Rest;
        $Rest->fill($param)->save();

        $param = [
            'attendance_id' => '5',
            'workDate'      => '2025-05-01',
            'restIn'        => '2025-05-01 17:35:00',
            'restOut'       => '2025-05-01 19:00:00',
            'restTime'      => '1.4',
        ];
        $Rest = new Rest;
        $Rest->fill($param)->save();

        $param = [
            'attendance_id' => '6',
            'workDate'      => '2025-06-01',
            'restIn'        => '2025-06-01 11:57:00',
            'restOut'       => '2025-06-01 15:00:00',
            'restTime'      => '3.3',
        ];
        $Rest = new Rest;
        $Rest->fill($param)->save();

        $param = [
            'attendance_id' => '7',
            'workDate'      => '2025-06-02',
            'restIn'        => '2025-06-02 12:00:00',
            'restOut'       => '2025-06-02 12:25:00',
            'restTime'      => '0.5',
        ];
        $Rest = new Rest;
        $Rest->fill($param)->save();

        $param = [
            'attendance_id' => '7',
            'workDate'      => '2025-06-02',
            'restIn'        => '2025-06-02 17:30:00',
            'restOut'       => '2025-06-02 19:00:00',
            'restTime'      => '0.6',
        ];
        $Rest = new Rest;
        $Rest->fill($param)->save();

        $param = [
            'attendance_id' => '8',
            'workDate'      => '2025-07-05',
            'restIn'        => '2025-07-05 12:30:00',
            'restOut'       => '2025-07-05 13:00:00',
            'restTime'      => '0.5',
        ];
        $Rest = new Rest;
        $Rest->fill($param)->save();
    }
}
