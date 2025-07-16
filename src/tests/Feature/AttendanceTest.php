<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Support\Carbon;

class AttendanceTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_attendance()
    {
        $user = User::find(1);
        $today = Carbon::today()->isoFormat('Y年M月D日(ddd)');

        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
        $response->assertSee($today);
    }

    public function test_attendance_status_1()
    {
        $user1 = User::find(1);

        $response = $this->actingAs($user1)->get('/');
        $response->assertStatus(200);
        $response->assertSeeText('勤務外');
    }

    public function test_attendance_status_2()
    {
        $user2 = User::find(2);

        $response = $this->actingAs($user2)->get('/');
        $response->assertStatus(200);
        $response->assertSeeText('出勤中');
    }

    public function test_attendance_status_3()
    {
        $user3 = User::find(3);

        $response = $this->actingAs($user3)->get('/');
        $response->assertStatus(200);
        $response->assertSeeText('休憩中');
    }

    public function test_attendance_status_4()
    {
        $user4 = User::find(4);

        $response = $this->actingAs($user4)->get('/');
        $response->assertStatus(200);
        dd($response->getContent());
        $response->assertSeeText('退勤済');
    }

}
