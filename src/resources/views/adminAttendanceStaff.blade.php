@extends('layouts.adminApp')

@section('css')
<link rel="stylesheet" href="{{ asset('css/adminAttendanceStaff.css') }}">
@endsection

@section('content')
<div class="list">
    <div class="list_title">
        勤怠一覧
    </div>
    <div class="list_select">
        <form action="{{ route('admin.staffLastMonth',$user->id) }}" method="get">
            <input type="hidden" name="viewMonth" value="{{ $viewMonth ?? ""}}">
            <input class="btn" type="submit" value="前月">
        </form>
        <div>
            <form action="{{ route('admin.attendanceStaff', $user->id) }}" method="get">
                <input type="date" name="viewMonth" value="{{ $viewMonth }}">
                <input type="submit" value="表示">
            </form>
        </div>
        <div>
            <form action="{{ route('admin.staffNextMonth', $user->id)}}" method="get">
                <input type="hidden" name="viewMonth" value="{{ $viewMonth ?? "" }}">
                <input class="btn" type="submit" value="翌月">
        </div>
    </div>
    <table class="list_table">
        <thead class=list_table-title>
            <tr>
                <th class="list_title-name">日付</th>
                <th class="list_title-name">出勤</th>
                <th class="list_title-name">退勤</th>
                <th class="list_title-name">休憩</th>
                <th class="list_title-name">合計</th>
                <th class="list_title-name">詳細</th>
            </tr>
        </thead>
        <tbody class="list_table-body">
            @foreach($month_day_lists as $month_day_list)
            <tr class="list_item">
                <td class="list_item-content">
                    {{ $month_day_list['format_date'] }}
                </td>
                <td class="list_item-content">
                    @if(isset($month_day_list['attendance']) && $month_day_list['attendance']->clockIn)
                    {{ $month_day_list['attendance']->clockIn->format('H:i')}}
                    @else
                    {{ '' }}
                    @endif
                </td>
                <td class="list_item-content">
                    @if(isset($month_day_list['attendance']) && $month_day_list['attendance']->clockOut)
                    {{ $month_day_list['attendance']->clockOut->format('H:i') }}
                    @else
                    {{ '' }}
                    @endif
                </td>
                <td class="list_item-content">
                    @if(isset($month_day_list['attendance']) && $month_day_list['attendance']->total_restTime)
                    {{ $month_day_list['attendance']->total_restTime }}時間
                    @else{{ ''}}
                    @endif
                </td>
                <td class="list_item-content">
                    @if(isset($month_day_list['attendance']) && $month_day_list['attendance']->workTime)
                    {{ $month_day_list['attendance']->workTime }}時間
                    @else{{ ''}}
                    @endif
                </td>
                <td class="list_item-content">
                    @if(isset($month_day_list['attendance']) && $month_day_list['attendance']->id)
                    <a href="
                    {{ route('detail',$month_day_list['attendance']->id) }}">
                        詳細</a>
                    @else<p>詳細</p>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>



@endsection