@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendanceDetail.css') }}">
@endsection

@section('content')

@if($attendanceRequest && $attendanceRequest->status == 'pending')
<div class="detail">
    <div class="detail_form">
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
                    {{ $attendanceRequest->user->name }}
                </td>
            </tr>
            <tr class="detail_body">
                <th class=" detail_body-date_title">
                    日付
                </th>
                <td class="detail_body-date_item">
                    {{ $attendanceRequest->workDate->format('Y年')}}
                </td>
                <td>
                </td>
                <td class="detail_body-date_item">
                    {{ $attendanceRequest->workDate->format('m月d日')}}
                </td>
            </tr>
            <tr class="detail_body">
                <th class=" detail_body-clock_title">
                    出勤・退勤
                </th>
                <td class="detail_body-clock_item">
                    {{ $attendanceRequest->requested_clockIn->format('H:i') }}
                </td>
                <td class="center-mark">
                    <p>~</p>
                </td>
                <td class="detail_body-clock_item">
                    {{ $attendanceRequest->requested_clockOut->format('H:i') }}
                </td>
            </tr>
            @foreach($requestRests as $requestRest)
            <tr class=" detail_body">
                <th class=" detail_body-break_title">
                    休憩
                </th>
                <td class="detail_body-break_item">
                    @if($requestRest->request_restIn)
                    <p>{{ $requestRest->request_restIn->format('H:i') }}</p>
                    @else
                    <p>{{ '' }}</p>
                    @endif
                </td>
                <td class="center-mark">
                    <p>~</p>
                </td>
                <td class=" detail_body-break_item">
                    @if($requestRest->request_restOut)
                    <p>{{ $requestRest->request_restOut->format('H:i') }}</p>
                    @else
                    <p>{{ '' }}</p>
                    @endif
                </td>
            </tr>
            @endforeach
            <tr class="detail_body">
                <th class=" detail_body-remark_title">
                    備考
                </th>
                <td class="detail_body-remark_item">
                    {{ $attendanceRequest->remark }}
                </td>
            </tr>
        </table>
        <div class="detail_comment">
            <p class="detail_comment-item">*承認待ちのため修正はできません。</p>
        </div>
    </div>
</div>
@else
<div class="detail">
    <form class="detail_form" action="{{ route('edit', $attendance->id)}}" method="post">
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
                    {{ $attendance->user->name }}
                </td>
            </tr>
            <tr class="detail_body">
                <th class=" detail_body-date_title">
                    日付
                </th>
                <td class="detail_body-date_item">
                    @if($attendance->workDate)
                    {{ $attendance->workDate->format('Y年')}}
                    <input type="hidden" name="workDate" value="{{ $attendance->workDate }}">
                    @else
                    {{ '' }}
                    @endif
                </td>
                <td>
                </td>
                <td class="detail_body-date_item">
                    @if($attendance->workDate)
                    {{ $attendance->workDate->format('m月d日')}}
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
                    <input type="dateTime" name="requested_clockIn" value="{{ $attendance->clockIn->format('H:i') }}">
                    @else
                    <input type="datetime" name="requested_clockIn" value="{{ '' }}">
                    @endif
                </td>
                <td class="center-mark">
                    <p>~</p>
                </td>
                <td class="detail_body-clock_item">
                    @if($attendance->clockOut)
                    <input type="datetime" name="requested_clockOut" value="{{ $attendance->clockOut->format('H:i') }}">
                    @else
                    <input type="datetime" name="requested_clockOut" value="{{ '' }}">
                    @endif
                </td>
            </tr>
            @foreach($rests as $rest)
            <tr class=" detail_body">
                <th class=" detail_body-break_title">
                    休憩
                </th>
                <td class="detail_body-break_item">
                    @if($rest->restIn)
                    <input type="datetime" name="request_restIn[]" value="{{ $rest->restIn->format('H:i') }}">
                    @else
                    <input type="datetime" name="request_restIn[]" value="{{ '' }}">
                    @endif
                </td>
                <td class="center-mark">
                    <p>~</p>
                </td>
                <td class=" detail_body-break_item">
                    @if($rest->restOut)
                    <input type="datetime" name="request_restOut[]" value="{{ $rest->restOut->format('H:i') }}">
                    @else
                    <input type="datetime" name="request_restOut[]" value="{{ '' }}">
                    @endif
                </td>
            </tr>
            @endforeach
            <tr class=" detail_body">
                <th class=" detail_body-break2_title">
                    休憩２
                </th>
                <td class="detail_body-break_item">
                    <input type="datetime" name="request_restIn[]">
                </td>
                <td class="center-mark">
                    <p>~</p>
                </td>
                <td class=" detail_body-break_item">
                    <input type="datetime" name="request_restOut[]">
                </td>
            </tr>
            <tr class="detail_body">
                <th class=" detail_body-remark_title">
                    備考
                </th>
                <td class="detail_body-remark_item" colspan="3">
                    <textarea name="remark"></textarea>
                </td>
            </tr>
        </table>

        @error('requested_clockIn')
        <div class="form__error">
            {{ $message }}
        </div>
        @enderror
        @error('remark')
        <div class="form__error">
            {{ $message }}
        </div>
        @enderror

        <div class="button">
            <button class="detail_button" type="submit">
                修正
            </button>
        </div>
    </form>
</div>
@endif

@endsection