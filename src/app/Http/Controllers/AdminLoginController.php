<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AdminLoginRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\AdminUser;

class AdminLoginController extends Controller
{
    public function admin_index()
    {
        return view('admin.adminLogin');
    }

    public function admin_login(AdminLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->route('admin.attendanceList');
        }
    }

    public function adminAttendanceList(Request $request)
    {
        $adminUser = Auth::guard('admin')->user()->id;
        $today = Carbon::now()->day;
        $today = Carbon::now()->format('Y-m-d');
        $viewDate = $request->input('viewDay');
        if (empty($viewDay)) {
            $viewDay = Carbon::now()->format('Y-m-d');
            $users = AdminUser::find($adminUser)->users;
            foreach ($users as $user) {
                $user_id = $user->id;
                $attendances = Attendance::where('employee_id', $user_id)->whereDate('clockIn', $viewDay)->get();
            }
            return view('adminAttendanceList', compact('users', 'attendances', 'viewDay'));
        } elseif (!empty($viewDay)) {
            $viewDayInput = new Carbon($request->input('viewDay'));
            $viewDay = $viewDayInput->format('Y-m-d');
            $newDay = $viewDayInput->format('m');
            $users = AdminUser::find($adminUser)->users;
            foreach ($users as $user) {
                $user_id = $user->id;
                $attendances = Attendance::where('employee_id', $user_id)->whereDate('clockIn', $viewDay)->get();
            }
            return view('adminAttendanceList', compact('users', 'attendances', 'viewDay'));
        }
    }
    public function listYesterday(Request $request)
    {
        $adminUser = Auth::guard('admin')->user()->id;
        $viewDay = new Carbon($request->input('viewDay'));
        if (empty($viewDay)) {
            $yesterday = Carbon::now()->subDay(1);
            $yesterdayCount = $yesterday->day;
            $users = AdminUser::find($adminUser)->users;
            foreach ($users as $user) {
                $user_id = $user->id;
                $attendances = Attendance::where('employee_id', $user_id)->whereDate('clockIn', $yesterdayCount)->get();
            }
            return view('adminAttendanceList', compact('users', 'attendances'));
        } elseif (!empty($viewDay)) {
            $yesterday = $viewDay->subDay(1);
            $yesterdayCount = $yesterday->day;
            $users = AdminUser::find($adminUser)->users;
            foreach ($users as $user) {
                $user_id = $user->id;
                $attendances = Attendance::where('employee_id', $user_id)->whereDate('clockIn', $yesterdayCount)->get();
            }
            return view('adminAttendanceList', compact('users', 'attendances', 'viewDay'));
        }
    }

    public function listTomorrow(Request $request)
    {
        $adminUser = Auth::guard('admin')->user()->id;
        $viewDay = new Carbon($request->input('viewDay'));
        if (empty($viewDay)) {
            $tomorrow = Carbon::now()->addDay(1);
            $tomorrowCount = $tomorrow->day;
            $users = AdminUser::find($adminUser)->users;
            foreach ($users as $user) {
                $user_id = $user->id;
                $attendances = Attendance::where('employee_id', $user_id)->whereDate('clockIn', $tomorrowCount)->get();
            }
            return view('adminAttendanceList', compact('users','attendances'));
        } elseif (!empty($viewDay)) {
            $tomorrow = $viewDay->addDay(1);
            $tomorrowCount = $tomorrow->day;
            $users = AdminUser::find($adminUser)->users;
            foreach ($users as $user) {
                $user_id = $user->id;
                $attendances = Attendance::where('employee_id', $user_id)->whereDate('clockIn', $tomorrowCount)->get();
            }
            return view('adminAttendanceList', compact('users', 'attendances', 'viewDay'));
        }
    }

    public function admin_logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return to_route('admin.login');
    }
}
