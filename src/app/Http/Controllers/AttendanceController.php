<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Carbon\CarbonImmutable;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $user_id = Auth::user()->id;
        CarbonImmutable::setlocale('ja');
        // リアルタイムで時間が変わるようにする
        $now = CarbonImmutable::now();
        $date = $now->isoformat('Y年M月D日(ddd)');
        $hour = $now->isoformat('HH:mm');
        $today = Carbon::today();
        $attendance = Attendance::where('employee_id', $user_id)->whereDate('clockIn', $today)->latest()->first();
        if (is_null($attendance)) {
            $attendance = (object)['status' => '出勤前'];
        }
        return view('index', compact('date', 'hour', 'attendance'));
    }

    /**
     * 出勤処理
     * @param Request $request リクエスト
     * @return Redirect リダイレクト
     */
    public function clockIn(Request $request, Attendance $attendance)
    {
        $user = Auth::user();

        $oldAttendance = Attendance::where('employee_id', $user->id)->latest()->first();
        $newAttendanceDay = Carbon::today();

        if ($oldAttendance) {
            $oldAttendanceDay = (new Carbon($oldAttendance->clockIn))->copy()->startOfDay();

            // 日付を比較する。同日付の出勤打刻で、かつ直前の退勤打刻がされていない場合エラーを吐き出す。
            if (($oldAttendanceDay == $newAttendanceDay) && (empty($oldAttendance->clockOut))) {
                return redirect()->back()->with('error', 'すでに出勤打刻がされています');
            }
        }
        $attendance = Attendance::create([
            'employee_id' => $user->id,
            'clockIn' => Carbon::now(),
            'status' => $request->input('status', '出勤中'),
        ]);

        CarbonImmutable::setlocale('ja');
        $now = CarbonImmutable::now();
        $date = $now->isoformat('Y年M月D日(ddd)');
        $hour = $now->isoformat('HH:mm');

        $currentStatus = $attendance->status;
        $newStatus = $request->input('status');

        if (isset($allowedTransitions[$currentStatus]) && in_array($newStatus, $allowedTransitions[$currentStatus])) {
            $attendance->status = $newStatus;
            $attendance->save();
        }

        return view('index', compact('date', 'hour', 'attendance'))->with('messege', '出勤打刻が完了しました');
    }

    /**
     * 退勤処理
     * @param Request $request リクエスト
     * @return Redirect リダイレクト
     */
    public function clockOut(Request $request)
    {
        $user = Auth::user();
        $user_id = Auth::user()->id;
        CarbonImmutable::setlocale('ja');
        // リアルタイムで時間が変わるようにする
        $now = CarbonImmutable::now();
        $date = $now->isoformat('Y年M月D日(ddd)');
        $hour = $now->isoformat('HH:mm');
        $today = Carbon::today();

        // 今日の出勤記録を取得
        $attendance = Attendance::where('employee_id', $user->id)
            ->whereDate('clockIn', Carbon::today())
            ->first();
        if ($attendance && !$attendance->clockOut) {
            $attendance->update([
                'employee_id' => $user->id,
                'clockOut' => Carbon::now(),
                'status' => $request->input('status', '退勤後'),
                'workTime' => $attendance->clockIn->diffInHours($now)
            ]);
            return view('index', compact('date', 'hour', 'attendance'))->with('error', '退勤しました。');
        } elseif (!$attendance && !$attendance->clockOut) {
            $attendance = Attendance::Create([
                'employee_id' => $user->id,
                'clockOut' => Carbon::now(),
                'status' => $request->input('status', '退勤後'),
                'workTime' => $attendance->clockIn->diffInHours($now)
            ]);

            return view('index', compact('date', 'hour', 'attendance'))->with('error', '退勤しました');
        } else {
            return view('index', compact('date', 'hour', 'attendance'))->with('error', 'まだ出勤していないか、既に退勤しています。');
        }
        // 所定労働時間（8時間として定義）
        // $standardWorkHours = 8;
        // 出勤時間と退勤時間の差を計算
        // $clockInTime = Carbon::parse($todayAttendance->clockIn);
        // $clockOutTime = Carbon::parse($todayAttendance->clockOut);
        // $totalWorkedHours = $clockInTime->diffInHours($clockOutTime);
        // 所定労働時間を超えた場合、時間外労働時間を計算
        // $overTimeHours = max(0, $totalWorkedHours - $standardWorkHours);
        // セッションにメッセージとして所定労働時間と時間外労働時間を保存
        // return redirect()->route('index')->with([
        //     'message' => '退勤しました',
        //     'standard_work_hours' => $standardWorkHours,
        //     'worked_hours' => $totalWorkedHours,
        //     'over_time_hours' => $overTimeHours
        // ]);
    }

    /**
     * 休憩処理
     * @param Request $request リクエスト
     * @return Redirect リダイレクト
     */
    public function breakIn(Request $request)
    {
        $user = Auth::user();
        $user_id = Auth::user()->id;
        CarbonImmutable::setlocale('ja');
        // リアルタイムで時間が変わるようにする
        $now = CarbonImmutable::now();
        $date = $now->isoformat('Y年M月D日(ddd)');
        $hour = $now->isoformat('HH:mm');
        $today = Carbon::today();

        // 今日の出勤記録を取得
        $attendance = Attendance::where('employee_id', $user->id)
            ->whereDate('clockIn', Carbon::today())
            ->first();
        if ($attendance && !$attendance->breakIn) {
            $attendance->update([
                'employee_id' => $user->id,
                'breakIn' => Carbon::now(),
                'status' => $request->input('status', '休憩中'),
            ]);
            return view('index', compact('date', 'hour', 'attendance'))->with('error', '休憩中です。');
        } elseif (!$attendance && !$attendance->breakIn) {
            $attendance = Attendance::Create([
                'employee_id' => $user->id,
                'breakIn' => Carbon::now(),
                'status' => $request->input('status', '休憩中'),
            ]);

            return view('index', compact('date', 'hour', 'attendance'))->with('error', '休憩中です');
        } else {
            return view('index', compact('date', 'hour', 'attendance'))->with('error', 'まだ出勤していないか、既に退勤しています。');
        }
    }

    public function breakOut(Request $request)
    {
        $user = Auth::user();
        $user_id = Auth::user()->id;
        CarbonImmutable::setlocale('ja');
        // リアルタイムで時間が変わるようにする
        $now = CarbonImmutable::now();
        $date = $now->isoformat('Y年M月D日(ddd)');
        $hour = $now->isoformat('HH:mm');
        $today = Carbon::today();

        // 今日の休憩記録を取得
        $attendance = Attendance::where('employee_id', $user->id)
            ->whereDate('breakIn', Carbon::today())
            ->first();
        if ($attendance && !$attendance->breakOut) {
            $attendance->update([
                'employee_id' => $user->id,
                'breakOut' => Carbon::now(),
                'status' => $request->input('status', '出勤中'),
                'breakTime' => $attendance->breakIn->diffInHours($now)
            ]);
            return view('index', compact('date', 'hour', 'attendance'))->with('error', '休憩から戻りました。');
        } elseif (!$attendance && !$attendance->breakOut) {
            $attendance = Attendance::Create([
                'employee_id' => $user->id,
                'breakOut' => Carbon::now(),
                'status' => $request->input('status', '出勤中'),
                'breakTime' => $attendance->breakIn->diffInHours($now)
            ]);
            dd($attendance['breakTime']);

            return view('index', compact('date', 'hour', 'attendance'))->with('error', '休憩から戻りました。');
        } else {
            return view('index', compact('date', 'hour', 'attendance'))->with('error', '休憩から戻っています');
        }
    }

    public function list(Request $request)
    {
        $user_id = Auth::user()->id;
        $thisMonth = Carbon::now()->month;
        $today = Carbon::now()->format('Y-m-d');

        $viewMonth = $request->input('viewMonth');
        if (empty($viewMonth)) {
            $viewMonth = Carbon::now()->format('Y-m-d');
            $attendances = Attendance::where('employee_id', $user_id)->whereMonth('clockIn', $thisMonth)->get();
            return view('attendanceList', compact('attendances', 'viewMonth'));
        } elseif (!empty($viewMonth)) {
            $viewMonthInput = new Carbon($request->input('viewMonth'));
            $viewMonth = $viewMonthInput ->format('Y-m-d');
            $newMonth = $viewMonthInput->format('m');
            $attendances = Attendance::where('employee_id', $user_id)->whereMonth('clockIn', $newMonth)->get();
            return view('attendanceList', compact('attendances', 'viewMonth'));
        }
    }

    public function listLastMonth(Request $request)

    // クエリパラメータで持ってくる ?tab=mylist
    {
        $user_id = Auth::user()->id;
        $viewMonth = new Carbon($request->input('viewMonth'));
        if (empty($viewMonth)) {
            $lastMonth = Carbon::now()->subMonthsNoOverflow(1);
            $lastMonthCount = $lastMonth->month;
            $attendances = Attendance::where('employee_id', $user_id)->whereMonth('clockIn', $lastMonthCount)->get();
            return view('attendanceList', compact('attendances'));
        } elseif (!empty($viewMonth)) {
            $lastMonth = $viewMonth->subMonthsNoOverflow(1);
            $lastMonthCount = $lastMonth->month;
            $attendances = Attendance::where('employee_id', $user_id)->whereMonth('clockIn', $lastMonthCount)->get();
            return view('attendanceList', compact('attendances', 'viewMonth'));
        }
    }

    public function listNextMonth(Request $request)
    {
        $user_id = Auth::user()->id;
        $viewMonth = new Carbon($request->input('viewMonth'));
        if (empty($viewMonth)) {
            $nextMonth = Carbon::now()->addMonthsNoOverflow(1);
            $nextMonthCount = $nextMonth->month;
            $attendances = Attendance::where('employee_id', $user_id)->whereMonth('clockIn', $nextMonthCount)->get();
            return view('attendanceList', compact('attendances'));
        } elseif (!empty($viewMonth)) {
            $nextMonth = $viewMonth->addMonthsNoOverflow(1);
            $nextMonthCount = $nextMonth->month;
            $attendances = Attendance::where('employee_id', $user_id)->whereMonth('clockIn', $nextMonthCount)->get();
            return view('attendanceList', compact('attendances', 'viewMonth'));
        }
    }

    public function detail($id)
    {
        $attendance = Attendance::find($id);

        return view('attendanceDetail', compact('attendance'));
    }
}
