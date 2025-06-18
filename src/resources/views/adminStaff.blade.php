@extends('layouts.adminApp')

@section('css')
<link rel="stylesheet" href="{{ asset('css/adminStaff.css') }}">
@endsection

@section('content')
<div class="staff">
    <div class="staff_title">
        スタッフ一覧
    </div>
    <table class="staff_table">
        <thead class=staff_table-title>
            <tr>
                <th class="staff_title-name">名前</th>
                <th class="staff_title-mail">メールアドレス</th>
                <th class="staff_title-work">月次勤怠</th>
            </tr>
        </thead>
        <tbody class="staff_table-body">
            @foreach($users as $user)
            <tr class="staff_item">
                <td class="staff_item-content">
                    @if($user->name)
                    {{ $user->name }}
                    @else
                    {{ '' }}
                    @endif
                </td>
                <td class="staff_item-content">
                    @if($user->email)
                    {{ $user->email }}
                    @else
                    {{ '' }}
                    @endif
                </td>
                <td class="staff_item-content">
                    <a href="{{ route('admin.attendanceStaff', $user->id) }}">詳細</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection