@extends('layouts.adminApp')

@section('css')
<link rel="stylesheet" href="{{ asset('css/adminAttendanceDetail.css') }}">
@endsection

@section('content')
<div class="detail">
    <div class="detail_title">
        勤怠詳細
    </div>
    <table class="detail_table">
        <tr class="detail_body">
            <th class="detail_body-name_title">
                名前
            </th>
            <td class="detail_body-name_item">
                {{ $attendance->user->name }}
            </td>
        </tr>
        <tr class="detail_body">
            <th class=" detail_body-date_title">
                日付
            </th>
            <td class="detail_body-date_item">
                @if($attendance->clockIn)
                {{ $attendance->clockIn->format('Y年')}}
                @else
                {{ '' }}
                @endif
            </td>
            <td class="detail_body-date_item">
                @if($attendance->clockIn)
                {{ $attendance->clockIn->format('m月d日')}}
                @else
                {{ '' }}
                @endif
            </td>
        </tr>
        <tr class="detail_body">
            <th class=" detail_body-clock_title">
                出勤・退勤
            </th>
            <td class="detail_body-clock_item">
                @if($attendance->clockIn)
                <input type="datetime" name="clockIn" value="{{ $attendance->clockIn->format('H:i') }}">
                @else
                <input type="datetime" name="requested_clockIn" value="{{ '' }}">
                @endif
            </td>
            <td class="detail_body-clock_item">
                @if($attendance->clockOut)
                <input type="datetime" name="clockOut" placeholder="{{ $attendance->clockOut->format('H:i') }}">
                @else
                <input type="datetime" name="requested_clockOut" value="{{ '' }}">
                @endif
            </td>
        </tr>
        <tr class=" detail_body">
            <th class=" detail_body-break_title">
                休憩
            </th>
            <td class="detail_body-break_item">
                @if($attendance->breakIn)
                <input type="datetime" name="breakIn" placeholder="{{ $attendance->breakIn->format('H:i') }}">
                @else
                <input type="datetime" name="breakIn" value="{{ '' }}">
                @endif
            </td>
            <td class=" detail_body-break_item">
                @if($attendance->breakOut)
                <input type="datetime" name="breakOut" placeholder="{{ $attendance->breakOut->format('H:i') }}">
                @else
                <input type="datetime" name="breakOut" value="{{ '' }}">
                @endif
            </td>
        </tr>
        <tr class=" detail_body">
            <th class=" detail_body-break2_title">
                休憩２
            </th>
            <td class="detail_body-break2_item">

            </td>
        </tr>
        <tr class="detail_body">
            <th class=" detail_body-remark_title">
                備考
            </th>
            <td class="detail_body-remark_item">
                <input type="textarea" name="remark" placeholder="">
            </td>
        </tr>
    </table>
    <button class="detail_button">
        修正
    </button>
</div>


@endsection