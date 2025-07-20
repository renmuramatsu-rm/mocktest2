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
        $user = Auth::user();
        CarbonImmutable::setLocale('ja');
        $now = CarbonImmutable::now();
        $attendance = Attendance::findOrFail($id); // 存在確認

        // 出退勤・備考を更新
        $attendance->update([
            'clockIn' => Carbon::parse($request->input('clockIn')),
            'clockOut' => Carbon::parse($request->input('clockOut')),
            'remark' => $request->input('remark'),
        ]);

        // 既存の休憩データは一旦削除（必要に応じて更新処理に変更可）
        $attendance->rests()->delete();

        // 休憩の入力（複数）
        $restIns = $request->input('restIn', []);
        $restOuts = $request->input('restOut', []);
        foreach ($restIns as $i => $restIn) {
            if (!empty($restIn) || !empty($restOuts[$i] ?? null)) {
                $attendance->rests()->create([
                    'workDate' => $attendance->workDate,
                    'restIn' => !empty($restIn) ? Carbon::parse($restIn) : null,
                    'restOut' => !empty($restOuts[$i]) ? Carbon::parse($restOuts[$i]) : null,
                    'restTime' => (!empty($restIn) && !empty($restOuts[$i]))
                        ? Carbon::parse($restIn)->diffInMinutes(Carbon::parse($restOuts[$i])) / 60
                        : 0,
                ]);
            }
        }

        return redirect()->route('detail', ['id' => $attendance->id])
            ->with('success', '勤怠情報を更新しました。');
    }

    public function staffList()
    {
        $adminUser = Auth::guard('admin')->user()->id;
        $users = User::all();
        return view('adminStaff', compact('users'));
    }

    public function adminAttendanceStaff(Request $request, $id)
    {
        $user = User::find($id);
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
                $days['attendance'] = Attendance::with('rests')->where('employee_id', $id)->whereDate('workDate', $month_day)->first();
                $month_day_lists[] = $days;
            }
            return view('adminAttendanceStaff', compact('viewMonth', 'month_day_lists', 'user'));
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
                $days['attendance'] = Attendance::where('employee_id', $id)->whereDate('workDate', $month_day)->first();
                $month_day_lists[] = $days;
            }
            return view('adminAttendanceStaff', compact('month_day_lists', 'viewMonth', 'user'));
        }
    }

    public function staffLastMonth(Request $request, $id)
    {
        $user = User::find($id);
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
                $days['attendance'] = Attendance::where('employee_id', $id)->whereDate('workDate', $month_day)->first();
                $month_day_lists[] = $days;
            }
            return view('adminAttendanceStaff', compact('viewMonth', 'month_day_lists', 'user'));
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
                $days['attendance'] = Attendance::where('employee_id', $id)->whereDate('workDate', $month_day)->first();
                $month_day_lists[] = $days;
            }
            return view('adminAttendanceStaff', compact('viewMonth', 'month_day_lists', 'user'));
        }
    }

    public function staffNextMonth(Request $request, $id)
    {
        $user = User::find($id);
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
                $days['attendance'] = Attendance::where('employee_id', $id)->whereDate('workDate', $month_day)->first();
                $month_day_lists[] = $days;
            }
            return view('adminAttendanceStaff', compact('viewMonth', 'month_day_lists', 'user'));
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
                $days['attendance'] = Attendance::where('employee_id', $id)->whereDate('workDate', $month_day)->first();
                $month_day_lists[] = $days;
            }
            return view('adminAttendanceStaff', compact('viewMonth', 'month_day_lists', 'user'));
        }
    }
}
