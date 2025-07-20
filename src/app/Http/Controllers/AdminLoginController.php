<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AdminLoginRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\AdminUser;
use App\Models\User;

class AdminLoginController extends Controller
{
    public function admin_index()
    {
        return view('admin.adminLogin');
    }

    public function admin_login(AdminLoginRequest $request)
    {
        if (Auth::check()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $request->authenticate();

        $credentials = $request->only('email', 'password');
        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('admin.attendanceList');
        }
    }

    public function adminAttendanceList(Request $request)
    {
        $adminUser = Auth::guard('admin')->user()->id;
        $today = Carbon::now()->day;
        $today = Carbon::now()->format('Y-m-d');
        $viewDay = $request->input('viewDay');
        if (empty($viewDay)) {
            $viewDay = Carbon::now()->format('Y-m-d');
            $users = User::find($adminUser)->get();
            $attendances = [];
            foreach ($users as $user) {
                $attendances[$user->id] = Attendance::where('employee_id', $user->id)->whereDate('workDate', $viewDay)->get();
            }
            return view('adminAttendanceList', compact('users', 'attendances', 'viewDay'));
        } elseif (!empty($viewDay)) {
            $users = User::find($adminUser)->get();
            $attendances = [];
            foreach ($users as $user) {
                $attendances[$user->id] = Attendance::where('employee_id', $user->id)->whereDate('workDate', $viewDay)->get();
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
            $users = User::find($adminUser)->get();
            $attendances = [];
            foreach ($users as $user) {
                $attendances[$user->id] = Attendance::where('employee_id', $user->id)->whereDate('workDate', $yesterday)->get();
            }
            return view('adminAttendanceList', compact('users', 'attendances'));
        } elseif (!empty($viewDay)) {
            $yesterday = $viewDay->subDay(1);
            $users = User::find($adminUser)->get();
            $attendances = [];
            foreach ($users as $user) {
                $attendances[$user->id] = Attendance::where('employee_id',$user->id)->whereDate('workDate', $yesterday)->get();
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
            $users = User::find($adminUser)->get();
            $attendances = [];
            foreach ($users as $user) {
                $attendances[$user->id] = Attendance::where('employee_id', $user->id)->whereDate('workDate', $tomorrow)->get();
            }
            return view('adminAttendanceList', compact('users', 'attendances'));
        } elseif (!empty($viewDay)) {
            $tomorrow = $viewDay->addDay(1);
            $users = User::find($adminUser)->get();
            $attendances = [];
            foreach ($users as $user) {
                $attendances[$user->id] = Attendance::where('employee_id', $user->id)->whereDate('workDate', $tomorrow)->get();
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
