<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceCorrectionRequest;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\User;

class AttendanceCorrectionRequestController extends Controller
{
    public function requestList()
    {
        $user = Auth::user();
        $adminUser = Auth::guard('admin')->user();
        if ($adminUser) {
            $requests = AttendanceCorrectionRequest::with('attendance')
                ->orderBy('created_at', 'desc')->get();
            return view('adminAttendanceCorrectionRequest', compact('requests'));
        }
        if ($user)
            $requests = AttendanceCorrectionRequest::with('attendance')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')->get();
        return view('attendanceCorrectionRequest',compact('requests'));
    }

    public function correctionRequest(Request $request)
    {
        $user_id = Auth::user()->id;
        $attendance = Attendance::where('employee_id', $user_id)->latest()->first();
        $attendanceRequest = AttendanceCorrectionRequest::create([
            'attendance_id' => $attendance->id,
            'user_id' => $user_id,
            'requested_clockIn' => Carbon::parse($request->input('requested_clockIn')),
            'requested_clockOut' => Carbon::parse($request->input('requested_clockOut')),
            'remark' => $request->input('remark'),
        ]);
        return redirect()->back()->with('attendanceRequest', $attendanceRequest);
    }


    public function approve(Request $request, AttendanceCorrectionRequest $revision)
    {
        $attendance = $revision->attendance;
        $attendance->update([
            'clockIn' => $revision->requested_clockIn,
            'clockOut' => $revision->requested_clockOut,
        ]);

        $revision->update(['status' => 'approved']);

        return redirect()->back()->with('status', '修正申請を承認しました。');
    }
}
