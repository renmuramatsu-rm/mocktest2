<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\AdminUser;
use App\Models\User;
use Carbon\CarbonImmutable;
use App\Models\Rest;
use App\Http\Requests\AdminAttendanceRequest;

class AdminAttendanceController extends Controller
{
    public function adminDetailEdit($id, AdminAttendanceRequest $request)
    {
        CarbonImmutable::setLocale('ja');
        $user = Auth::user();
        $now  = CarbonImmutable::now();
        $attendance = Attendance::findOrFail($id);

        // 既存の休憩データ削除
        $attendance->rests()->delete();

        // 休憩の入力（複数）
        $restIns  = $request->input('restIn', []);
        $restOuts = $request->input('restOut', []);
        foreach ($restIns as $i => $restIn) {
            if (!empty($restIn) || !empty($restOuts[$i] ?? null)) {
                $attendance->rests()->create([
                    'workDate' => $attendance->workDate,
                    'restIn'   => !empty($restIn) ? Carbon::parse($restIn) : null,
                    'restOut'  => !empty($restOuts[$i]) ? Carbon::parse($restOuts[$i]) : null,
                    'restTime' => (!empty($restIn) && !empty($restOuts[$i]))
                        ? Carbon::parse($restIn)->diffInMinutes(Carbon::parse($restOuts[$i])) / 60
                        : 0,
                ]);
            }
        }
        $attendance->update([
            'clockIn'        => Carbon::parse($request->input('clockIn')),
            'clockOut'       => Carbon::parse($request->input('clockOut')),
            'remark'         => $request->input('remark'),
            'total_restTime' => Rest::where('attendance_id', $id)->sum('restTime'),
            'workTime'       => round((Carbon::parse($request->input('clockIn'))->diffInMinutes(Carbon::parse($request->input('clockOut'))) / 60) - Rest::where('attendance_id', $id)->sum('restTime'), 2)
        ]);

        return redirect()->route('detail', ['id' => $attendance->id])
            ->with('success', '勤怠情報を更新しました。');
    }

    public function staffList()
    {
        $adminUser = Auth::guard('admin')->user()->id;
        $users     = User::all();
        return view('adminStaff', compact('users'));
    }

