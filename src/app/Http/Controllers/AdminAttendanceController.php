<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\AdminUser;
use App\Models\User;

class AdminAttendanceController extends Controller
{
    public function adminDetail($id)
    {
        $attendance = Attendance::find($id);

        return view('adminAttendanceDetail', compact('attendance'));
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
                $days['attendance'] = Attendance::with('rests')->where('employee_id', $id)->whereDate('clockIn', $month_day)->first();
                $month_day_lists[] = $days;
            }
            return view('adminAttendanceStaff', compact('viewMonth', 'month_day_lists','user'));
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
                $days['attendance'] = Attendance::where('employee_id', $id)->whereDate('clockIn', $month_day)->first();
                $month_day_lists[] = $days;
            }
            return view('adminAttendanceStaff', compact('month_day_lists', 'viewMonth','user'));
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
                $days['attendance'] = Attendance::where('employee_id', $id)->whereDate('clockIn', $month_day)->first();
                $month_day_lists[] = $days;
            }
            return view('adminAttendanceStaff', compact('viewMonth', 'month_day_lists','user'));
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
                $days['attendance'] = Attendance::where('employee_id', $id)->whereDate('clockIn', $month_day)->first();
                $month_day_lists[] = $days;
            }
            return view('adminAttendanceStaff', compact('viewMonth', 'month_day_lists','user'));
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
                $days['attendance'] = Attendance::where('employee_id', $id)->whereDate('clockIn', $month_day)->first();
                $month_day_lists[] = $days;
            }
            return view('adminAttendanceStaff', compact('viewMonth', 'month_day_lists','user'));
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
                $days['attendance'] = Attendance::where('employee_id', $id)->whereDate('clockIn', $month_day)->first();
                $month_day_lists[] = $days;
            }
            return view('adminAttendanceStaff', compact('viewMonth', 'month_day_lists','user'));
        }
    }
}
