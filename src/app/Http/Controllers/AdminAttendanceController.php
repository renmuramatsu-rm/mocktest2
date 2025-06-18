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
        $users = AdminUser::find($adminUser)->users;
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
            $attendances = Attendance::where('employee_id', $id)->whereMonth('clockIn', $thisMonth)->get();
            return view('adminAttendanceStaff', compact('attendances', 'viewMonth', 'user'));
        } elseif (!empty($viewMonth)) {
            $viewMonthInput = new Carbon($request->input('viewMonth'));
            $viewMonth = $viewMonthInput->format('Y-m-d');
            $newMonth = $viewMonthInput->format('m');
            $attendances = Attendance::where('employee_id', $id)->whereMonth('clockIn', $newMonth)->get();
            return view('adminAttendanceStaff', compact('attendances', 'viewMonth', 'user'));
        }
    }

    public function staffLastMonth(Request $request, $id)
    {
        $user = User::find($id);
        $viewMonth = new Carbon($request->input('viewMonth'));
        if (empty($viewMonth)) {
            $lastMonth = Carbon::now()->subMonthsNoOverflow(1);
            $lastMonthCount = $lastMonth->month;
            $attendances = Attendance::where('employee_id', $id)->whereMonth('clockIn', $lastMonthCount)->get();
            return view('adminAttendanceStaff', compact('attendances', 'user'));
        } elseif (!empty($viewMonth)) {
            $lastMonth = $viewMonth->subMonthsNoOverflow(1);
            $lastMonthCount = $lastMonth->month;
            $attendances = Attendance::where('employee_id', $id)->whereMonth('clockIn', $lastMonthCount)->get();
            return view('adminAttendanceStaff', compact('attendances', 'viewMonth', 'user'));
        }
    }

    public function staffNextMonth(Request $request, $id)
    {
        $user = User::find($id);
        $viewMonth = new Carbon($request->input('viewMonth'));
        if (empty($viewMonth)) {
            $nextMonth = Carbon::now()->addMonthsNoOverflow(1);
            $nextMonthCount = $nextMonth->month;
            $attendances = Attendance::where('employee_id', $id)->whereMonth('clockIn', $nextMonthCount)->get();
            return view('adminAttendanceStaff', compact('attendances', 'user'));
        } elseif (!empty($viewMonth)) {
            $nextMonth = $viewMonth->addMonthsNoOverflow(1);
            $nextMonthCount = $nextMonth->month;
            $attendances = Attendance::where('employee_id', $id)->whereMonth('clockIn', $nextMonthCount)->get();
            return view('adminAttendanceStaff', compact('attendances', 'viewMonth', 'user'));
        }
    }
}
