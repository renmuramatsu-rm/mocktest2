<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Support\Carbon;
use App\Models\Attendance;
use App\Models\User;

class AdminAttendanceTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    // 勤怠一覧情報取得機能（管理者）
    public function test_admin_attendance_list()
    {
        $adminUser = AdminUser::find(1);
        $today = Carbon::today()->Format('Y年m月d日');
        $response = $this->actingAs($adminUser, 'admin')->get('/admin/attendance/list');
        $response->assertStatus(200);
        $response->assertViewHas('attendances');
        $response->assertSee($today);
    }

    // 勤怠一覧情報取得機能（管理者） 前日表示
    public function test_admin_attendance_list_yesterday()
    {
        $adminUser = AdminUser::find(1);
        $response = $this->actingAs($adminUser, 'admin')->get('/admin/attendance/list/yesterday');
        $response->assertStatus(200);
        $response->assertViewHas('attendances');
    }

    // 勤怠一覧情報取得機能（管理者） 翌日表示
    public function test_admin_attendance_list_tomorrow()
    {
        $adminUser = AdminUser::find(1);
        $response = $this->actingAs($adminUser, 'admin')->get('/admin/attendance/list/tomorrow');
        $response->assertStatus(200);
        $response->assertViewHas('attendances');
    }

    // 勤怠詳細情報取得・修正機能（管理者）
    public function test_admin_attendance_detail()
    {
        $adminUser = AdminUser::find(1);
        $attendance = Attendance::with(['user', 'rests'])->find(8);
        $response = $this->actingAs($adminUser, 'admin')->get('/attendance/8');
        $response->assertStatus(200);

        // 勤務日
        $response->assertSee($attendance->workDate->format('Y年'));
        $response->assertSee($attendance->workDate->format('m月d日'));

        // 出勤時間・退勤時間
        if ($attendance->clockIn) {
            $response->assertSee($attendance->clockIn->format('H:i'));
        }
        if ($attendance->clockOut) {
            $response->assertSee($attendance->clockOut->format('H:i'));
        }

        // 休憩時間
        foreach ($attendance->rests as $rest) {
            if ($rest->restIn) {
                $response->assertSee($rest->restIn->format('H:i'));
            }
            if ($rest->restOut) {
                $response->assertSee($rest->restOut->format('H:i'));
            }
        }
    }

    // 勤怠詳細情報取得・修正機能（管理者）バリデーション確認
    public function test_admin_attendance_detail_validation_clockIn()
    {
        $adminUser = AdminUser::find(1);
        $attendance = Attendance::with(['user', 'rests'])->find(8);
        $response = $this->actingAs($adminUser, 'admin')->post('/admin/attendance/8', [
            'clockIn' => "11:00",
            'clockOut' => "10:00",
            'remark' => "昼食のため",
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors('clockIn');

        $errors = session('errors');
        $this->assertEquals('出勤時間もしくは退勤時間が不適切な値です', $errors->first('clockIn'));
    }

    public function test_admin_attendance_detail_validation_remark()
    {
        $adminUser = AdminUser::find(1);
        $attendance = Attendance::with(['user', 'rests'])->find(8);
        $response = $this->actingAs($adminUser, 'admin')->post('/admin/attendance/8', [
            'clockIn' => "09:00",
            'clockOut' => "19:00",
            'remark' => "",
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors('remark');

        $errors = session('errors');
        $this->assertEquals('備考を記入してください', $errors->first('remark'));
    }

    // ユーザー情報取得機能（管理者）
    public function test_admin_attendance_staff()
    {
        $adminUser = AdminUser::find(1);
        $users = User::all();
        $response = $this->actingAs($adminUser, 'admin')->get('/admin/staff/list');
        $response->assertStatus(200);
        foreach ($users as $user) {
            $response->assertSee($user->name);
            $response->assertSee($user->email);
        }
    }

    // ユーザー情報取得機能（管理者）
    public function test_attendance_staff_attendance()
    {
        $adminUser = AdminUser::find(1);
        $response = $this->actingAs($adminUser, 'admin')->get('/admin/attendance/staff/2');
        $response->assertStatus(200);
        $response->assertViewHas('month_day_lists');
    }

    // ユーザー情報取得機能（管理者） 前月表示
    public function test_attendance_staff_attendance_lastMonth()
    {
        $adminUser = AdminUser::find(1);
        $response = $this->actingAs($adminUser, 'admin')->get('/admin/attendance/staff/lastMonth/2');
        $response->assertStatus(200);
        $response->assertViewHas('month_day_lists');
    }

    // ユーザー情報取得機能（管理者） 翌月表示
    public function test_attendance_staff_attendance_nextMonth()
    {
        $adminUser = AdminUser::find(1);
        $response = $this->actingAs($adminUser, 'admin')->get('/admin/attendance/staff/nextMonth/2');
        $response->assertStatus(200);
        $response->assertViewHas('month_day_lists');
    }

    // ユーザー情報取得機能（管理者） 詳細表示
    public function test_attendance_staff_attendance_detail()
    {
        $adminUser = AdminUser::find(1);
        $response = $this->actingAs($adminUser, 'admin')->get('/attendance/8');
        $response->assertStatus(200);
        $response->assertViewHas('attendance');
    }

    // 勤怠情報修正機能（管理者） 承認待ち
    public function test_attendance_request_pending()
    {
        $adminUser = AdminUser::find(1);
        $response = $this->actingAs($adminUser, 'admin')->get('/stamp_correction_request/list?status=pending');
        $response->assertStatus(200);
        $response->assertViewHas('requests');
    }

    // 勤怠情報修正機能（管理者） 承認済み
    public function test_attendance_request_approved()
    {
        $adminUser = AdminUser::find(1);
        $response = $this->actingAs($adminUser, 'admin')->get('/stamp_correction_request/list?status=approved');
        $response->assertStatus(200);
        $response->assertViewHas('requests');
    }

    // 勤怠情報修正機能（管理者） 詳細
    public function test_attendance_request_detail()
    {
        $adminUser = AdminUser::find(1);
        $response = $this->actingAs($adminUser, 'admin')->get('/stamp_correction_request/approve/1');
        $response->assertStatus(200);
        $response->assertViewHas('attendanceRequest');
    }


    // 勤怠情報修正機能（管理者） 承認
    public function test_attendance_request_detail_approve()
    {
        $adminUser = AdminUser::find(1);
        $response = $this->actingAs($adminUser, 'admin')->post('/stamp_correction_request/approve/1');
        $response->assertStatus(302);
        $response->assertSee('承認済み');
    }
}
