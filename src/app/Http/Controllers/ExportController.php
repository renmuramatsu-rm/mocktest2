<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Carbon;

class ExportController extends Controller
{
    public function export(Request $request, $id)
    {
        $viewMonth = $request->input('viewMonth');
        if (empty($viewMonth)) {
            $viewMonth = Carbon::now()->format('m');


        } elseif (!empty($viewMonth)) {
            $viewMonthInput = new Carbon($request->input('viewMonth'));
            $viewMonth = $viewMonthInput->format('Y-m-d');
        }

        $attendances = Attendance::where('employee_id', $id)->whereDate('workDate', $viewMonth)->first();;

        $csvHeader = [
            '名前',
            '日付',
            '出勤',
            '退勤',
            '休憩',
            '合計',
        ];

        $response = new StreamedResponse(
            function () use ($csvHeader, $attendances) {
                $handle = fopen('php://output', 'w');

                fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));


                fputcsv($handle, $csvHeader);

                foreach ($attendances as $attendance) {
                    fputcsv($handle, [
                        $attendance->last_name,
                        $attendance->first_name,
                        $attendance->email,
                        $attendance->phone,
                        $attendance->address,
                        $attendance->building,
                        $attendance->category->content ?? '',
                        $attendance->message,
                    ]);
                }

                fclose($handle);
            },
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="attendances.csv"',
            ]
        );
        return $response;
    }
}
