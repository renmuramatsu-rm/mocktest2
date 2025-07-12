<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Carbon\CarbonImmutable;
use App\Models\Attendance;
use App\Models\Rest;
use App\Models\AttendanceCorrectionRequest;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $user_id = Auth::user()->id;
        CarbonImmutable::setlocale('ja');
        $now = CarbonImmutable::now();
        $date = $now->isoFormat('Y年M月D日(ddd)');
        $hour = $now->isoFormat('HH:mm');
        $today = Carbon::today();
        $attendance = Attendance::where('employee_id', $user_id)->whereDate('workDate', $today)->latest()->first();
        if (is_null($attendance)) {
            $attendance = (object)['status' => '勤務外'];
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
            // 同日付の出勤打刻で、かつ直前の退勤打刻がされていないか日付を比較する。
            if (($oldAttendanceDay == $newAttendanceDay) && (empty($oldAttendance->clockOut))) {
                return redirect()->back();
            }
        }
        $attendance = Attendance::create([
            'employee_id' => $user->id,
            'workDate' => Carbon::now(),
            'clockIn' => Carbon::now()->toTimeString(),
            'status' => $request->input('status', '出勤中'),
        ]);
        CarbonImmutable::setlocale('ja');
        $now = CarbonImmutable::now();
        $date = $now->isoFormat('Y年M月D日(ddd)');
        $hour = $now->isoFormat('HH:mm');
        $currentStatus = $attendance->status;
        $newStatus = $request->input('status');
        if (isset($allowedTransitions[$currentStatus]) && in_array($newStatus, $allowedTransitions[$currentStatus])) {
            $attendance->status = $newStatus;
            $attendance->save();
        }
        return view('index', compact('date', 'hour', 'attendance'));
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
        $now = CarbonImmutable::now();
        $date = $now->isoFormat('Y年M月D日(ddd)');
        $hour = $now->isoFormat('HH:mm');
        $today = Carbon::today();
        $attendance = Attendance::where('employee_id', $user->id)
            ->whereDate('clockIn', Carbon::today())
            ->first();
        if ($attendance && !$attendance->clockOut) {
            $attendance->update([
                'employee_id' => $user->id,
                'clockOut' => Carbon::now()->toTimeString(),
                'status' => $request->input('status', '退勤済'),
                'workTime' => $attendance->clockIn->diffInHours($now)
            ]);
            return view('index', compact('date', 'hour', 'attendance'));
        } elseif (!$attendance && !$attendance->clockOut) {
            $attendance = Attendance::Create([
                'employee_id' => $user->id,
                'clockOut' => Carbon::now(),
                'status' => $request->input('status', '退勤済'),
                'workTime' => $attendance->clockIn->diffInHours($now)
            ]);

            return view('index', compact('date', 'hour', 'attendance'));
        } else {
            return view('index', compact('date', 'hour', 'attendance'));
        }
    }

    /**
     * 休憩処理
     * @param Request $request リクエスト
     * @return Redirect リダイレクト
     */
    public function restIn(Request $request)
    {
        $user = Auth::user();
        $user_id = Auth::user()->id;
        CarbonImmutable::setlocale('ja');
        $now = CarbonImmutable::now();
        $date = $now->isoFormat('Y年M月D日(ddd)');
        $hour = $now->isoFormat('HH:mm');
        $today = Carbon::today();

        $attendance = Attendance::where('employee_id', $user->id)->whereDate('clockIn', Carbon::today())->first();
        if ($attendance) {
            $attendance->update([
                'employee_id' => $user->id,
                'status' => $request->input('status', '休憩中'),
            ]);
            $rest = new Rest;
            $rest->attendance_id = $attendance->id;
            $rest->workDate = Carbon::now();
            $rest->restIn = Carbon::now();
            $rest->save();
            return view('index', compact('date', 'hour', 'attendance', 'rest'));
        } else {
            return view('index', compact('date', 'hour', 'attendance', 'rest'));
        }
    }

    public function restOut(Request $request)
    {
        $user = Auth::user();
        $user_id = Auth::user()->id;
        CarbonImmutable::setlocale('ja');
        $now = CarbonImmutable::now();
        $date = $now->isoFormat('Y年M月D日(ddd)');
        $hour = $now->isoFormat('HH:mm');
        $today = Carbon::today();
        $attendance = Attendance::where('employee_id', $user->id)->whereDate('clockIn', Carbon::today())->first();
        if ($attendance) {
            $rest = Rest::where('attendance_id', $attendance->id)->whereDate('workDate', Carbon::today())->orderBy('restIn', 'desc')->first();
            $rest->attendance_id = $attendance->id;
            $rest->restOut = Carbon::now();
            $rest->restTime = $rest->restIn->diffInHours($now);
            $rest->save();
            $attendance->update([
                'employee_id' => $user->id,
                'status' => $request->input('status', '出勤中'),
                'total_restTime' => Rest::where('attendance_id', $attendance->id)->sum('restTime'),
            ]);
            return view('index', compact('date', 'hour', 'attendance', 'rest'));
        } else {
            return view('index', compact('date', 'hour', 'attendance', 'rest'));
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
            $year = date(('Y'), strtotime($viewMonth));
            $month = date(('m'), strtotime($viewMonth));
            $first_day = Carbon::create($year, $month, 1)->firstOfMonth()->format('d');
            $last_day  = Carbon::create($year, $month, 1)->lastOfMonth()->format('d');
            $month_day_list = [];
            $week = [
                '(日)', //0
                '(月)', //1
                '(火)', //2
                '(水)', //3
                '(木)', //4
                '(金)', //5
                '(土)', //6
            ];
            for ($day = $first_day; $last_day >= $day; $day++) {
                $month_day = Carbon::create($year, $month, $day);
                $day_of_week = $week[date('w', strtotime($month_day))];
                $days = [];
                $days['format_date'] = $month_day->format('m/d' . $day_of_week);
                $days['target_date'] = $month_day->format('Y-m-d');
                $days['attendance'] = Attendance::with('rests')->where('employee_id', $user_id)->whereDate('clockIn', $month_day)->first();
                $month_day_lists[] = $days;
            }
            return view('attendanceList', compact('viewMonth', 'month_day_lists'));
        } elseif (!empty($viewMonth)) {
            $viewMonthInput = new Carbon($request->input('viewMonth'));
            $viewMonth = $viewMonthInput->format('Y-m-d');
            $year = date(('Y'), strtotime($viewMonth));
            $month = date(('m'), strtotime($viewMonth));
            $first_day = Carbon::create($year, $month, 1)->firstOfMonth()->format('d');
            $last_day  = Carbon::create($year, $month, 1)->lastOfMonth()->format('d');
            $month_day_list = [];
            $week = [
                '(日)', //0
                '(月)', //1
                '(火)', //2
                '(水)', //3
                '(木)', //4
                '(金)', //5
                '(土)', //6
            ];
            for ($day = $first_day; $last_day >= $day; $day++) {
                $month_day = Carbon::create($year, $month, $day);
                $day_of_week = $week[date('w', strtotime($month_day))];
                $days = [];
                $days['format_date'] = $month_day->format('m/d' . $day_of_week);
                $days['target_date'] = $month_day->format('Y-m-d');
                $days['attendance'] = Attendance::where('employee_id', $user_id)->whereDate('clockIn', $month_day)->first();
                $month_day_lists[] = $days;
            }
            return view('attendanceList', compact('month_day_lists', 'viewMonth'));
        }
    }

    public function listLastMonth(Request $request)
    {
        $user_id = Auth::user()->id;
        $viewMonth = new Carbon($request->input('viewMonth'));
        if (empty($viewMonth)) {
            $viewMonth = Carbon::now()->subMonthsNoOverflow(1)->format('Y-m-d');
            $year = date(('Y'), strtotime($viewMonth));
            $month = date(('m'), strtotime($viewMonth));
            $first_day = Carbon::create($year, $month, 1)->firstOfMonth()->format('d');
            $last_day  = Carbon::create($year, $month, 1)->lastOfMonth()->format('d');
            $month_day_list = [];
            $week = [
                '(日)', //0
                '(月)', //1
                '(火)', //2
                '(水)', //3
                '(木)', //4
                '(金)', //5
                '(土)', //6
            ];
            for ($day = $first_day; $last_day >= $day; $day++) {
                $month_day = Carbon::create($year, $month, $day);
                $day_of_week = $week[date('w', strtotime($month_day))];
                $days = [];
                $days['format_date'] = $month_day->format('m/d' . $day_of_week);
                $days['target_date'] = $month_day->format('Y-m-d');
                $days['attendance'] = Attendance::where('employee_id', $user_id)->whereDate('clockIn', $month_day)->first();
                $month_day_lists[] = $days;
            }
            return view('attendanceList', compact('viewMonth', 'month_day_lists'));
        } elseif (!empty($viewMonth)) {
            $lastMonth = $viewMonth->subMonthsNoOverflow(1)->format('Y-m-d');
            $year = date(('Y'), strtotime($lastMonth));
            $month = date(('m'), strtotime($lastMonth));
            $first_day = Carbon::create($year, $month, 1)->firstOfMonth()->format('d');
            $last_day  = Carbon::create($year, $month, 1)->lastOfMonth()->format('d');
            $month_day_list = [];
            $week = [
                '(日)', //0
                '(月)', //1
                '(火)', //2
                '(水)', //3
                '(木)', //4
                '(金)', //5
                '(土)', //6
            ];
            for ($day = $first_day; $last_day >= $day; $day++) {
                $month_day = Carbon::create($year, $month, $day);
                $day_of_week = $week[date('w', strtotime($month_day))];
                $days = [];
                $days['format_date'] = $month_day->format('m/d' . $day_of_week);
                $days['target_date'] = $month_day->format('Y-m-d');
                $days['attendance'] = Attendance::where('employee_id', $user_id)->whereDate('clockIn', $month_day)->first();
                $month_day_lists[] = $days;
            }
            return view('attendanceList', compact('viewMonth', 'month_day_lists'));
        }
    }

    public function listNextMonth(Request $request)
    {
        $user_id = Auth::user()->id;
        $viewMonth = new Carbon($request->input('viewMonth'));
        if (empty($viewMonth)) {
            $viewMonth = Carbon::now()->addMonthsNoOverflow(1)->format('Y-m-d');
            $year = date(('Y'), strtotime($viewMonth));
            $month = date(('m'), strtotime($viewMonth));
            $first_day = Carbon::create($year, $month, 1)->firstOfMonth()->format('d');
            $last_day  = Carbon::create($year, $month, 1)->lastOfMonth()->format('d');
            $month_day_list = [];
            $week = [
                '(日)', //0
                '(月)', //1
                '(火)', //2
                '(水)', //3
                '(木)', //4
                '(金)', //5
                '(土)', //6
            ];
            for ($day = $first_day; $last_day >= $day; $day++) {
                $month_day = Carbon::create($year, $month, $day);
                $day_of_week = $week[date('w', strtotime($month_day))];
                $days = [];
                $days['format_date'] = $month_day->format('m/d' . $day_of_week);
                $days['target_date'] = $month_day->format('Y-m-d');
                $days['attendance'] = Attendance::where('employee_id', $user_id)->whereDate('clockIn', $month_day)->first();
                $month_day_lists[] = $days;
            }
            return view('attendanceList', compact('viewMonth', 'month_day_lists'));
        } elseif (!empty($viewMonth)) {
            $nextMonth = $viewMonth->addMonthsNoOverflow(1)->format('Y-m-d');
            $year = date(('Y'), strtotime($nextMonth));
            $month = date(('m'), strtotime($nextMonth));
            $first_day = Carbon::create($year, $month, 1)->firstOfMonth()->format('d');
            $last_day  = Carbon::create($year, $month, 1)->lastOfMonth()->format('d');
            $month_day_list = [];
            $week = [
                '(日)', //0
                '(月)', //1
                '(火)', //2
                '(水)', //3
                '(木)', //4
                '(金)', //5
                '(土)', //6
            ];
            for ($day = $first_day; $last_day >= $day; $day++) {
                $month_day = Carbon::create($year, $month, $day);
                $day_of_week = $week[date('w', strtotime($month_day))];
                $days = [];
                $days['format_date'] = $month_day->format('m/d' . $day_of_week);
                $days['target_date'] = $month_day->format('Y-m-d');
                $days['attendance'] = Attendance::where('employee_id', $user_id)->whereDate('clockIn', $month_day)->first();
                $month_day_lists[] = $days;
            }
            return view('attendanceList', compact('viewMonth', 'month_day_lists'));
        }
    }

    public function detail($id)
    {
        $user = Auth::user();
        $adminUser = Auth::guard('admin')->user();
        if ($adminUser) {
            $attendance = Attendance::find($id);
            $rests = Rest::where('attendance_id', $attendance->id)->whereDate('workDate', $attendance->workDate)->get();
            return view('adminAttendanceDetail', compact('attendance', 'rests'));
        }

        if ($user) {
            $attendance = Attendance::with('user')->find($id);
            $rests = Rest::where('attendance_id', $attendance->id)->whereDate('workDate', $attendance->workDate)->get();
            $attendanceRequest = AttendanceCorrectionRequest::with('requestRest')->where('attendance_id', $attendance->id)->first();
            $requestRests = $attendanceRequest ? $attendanceRequest->requestRest : [];
            return view('attendanceDetail', compact('attendance', 'rests', 'attendanceRequest', 'requestRests'));
        }
    }

    public function edit($id, Request $request)
    {
        $user = Auth::user();
        $user_id = Auth::user()->id;
        CarbonImmutable::setlocale('ja');
        $now = CarbonImmutable::now();
        $date = $now->isoFormat('Y年M月D日(ddd)');
        $hour = $now->isoFormat('HH:mm');
        $today = Carbon::today();
        $attendance = Attendance::find($id);

        $restIns = $request->input('restIn', []);
        $restOuts = $request->input('restOut', []);
        if (!empty($restIns) || !empty($restOuts)) {
            foreach ($restIns as $i => $restIn) {
                if ($restIn || ($restOuts[$i] ?? null)) {
                    Rest::create([
                        'attendance_id' => $id,
                        'workDate' => Carbon::today(),
                        'restIn' => $restIn ? Carbon::parse($restIn) : null,
                        'restOut' => $restOuts[$i] ? Carbon::parse($restOuts[$i]) : null,
                        'restTime' => isset($restIns[$i], $restOuts[$i])
                            ? Carbon::parse($restOuts[$i])->diffInMinutes(Carbon::parse($restIns[$i]))
                            : 0
                    ]);
                }
            }
        }

        $clockIn = $request->clockIn ? Carbon::parse($request->clockIn) : null;
        $clockOut = $request->clockOut ? Carbon::parse($request->clockOut) : null;
        if ($clockIn) {
            $attendance->clockIn = $clockIn;
        }
        if ($clockOut) {
            $attendance->clockOut = $clockOut;
            if ($attendance->clockIn) {
                $attendance->workTime = $attendance->clockIn->diffInHours($clockOut);
            }
            $attendance->total_restTime = Rest::where('attendance_id', $attendance->id)->sum('restTime');
            $attendance->save();
            return view('adminAttendanceDetail', compact('date', 'hour', 'attendance'));
        }
    }
}
