<?php

namespace Tests\Feature;

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

    // 日時取得機能
    public function test_attendance()
    {
        $user = User::find(1);
        $today = Carbon::today()->isoFormat('Y年M月D日(ddd)');
        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
        $response->assertSee($today);
    }

    // ステータス確認機能
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
        $response->assertSeeText('退勤済');
    }

    // 出勤機能
    public function test_attendance_clockIn()
    {
        $user1 = User::find(1);
        $response = $this->actingAs($user1)->get('/');
        $response->assertStatus(200);
        $response->assertSeeText('出勤');

        $clockInResponse = $this->actingAs($user1)->post('/attendance/clock-in');
        $afterResponse = $this->actingAs($user1)->get('/');
        $afterResponse->assertStatus(200);
        $afterResponse->assertSeeText('出勤中');
    }

    public function test_attendance_clockIn_cant()
    {
        $user3 = User::find(3);
        $response = $this->actingAs($user3)->get('/');
        $response->assertStatus(200);
        $response->assertDontSeeText('出勤中');
    }

    // 休憩機能
    public function test_attendance_restIn()
    {
        $user2 = User::find(2);
        $response = $this->actingAs($user2)->get('/');
        $response->assertStatus(200);
        $response->assertSeeText('休憩入');

        $restInResponse = $this->actingAs($user2)->post('/attendance/restIn');
        $afterResponse = $this->actingAs($user2)->get('/');
        $afterResponse->assertStatus(200);
        $afterResponse->assertSeeText('休憩中');
    }

    public function test_attendance_restIn_many()
    {
        $user2 = User::find(2);
        $response = $this->actingAs($user2)->get('/');
        $response->assertStatus(200);
        $response->assertSeeText('休憩入');

        $restInResponse = $this->actingAs($user2)->post('/attendance/restIn');
        $restOutResponse = $this->actingAs($user2)->post('/attendance/restOut');
        $afterResponse = $this->actingAs($user2)->get('/');
        $afterResponse->assertStatus(200);
        $afterResponse->assertSeeText('休憩入');
    }

    public function test_attendance_restOut()
    {
        $user2 = User::find(2);
        $response = $this->actingAs($user2)->get('/');
        $restInResponse = $this->actingAs($user2)->post('/attendance/restIn');
        $response = $this->actingAs($user2)->get('/');
        $response->assertStatus(200);
        $response->assertSeeText('休憩戻');

        $restOutResponse = $this->actingAs($user2)->post('/attendance/restOut');
        $afterResponse = $this->actingAs($user2)->get('/');
        $afterResponse->assertStatus(200);
        $afterResponse->assertSeeText('出勤中');
    }

    public function test_attendance_restOut_many()
    {
        $user2 = User::find(2);
        $response = $this->actingAs($user2)->get('/');
        $restInResponse = $this->actingAs($user2)->post('/attendance/restIn');
        $restOutResponse = $this->actingAs($user2)->post('/attendance/restOut');
        $restInResponse = $this->actingAs($user2)->post('/attendance/restIn');
        $afterResponse = $this->actingAs($user2)->get('/');
        $afterResponse->assertStatus(200);
        $afterResponse->assertSeeText('休憩戻');
    }

    public function test_attendance_restTime_one_hour()
    {
        $user = User::find(2);

        Carbon::setTestNow(Carbon::createFromTime(9, 0, 0));
        $this->actingAs($user)->post('/attendance/clockIn');
        Carbon::setTestNow(Carbon::createFromTime(12, 0, 0));
        $this->actingAs($user)->post('/attendance/restIn');
        Carbon::setTestNow(Carbon::createFromTime(13, 0, 0));
        $this->actingAs($user)->post('/attendance/restOut');
        Carbon::setTestNow(Carbon::createFromTime(18, 0, 0));
        $this->actingAs($user)->post('/attendance/clockOut');
        $response = $this->actingAs($user)->get('/attendance/list');
        $response->assertStatus(200);
        $response->assertSeeText('01:00');
        Carbon::setTestNow();
    }

    // 退勤機能
    public function test_attendance_clockOut()
    {
        $user2 = User::find(2);
        $response = $this->actingAs($user2)->get('/');
        $response->assertStatus(200);
        $response->assertSeeText('退勤');

        $clockOutResponse = $this->actingAs($user2)->post('/attendance/clock-out');
        $afterResponse = $this->actingAs($user2)->get('/');
        $afterResponse->assertStatus(200);
        $afterResponse->assertSeeText('退勤済');
    }

    public function test_attendance_clockIn_Out()
    {
        $user4 = User::find(5);

        $response = $this->actingAs($user4)->get('/');
        $clockInResponse = $this->actingAs($user4)->post('/attendance/clock-in');
        $clockOut = now()->setTime(18, 0);
        Carbon::setTestNow($clockOut);
        $clockOutResponse = $this->actingAs($user4)->post('/attendance/clock-out');
        $afterResponse = $this->actingAs($user4)->get('/attendance/list');
        $afterResponse->assertStatus(200);
        $formattedClockOut = $clockOut->format('H:i');
        $afterResponse->assertSeeText($formattedClockOut);
    }

    // 勤怠一覧情報取得機能（一般ユーザー）
    public function test_attendance_list()
    {
        $user2 = User::find(2);
        $response = $this->actingAs($user2)->get('/attendance/list');
        $response->assertStatus(200);
        $response->assertViewHas('month_day_lists');
    }

    // 勤怠一覧情報取得機能（一般ユーザー） 前月表示
    public function test_attendance_list_lastMonth()
    {
        $user2 = User::find(2);
        $response = $this->actingAs($user2)->get('/attendance/list/lastMonth');
        $response->assertStatus(200);
        $response->assertViewHas('month_day_lists');
    }

    // 勤怠一覧情報取得機能（一般ユーザー） 翌月表示
    public function test_attendance_list_nextMonth()
    {
        $user2 = User::find(2);
        $response = $this->actingAs($user2)->get('/attendance/list/nextMonth');
        $response->assertStatus(200);
        $response->assertViewHas('month_day_lists');
    }
    // 勤怠一覧情報取得機能（一般ユーザー） 詳細表示
    public function test_attendance_list_detail()
    {
        $user2 = User::find(2);
        $response = $this->actingAs($user2)->get('/attendance/8');
        $response->assertStatus(200);
        $response->assertViewHas('attendance');
    }
}
