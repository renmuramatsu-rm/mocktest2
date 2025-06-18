<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECH</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header-utilities">
                <a class="header__logo" href="/">
                    <img src="{{ asset('storage/coachtech.svg') }}" alt="coachtech" class="coachtech__img">
                </a>
                <nav>
                    <ul class="header-nav">
                        <li class="header-nav__item">
                            <form>
                                @csrf
                                <a href="{{ route('attendanceList') }}" class="header-nav__button-logout">勤怠</a>
                            </form>
                        </li>
                        <li class="header-nav__item">
                            <a href="{{ route('attendanceList') }}" class="header-nav__button-login">勤怠一覧</a>
                        </li>
                        <li class="header-nav__item">
                            <a href="" class=" header-nav__button-mypage">申請</a>
                        </li>
                        <li class=" header-nav__item">
                            <form method="POST" action="/logout">
                                @csrf
                                <button class="header-nav__button-logout" type="submit">ログアウト</button>
                            </form>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>

</html>