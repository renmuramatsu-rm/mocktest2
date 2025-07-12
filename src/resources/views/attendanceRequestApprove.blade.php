@extends('layouts.adminApp')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendanceRequestApprove.css') }}">
@endsection

@section('content')
@if($attendanceRequest->status == 'pending')
<div class="detail">
    <form class="detail_form" action="{{ route('requestApproved',['attendance_correct_request' => $attendanceRequest->id]) }}" method="post">
        @csrf
        <div class="detail_title">
            勤怠詳細
        </div>
        <table class="detail_table">
            <tr class="detail_body">
                <th class="detail_body-name_title">
                    名前
                </th>
                <td class="detail_body-name_item">
                </td>
            </tr>
            <tr class="detail_body">
                <th class=" detail_body-date_title">
                    日付
                </th>
                <td class="detail_body-date_item">
                    {{ $attendanceRequest->requested_clockIn->format('Y年')}}
                </td>
                <td>
                </td>
                <td class="detail_body-date_item">
                    {{ $attendanceRequest->requested_clockIn->format('m月d日')}}
                </td>
            </tr>
            <tr class="detail_body">
                <th class=" detail_body-clock_title">
                    出勤・退勤
                </th>
                <td class="detail_body-clock_item">
                    {{ $attendanceRequest->requested_clockIn->format('H:i') }}
                </td>
                <td>
                    <p>~</p>
                </td>
                <td class="detail_body-clock_item">
                    {{ $attendanceRequest->requested_clockOut->format('H:i') }}
                </td>
            </tr>
            <tr class=" detail_body">
                <th class=" detail_body-break_title">
                    休憩
                </th>
                <td class="detail_body-break_item">

                </td>
                <td>
                    <p>~</p>
                </td>
                <td class=" detail_body-break_item">
                </td>
            </tr>
            <tr class="detail_body">
                <th class=" detail_body-remark_title">
                    備考
                </th>
                <td class="detail_body-remark_item">
                    {{ $attendanceRequest->remark }}
                </td>
            </tr>
        </table>
        <div class="button">
            <button class="detail_button" type="submit">
                承認
            </button>
        </div>
    </form>
</div>
@elseif(($attendanceRequest->status == 'approved'))
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
            </td>
        </tr>
        <tr class="detail_body">
            <th class=" detail_body-date_title">
                日付
            </th>
            <td class="detail_body-date_item">
                {{ $attendanceRequest->requested_clockIn->format('Y年')}}
            </td>
            <td>
            </td>
            <td class="detail_body-date_item">
                {{ $attendanceRequest->requested_clockIn->format('m月d日')}}
            </td>
        </tr>
        <tr class="detail_body">
            <th class=" detail_body-clock_title">
                出勤・退勤
            </th>
            <td class="detail_body-clock_item">
                {{ $attendanceRequest->requested_clockIn->format('H:i') }}
            </td>
            <td>
                <p>~</p>
            </td>
            <td class="detail_body-clock_item">
                {{ $attendanceRequest->requested_clockOut->format('H:i') }}
            </td>
        </tr>
        <tr class=" detail_body">
            <th class=" detail_body-break_title">
                休憩
            </th>
            <td class="detail_body-break_item">

            </td>
            <td>
                <p>~</p>
            </td>
            <td class=" detail_body-break_item">
            </td>
        </tr>
        <tr class="detail_body">
            <th class=" detail_body-remark_title">
                備考
            </th>
            <td class="detail_body-remark_item">
                {{ $attendanceRequest->remark }}
            </td>
        </tr>
    </table>
    <div class="button">
        <button class="detail_button-approved">
            承認済み
        </button>
    </div>
</div>
@endif

@endsection