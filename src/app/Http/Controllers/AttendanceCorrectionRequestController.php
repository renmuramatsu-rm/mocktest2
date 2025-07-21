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
        if ($user){
            $requests = AttendanceCorrectionRequest::with('attendance')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')->get();
        return view('attendanceCorrectionRequest', compact('requests'));
        }
        if ($adminUser) {
            $requests = AttendanceCorrectionRequest::with('attendance')
                ->orderBy('created_at', 'desc')->get();
            return view('adminAttendanceCorrectionRequest', compact('requests'));
        }
    }

    public function correctionRequest(AttendanceRequest $request)
    {
        $user = Auth::user();
        if ($user) {
            $attendance = Attendance::where('employee_id', $user->id)->latest()->first();
            $attendanceRequest = AttendanceCorrectionRequest::create([
                'attendance_id' => $attendance->id,
                'user_id' => $user->id,
                'workDate' => $request -> input('workDate'),
                'requested_clockIn' => $request->input('requested_clockIn'),
                'requested_clockOut' => $request->input('requested_clockOut'),
                'remark' => $request->input('remark'),
                'requested_workTime' => round(Carbon::parse($request->input('requested_clockIn'))->diffInSeconds(
                    Carbon::parse($request->input('requested_clockOut'))) / 3600, 2
                    ),
                'status' => 'pending',
            ]);
            $request_restIns = $request->input('request_restIn', []);
            $request_restOuts = $request->input('request_restOut', []);
            foreach ($request_restIns as $i => $request_restIn) {
                if ($request_restIn || ($request_restOuts[$i] ?? null)) {
                    $attendanceRequest->requestRests()->create([
                        'request_restIn' => $request_restIn ? $request_restIn : null,
                        'request_restOut' => $request_restOuts[$i] ? $request_restOuts[$i] : null,
                        'request_restTime' => ($request_restIn && $request_restOuts[$i])
                            ? round($request_restIn->diffInSeconds($request_restOuts[$i]) / 3600, 2)
                            : 0,
                    ]);
                }
            }
            $attendanceRequest->load('requestRests');
            return view('attendanceDetail', [
                'attendanceRequest' => $attendanceRequest,
                'requestRests' => $attendanceRequest->requestRests,
            ]);
        }
    }

    public function requestApprove(AttendanceCorrectionRequest $attendance_correct_request)
    {
        $adminUser = Auth::guard('admin')->user();
        if ($adminUser) {
            $attendanceRequest = AttendanceCorrectionRequest::with('requestRests','user')->where('id', $attendance_correct_request->id)->first();

            $requestRests = $attendanceRequest ? $attendanceRequest->requestRests : [];

            return view('attendanceRequestApprove', compact('attendanceRequest', 'requestRests'));
        }
    }

    public function requestApproved(AttendanceCorrectionRequest $attendance_correct_request)
    {
        $adminUser = Auth::guard('admin')->user();
        if ($adminUser) {
        $attendance = $attendance_correct_request->attendance;

        // 出退勤の更新
        $attendance->update([
            'clockIn' => $attendance_correct_request->requested_clockIn,
            'clockOut' => $attendance_correct_request->requested_clockOut,
        ]);

        // 休憩の更新（複数対応）
        $requestedRests = $attendance_correct_request->requestRests;
        $attendanceRests = $attendance->rests;
        foreach ($requestedRests as $i => $requestedRest) {
            if (isset($attendanceRests[$i])) {
                // 既存の休憩を更新
                $attendanceRests[$i]->update([
                    'workDate' => $requestedRest  -> workDate,
                    'restIn'   => $requestedRest  -> request_restIn,
                    'restOut'  => $requestedRest  -> request_restOut,
                    'restTime' => ($requestedRest -> request_restIn && $requestedRest->request_restOut)
                        ? $requestedRest->request_restIn->diffInMinutes($requestedRest->request_restOut) / 60
                        : 0
                ]);
            } else {
                // 足りない場合は追加
                $attendance->rests()->create([
                    'workDate' => $requestedRest->workDate,
                    'restIn'   => $requestedRest  -> request_restIn,
                    'restOut'  => $requestedRest  -> request_restOut,
                    'restTime' => ($requestedRest -> request_restIn && $requestedRest->request_restOut)
                        ? $requestedRest->request_restOut->diffInMinutes($requestedRest->request_restIn) / 60
                        : 0
                ]);
            }
        }

        // 余分な休憩レコードがある場合は削除
        if (count($attendanceRests) > count($requestedRests)) {
            for ($i = count($requestedRests); $i < count($attendanceRests); $i++) {
                $attendanceRests[$i]->delete();
            }
        }

        // 総休憩時間を更新
        $attendance->total_restTime = $attendance->rests()->sum('restTime');

        // 勤務時間再計算
        if ($attendance->clockIn && $attendance->clockOut) {
            $workMinutes = $attendance->clockIn->diffInMinutes($attendance->clockOut);
            $attendance->workTime = ($workMinutes - ($attendance->total_restTime * 60)) / 60; // 時間単位
        }

        $attendance->save();

        // ステータス変更
        $attendance_correct_request->update(['status' => 'approved']);

            return redirect()->back();
        }
    }
}
