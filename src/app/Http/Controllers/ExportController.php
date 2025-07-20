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
        $user = User::find($id);
        $viewMonth = $request->input('viewMonth') ?: Carbon::now()->format('Y-m-d');

        $year = date('Y', strtotime($viewMonth));
        $month = date('m', strtotime($viewMonth));
        $firstDay = Carbon::create($year, $month, 1);
        $lastDay = $firstDay->copy()->endOfMonth();
        $week = ['(日)', '(月)', '(火)', '(水)', '(木)', '(金)', '(土)'];

        $monthDayLists = [];
        for ($date = $firstDay->copy(); $date->lte($lastDay); $date->addDay()) {
            $attendance = Attendance::with('rests')->where('employee_id', $id)
                ->whereDate('workDate', $date->format('Y-m-d'))->first();

            $monthDayLists[] = [
                'format_date' => $date->format('m/d') . $week[$date->dayOfWeek],
                'attendance' => $attendance,
            ];
        }

        $csvHeader = ['日付', '出勤', '退勤', '休憩', '合計'];

        return new StreamedResponse(function () use ($csvHeader, $monthDayLists) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
            fputcsv($handle, $csvHeader);

            foreach ($monthDayLists as $data) {
                $attendance = $data['attendance'];
                fputcsv($handle, [
                    $data['format_date'],
                    $attendance->clockIn ?? '',
                    $attendance->clockOut ?? '',
                    $attendance->total_restTime ?? '',
                    $attendance->workTime ?? '',
                ]);
            }
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="attendances.csv"',
        ]);
    }
}
