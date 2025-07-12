@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/registerMail.css') }}">
@endsection

@section('content')
<div class="register-container">
    <p class="register-message">
        登録していただいたメールアドレスに認証メールを送付しました。
        メール認証を完了してください
    </p>
    <form method="post" action="{{ route('verification.send') }}">
        @csrf
        <button class="register-button">
            認証メールを再送する</button>
    </form>
</div>


@endsection