    public function adminAttendanceStaff(Request $request, $id)
    {
        $user      = User::find($id);
        $thisMonth = Carbon::now()->month;
        $today     = Carbon::now()->format('Y-m-d');
        $viewMonth = $request->input('viewMonth');
        if (empty($viewMonth)) {
            $viewMonth      = Carbon::now()->format('Y-m-d');
            $year           = date(('Y'), strtotime($viewMonth));
            $month          = date(('m'), strtotime($viewMonth));
            $first_day      = Carbon::create($year, $month, 1)->firstOfMonth()->format('d');
            $last_day       = Carbon::create($year, $month, 1)->lastOfMonth()->format('d');
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
                $month_day           = Carbon::create($year, $month, $day);
                $day_of_week         = $week[date('w', strtotime($month_day))];
                $days                = [];
                $days['format_date'] = $month_day->format('m/d' . $day_of_week);
                $days['target_date'] = $month_day->format('Y-m-d');
                $days['attendance']  = Attendance::with('rests')->where('employee_id', $id)->whereDate('workDate', $month_day)->first();
                $month_day_lists[]   = $days;
            }
            return view('adminAttendanceStaff', compact('viewMonth', 'month_day_lists', 'user'));
        } elseif (!empty($viewMonth)) {
            $viewMonthInput = new Carbon($request->input('viewMonth'));
            $viewMonth      = $viewMonthInput->format('Y-m-d');
            $year           = date(('Y'), strtotime($viewMonth));
            $month          = date(('m'), strtotime($viewMonth));
            $first_day      = Carbon::create($year, $month, 1)->firstOfMonth()->format('d');
            $last_day       = Carbon::create($year, $month, 1)->lastOfMonth()->format('d');
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
                $month_day           = Carbon::create($year, $month, $day);
                $day_of_week         = $week[date('w', strtotime($month_day))];
                $days                = [];
                $days['format_date'] = $month_day->format('m/d' . $day_of_week);
                $days['target_date'] = $month_day->format('Y-m-d');
                $days['attendance']  = Attendance::where('employee_id', $id)->whereDate('workDate', $month_day)->first();
                $month_day_lists[]   = $days;
            }
            return view('adminAttendanceStaff', compact('month_day_lists', 'viewMonth', 'user'));
        }
    }

    public function staffLastMonth(Request $request, $id)
    {
        $user      = User::find($id);
        $viewMonth = new Carbon($request->input('viewMonth'));
        if (empty($viewMonth)) {
            $viewMonth      = Carbon::now()->subMonthsNoOverflow(1)->format('Y-m-d');
            $year           = date(('Y'), strtotime($viewMonth));
            $month          = date(('m'), strtotime($viewMonth));
            $first_day      = Carbon::create($year, $month, 1)->firstOfMonth()->format('d');
            $last_day       = Carbon::create($year, $month, 1)->lastOfMonth()->format('d');
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
                $month_day           = Carbon::create($year, $month, $day);
                $day_of_week         = $week[date('w', strtotime($month_day))];
                $days                = [];
                $days['format_date'] = $month_day->format('m/d' . $day_of_week);
                $days['target_date'] = $month_day->format('Y-m-d');
                $days['attendance']  = Attendance::where('employee_id', $id)->whereDate('workDate', $month_day)->first();
                $month_day_lists[]   = $days;
            }
            return view('adminAttendanceStaff', compact('viewMonth', 'month_day_lists', 'user'));
        } elseif (!empty($viewMonth)) {
            $lastMonth      = $viewMonth->subMonthsNoOverflow(1)->format('Y-m-d');
            $year           = date(('Y'), strtotime($lastMonth));
            $month          = date(('m'), strtotime($lastMonth));
            $first_day      = Carbon::create($year, $month, 1)->firstOfMonth()->format('d');
            $last_day       = Carbon::create($year, $month, 1)->lastOfMonth()->format('d');
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
                $month_day           = Carbon::create($year, $month, $day);
                $day_of_week         = $week[date('w', strtotime($month_day))];
                $days = [];
                $days['format_date'] = $month_day->format('m/d' . $day_of_week);
                $days['target_date'] = $month_day->format('Y-m-d');
                $days['attendance']  = Attendance::where('employee_id', $id)->whereDate('workDate', $month_day)->first();
                $month_day_lists[]   = $days;
            }
            return view('adminAttendanceStaff', compact('viewMonth', 'month_day_lists', 'user'));
        }
    }

    public function staffNextMonth(Request $request, $id)
    {
        $user = User::find($id);
        $viewMonth = new Carbon($request->input('viewMonth'));
        if (empty($viewMonth)) {
            $viewMonth      = Carbon::now()->addMonthsNoOverflow(1)->format('Y-m-d');
            $year           = date(('Y'), strtotime($viewMonth));
            $month          = date(('m'), strtotime($viewMonth));
            $first_day      = Carbon::create($year, $month, 1)->firstOfMonth()->format('d');
            $last_day       = Carbon::create($year, $month, 1)->lastOfMonth()->format('d');
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
                $month_day           = Carbon::create($year, $month, $day);
                $day_of_week         = $week[date('w', strtotime($month_day))];
                $days                = [];
                $days['format_date'] = $month_day->format('m/d' . $day_of_week);
                $days['target_date'] = $month_day->format('Y-m-d');
                $days['attendance']  = Attendance::where('employee_id', $id)->whereDate('workDate', $month_day)->first();
                $month_day_lists[]   = $days;
            }
            return view('adminAttendanceStaff', compact('viewMonth', 'month_day_lists', 'user'));
        } elseif (!empty($viewMonth)) {
            $nextMonth      = $viewMonth->addMonthsNoOverflow(1)->format('Y-m-d');
            $year           = date(('Y'), strtotime($nextMonth));
            $month          = date(('m'), strtotime($nextMonth));
            $first_day      = Carbon::create($year, $month, 1)->firstOfMonth()->format('d');
            $last_day       = Carbon::create($year, $month, 1)->lastOfMonth()->format('d');
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
                $month_day           = Carbon::create($year, $month, $day);
                $day_of_week         = $week[date('w', strtotime($month_day))];
                $days                = [];
                $days['format_date'] = $month_day->format('m/d' . $day_of_week);
                $days['target_date'] = $month_day->format('Y-m-d');
                $days['attendance']  = Attendance::where('employee_id', $id)->whereDate('workDate', $month_day)->first();
                $month_day_lists[]   = $days;
            }
            return view('adminAttendanceStaff', compact('viewMonth', 'month_day_lists', 'user'));
        }
    }
}
