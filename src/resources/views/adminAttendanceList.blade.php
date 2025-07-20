@extends('layouts.adminApp')

@section('css')
<link rel="stylesheet" href="{{ asset('css/adminAttendanceList.css') }}">
@endsection

@section('content')
<div class="list">
    <div class="list_title">
        {{ \Carbon\Carbon::parse($viewDay)->format('Y年m月d日') }}の勤怠
    </div>
    <div class="list_select">
        <form action="{{ route('listYesterday') }}" method="get">
            <input type="hidden" name="viewDay" value="{{ $viewDay ?? "" }}">
            <input class="btn" type="submit" value="前日">
        </form>
        <div>
            <form action="{{ route('admin.attendanceList') }}" method="get">
                <input type="date" name="viewDay" value="{{ $viewDay }}">
                <input type="submit" value="表示">
            </form>
        </div>
        <div>
            <form action="{{ route('listTomorrow')}}" method="get">
                <input type="hidden" name="viewDay" value="{{ $viewDay ?? "" }}">
                <input class="btn" type="submit" value="翌日">
        </div>
    </div>
    <table class="list_table">
        <thead class=list_table-title>
            <tr>
                <th class="list_title-name">名前</th>
                <th class="list_title-name">出勤</th>
                <th class="list_title-name">退勤</th>
                <th class="list_title-name">休憩</th>
                <th class="list_title-name">合計</th>
                <th class="list_title-name">詳細</th>
            </tr>
        </thead>
        <tbody class="list_table-body">
            @foreach($users as $user)
            @foreach ($attendances[$user->id] as $attendance)
            <tr class="list_item">
                <td class="list_item-content">
                    @if($user->name)
                    {{ $user->name }}
                    @else
                    {{ '' }}
                    @endif
                </td>
                <td class="list_item-content">
                    @if($attendance->clockIn)
                    {{ $attendance->clockIn->format('H:i') }}
                    @else
                    {{ '' }}
                    @endif
                </td>
                <td class="list_item-content">
                    @if($attendance->clockOut)
                    {{ $attendance->clockOut->format('H:i') }}
                    @else
                    {{ '' }}
                    @endif
                </td>
                <td class="list_item-content">
                    @if($attendance->total_restTime)
                    {{ $attendance->formatted_total_restTime }}
                    @else{{ ''}}
                    @endif
                </td>
                <td class="list_item-content">
                    @if($attendance->workTime)
                    {{ $attendance->formatted_workTime }}
                    @else{{ ''}}
                    @endif
                </td>
                <td class="list_item-content">
                    <a href="
                    {{ route('detail',$attendance->id) }}">
                        詳細</a>
                </td>
            </tr>
            @endforeach
            @endforeach
        </tbody>
    </table>
</div>


@endsection