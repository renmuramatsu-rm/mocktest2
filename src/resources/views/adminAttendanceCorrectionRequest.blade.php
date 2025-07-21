@extends('layouts.adminApp')

@section('css')
<link rel="stylesheet" href="{{ asset('css/adminAttendanceCorrectionRequest.css') }}">
@endsection

@section('content')
<div class="list">
    <div class="list_title">
        申請一覧
    </div>
    <div class="list_tab">
        <ul class="request">
            <li class="list__item">
                <a href="?status=pending" class="request_list">承認待ち</a>
            </li>
            <li class="list__item">
                <a href="?status=approved" class="request_list">承認済み</a>
            </li>
        </ul>
    </div>
    <table class="list_table">
        <thead class=list_table-title>
            <tr>
                <th class="list_title-name">状態</th>
                <th class="list_title-name">名前</th>
                <th class="list_title-name">対象日時</th>
                <th class="list_title-name">申請理由</th>
                <th class="list_title-name">申請日時</th>
                <th class="list_title-name">詳細</th>
            </tr>
        </thead>
        <tbody class="list_table-body">
            @php
            $status = request('status', 'pending');
            @endphp

            @foreach($requests->where('status', $status) as $request)
            <tr class="list_item">
                <td class="list_item-content">
                    @if($request->status)
                    {{ $request->status_label }}
                    @else
                    {{ '' }}
                    @endif
                </td>
                <td class="list_item-content">
                    @if($request->user->name)
                    {{ $request->user->name }}
                    @else
                    {{ '' }}
                    @endif
                </td>
                <td class="list_item-content">
                    @if($request->attendance->workDate)
                    {{ $request->attendance->workDate->format('Y/m/d') }}
                    @else
                    {{ '' }}
                    @endif
                </td>
                <td class="list_item-content">
                    @if($request->remark)
                    {{ $request->remark }}
                    @else
                    {{ '' }}
                    @endif
                </td>
                <td class="list_item-content">
                    @if($request->created_at)
                    {{ $request->created_at->format('Y/m/d') }}
                    @else
                    {{ '' }}
                    @endif
                </td>
                <td class="list_item-content">
                    <a href="
                {{ route('requestApprove',['attendance_correct_request' => $request->id]) }}">詳細</a>
                </td>
            </tr>
            @endforeach
            </tr>
        </tbody>
    </table>
</div>


@endsection