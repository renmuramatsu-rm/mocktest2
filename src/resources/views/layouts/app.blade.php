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
                        @if (Auth::check())
                        <li class="header-nav__item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="header-nav__button-logout">ログアウト</button>
                            </form>
                        </li>
                        @else
                        <li class="header-nav__item">
                            <a href="{{ route('login') }}" class="header-nav__button-login">ログイン</a>
                        </li>
                        @endif
                        <li class="header-nav__item">
                            <a href="{{ route('mypage') }}" class=" header-nav__button-mypage">マイページ</a>
                        </li>
                        <li class=" header-nav__item">
                            <form action="/sell" method="get">
                                @csrf
                                <button class="header-nav__button-sell" type="submit">出品</button>
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