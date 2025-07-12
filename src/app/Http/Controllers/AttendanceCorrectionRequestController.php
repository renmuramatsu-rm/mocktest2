<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceCorrectionRequest;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\RequestRest;
use App\Http\Requests\AttendanceRequest;

class AttendanceCorrectionRequestController extends Controller
{
    public function requestList()
    {
        $user = Auth::user();
        $adminUser = Auth::guard('admin')->user();
        // sessionを削除してからログイン処理を行う(パターンを網羅する)
        if ($adminUser) {
            $requests = AttendanceCorrectionRequest::with('attendance')
                ->orderBy('created_at', 'desc')->get();
            return view('adminAttendanceCorrectionRequest', compact('requests'));
        }
        if ($user)
            $requests = AttendanceCorrectionRequest::with('attendance')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')->get();
        return view('attendanceCorrectionRequest', compact('requests'));
    }

    public function correctionRequest(AttendanceRequest $request)
    {
        $user = Auth::user();
        if ($user) {
            $attendance = Attendance::where('employee_id', $user->id)->latest()->first();
            $attendanceRequest = AttendanceCorrectionRequest::create([
                'attendance_id' => $attendance->id,
                'user_id' => $user->id,
                'requested_clockIn' => Carbon::parse($request->input('requested_clockIn')),
                'requested_clockOut' => Carbon::parse($request->input('requested_clockOut')),
                'remark' => $request->input('remark'),
                'status' => 'pending',
            ]);
            $request_restIns = $request->input('request_restIn', []);
            $request_restOuts = $request->input('request_restOut', []);
            foreach ($request_restIns as $i => $request_restIn) {
                if ($request_restIn || ($request_restOuts[$i] ?? null)) {
                    $attendanceRequest->requestRest()->create([
                        'request_restIn' => $request_restIn ? Carbon::parse($request_restIn) : null,
                        'request_restOut' => $request_restOuts[$i] ? Carbon::parse($request_restOuts[$i]) : null,
                    ]);
                }
            }
            $attendanceRequest->load('requestRest');
            return view('attendanceDetail', [
                'attendanceRequest' => $attendanceRequest,
                'requestRests' => $attendanceRequest->requestRest,
            ]);
        }
    }

    public function requestApprove(AttendanceCorrectionRequest $attendance_correct_request)
    {
        $adminUser = Auth::guard('admin')->user();
        if ($adminUser) {
            $attendanceRequest = AttendanceCorrectionRequest::where('id', $attendance_correct_request->id)->first();
            return view('attendanceRequestApprove', compact('attendanceRequest'));
        }
    }

    public function requestApproved(AttendanceCorrectionRequest $attendance_correct_request)
    {
        $adminUser = Auth::guard('admin')->user();
        if ($adminUser) {
            $attendance = $attendance_correct_request->attendance;
            $attendance->update([
                'clockIn' => $attendance_correct_request->requested_clockIn,
                'clockOut' => $attendance_correct_request->requested_clockOut,
            ]);

            $requestedRests = $attendance_correct_request->requestRest;
            $attendanceRests = $attendance->rests;

            foreach ($attendanceRests as $i => $rest) {
                if (isset($requestedRests[$i])) {
                    $rest->update([
                        'restIn' => $requestedRests[$i]->request_restIn,
                        'restOut' => $requestedRests[$i]->request_restOut,
                    ]);
                }
            }
            $attendance_correct_request->update(['status' => 'approved']);

            return redirect()->back();
        }
    }
}
