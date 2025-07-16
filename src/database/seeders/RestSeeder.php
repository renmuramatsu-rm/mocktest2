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
            'attendance_id' => '4',
            'workDate'      => '2025-05-01',
            'restIn'        => '2025-05-01 12:00:00',
            'restOut'       => '2025-05-01 13:00:00',
            'restTime'      => '1',
        ];
        $Rest = new Rest;
        $Rest->fill($param)->save();

        $param = [
            'attendance_id' => '4',
            'workDate'      => '2025-05-01',
            'restIn'        => '2025-05-01 18:00:00',
            'restOut'       => '2025-05-01 19:00:00',
            'restTime'      => '1',

        ];
        $Rest = new Rest;
        $Rest->fill($param)->save();

        $param = [
            'attendance_id' => '5',
            'workDate'      => '2025-06-01',
            'restIn'        => '2025-06-01 12:00:00',
            'restOut'       => '2025-06-01 13:00:00',
            'restTime'      => '1',

        ];
        $Rest = new Rest;
        $Rest->fill($param)->save();

        $param = [
            'attendance_id' => '6',
            'workDate'      => '2025-06-02',
            'restIn'        => '2025-06-02 12:00:00',
            'restOut'       => '2025-06-02 13:00:00',
            'restTime'      => '1',

        ];
        $Rest = new Rest;
        $Rest->fill($param)->save();

        $param = [
            'attendance_id' => '6',
            'workDate'      => '2025-06-02',
            'restIn'        => '2025-06-02 18:00:00',
            'restOut'       => '2025-06-02 19:00:00',
            'restTime'      => '1',

        ];
        $Rest = new Rest;
        $Rest->fill($param)->save();
    }
}
