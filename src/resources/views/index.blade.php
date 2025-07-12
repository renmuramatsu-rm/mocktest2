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
            <p id="clock"></p>
            <script>
                function showClock() {
                    const now = new Date();
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    const time = hours + ':' + minutes;
                    document.getElementById('clock').textContent = time;
                }
                setInterval('showClock()', 1000);
            </script>
        </div>
        @if($attendance->status === '出勤中')
        <div class="button-row">
            <form action="{{ route('clockOut') }}" method="POST">
                @csrf
                <button type="submit" class="work" name="status" value="退勤済">退勤</button>
            </form>
            <form action="{{ route('restIn') }}" method="POST">
                @csrf
                <button type="submit" class="work" name="status" value="休憩中">休憩入</button>
            </form>
        </div>
        @elseif($attendance->status === '休憩中')
        <form action="{{ route('restOut') }}" method="POST">
            @csrf
            <button type="submit" class="work" name="status" value="出勤中">休憩戻</button>
        </form>
        @elseif($attendance->status === '退勤済')
        <form action="{{ route('clockIn') }}" method="POST">
            @csrf
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