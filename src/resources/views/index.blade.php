@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="mocktest2">
    <div class="stamp">
        <button class="status">
            {{ $attendance->status }}
        </button>
        <div class="date">
            {{ $date }}
        </div>
        <div class="hour">
            {{ $hour }}
        </div>
        @if($attendance->status === '出勤中')
        <form action="{{ route('clockOut') }}" method="POST">
            @csrf
            <button type="submit" class="work" name="status" value="退勤後">退勤</button>
        </form>
        <form action="{{ route('breakIn') }}" method="POST">
            @csrf
            <button type="submit" class="work" name="status" value="休憩中">休憩入</button>
        </form>
        @elseif($attendance->status === '休憩中')
        <form action="{{ route('breakOut') }}" method="POST">
            @csrf
            <button type="submit" class="work" name="status" value="出勤中">休憩戻</button>
        </form>
        @elseif($attendance->status === '退勤後')
        <form action="{{ route('clockIn') }}" method="POST">
            @csrf
            <input type="hidden" name="status" value="休憩中">
            <p>お疲れ様でした。</p>
        </form>
        @else
        <form action="{{ route('clockIn') }}" method="POST">
            @csrf
            <button type="submit" class="work" name="status" value="出勤中">出勤</button>
        </form>
        @endif
    </div>
    @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
</div>
@endsection