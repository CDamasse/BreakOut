<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BreakOut') }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>

    <!-- Styles -->
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
</head>
<body>
    <div id="app" class="app">
        <nav class="navbar navbar-expand-lg navbar-light breakout-navbar">
            <a class="navbar-brand" href="{{ url('/') }}">
                <div class="navbar-logo"></div>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse navbar-left" id="navbarTogglerDemo02">
                <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                    <li class="nav-item active">
                        <a class="nav-link nav-link-info nav-link-icones" href="#"><span class="nav-link-icones-span">Info</span><span class="sr-only">(current)</span></a>
                    </li>
                </ul>
                <ul class="navbar-nav my-2 my-lg-0">
                    <li class="nav-item">
                        <a class="nav-link nav-link-home nav-link-icones" href="{{ url('/') }}"><span class="nav-link-icones-span">Accueil</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-room nav-link-icones" href="{{ url('/rooms') }}"><span class="nav-link-icones-span">Salles</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-forum nav-link-icones" href="{{ url('/forum') }}"><span class="nav-link-icones-span">Forum</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-shop nav-link-icones" href="{{ url('/shop') }}"><span class="nav-link-icones-span">Boutique</span></a>
                    </li>
                    @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link nav-link-parameters nav-link-icones dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="nav-link-icones-span">Paramètres</span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <a class="dropdown-item" href="#">Mon compte</a>
                            <a class="dropdown-item" href="#">Mon avatar</a>
                            <a class="dropdown-item" href="#">Inventaire</a>
                            <a class="dropdown-item" href="#">Mes salles</a>
                        </div>
                    </li>
                    @endauth
                    <li class="nav-item pr-4">
                        <a class="nav-link nav-link-sound nav-link-icones" href=""></a>
                    </li>
                    <li class="nav-item navbar-button">
                        @guest
                            <button class="navbar-profile-button" data-toggle="modal" data-target="#modalconnexion">
                                <span>Se connecter</span>
                            </button>
                            <div class="modal fade" id="modalconnexion" tabindex="-1" aria-labelledby="modalconnexion" aria-hidden="true">
                                <div class="modal-dialog login-modal-dialog modal-dialog-centered">
                                    <div class="login-modal-content">
                                        <div class="login-modal-header">
                                            <div></div>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1 !important;">
                                                <div aria-hidden="true" class="modal-cross"></div>
                                            </button>
                                        </div>
                                        <div class="login-modal-body">
                                            <ul class="nav nav-pills justify-content-between mb-3" id="pills-tab" role="tablist">
                                                <li class="nav-item onglet-modal-login margin-modal-login-left" role="presentation">
                                                    <a class="nav-link w-100 active" id="pills-connect-tab" data-toggle="pill" href="#pills-connect" role="tab" aria-controls="pills-connect" aria-selected="true">Se connecter</a>
                                                </li>
                                                <li class="nav-item onglet-modal-login margin-modal-login-right" role="presentation">
                                                    <a class="nav-link w-100" id="pills-register-tab" data-toggle="pill" href="#pills-register" role="tab" aria-controls="pills-register" aria-selected="false">S'inscrire</a>
                                                </li>
                                            </ul>
                                            <div class="tab-content" id="pills-tabContent">
                                                <div class="tab-pane d-flex justify-content-between fade show active" id="pills-connect" role="tabpanel" aria-labelledby="pills-connect-tab">
                                                    <div class="onglet-modal-login-body margin-modal-login-left" style="margin-top: 0 !important; margin-bottom: 10px !important;">
                                                    </div>
                                                    <div class="onglet-modal-login-body margin-modal-login-right" style="margin-top: 0 !important; margin-bottom: 10px !important;">
                                                        <div class="w-100 h-100 p-3">
                                                            <div class="d-flex justify-content-center">
                                                                <img class="modal-login-logo" src="{{ asset('img/logos/BreakOut_Logotype_Citation.png') }}">
                                                            </div>
                                                            <form method="POST" action="{{ route('login')}}" class="mt-4">
                                                                @csrf
                                                                <label for="login-mail" class="modal-login-credentials-text">Nom d'utilisateur :</label>
                                                                <input id="login-mail" class="input-login @error('email') is-invalid @enderror" type="email" placeholder="Votre email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                                                    @error('email')
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                    @enderror
                                                                <label for="login-password" class="modal-login-credentials-text mt-2">Mot de passe :</label>
                                                                <div class="input-group">
                                                                    <input id="login-password  @error('password') is-invalid @enderror" type="password" class="form-control input-login-2" placeholder="Mot de passe" name="password" required autocomplete="current-password">
                                                                    <div class="input-group-append">
                                                                        <button class="btn btn-outline-secondary input-login-btn" type="submit">GO</button>
                                                                    </div>
                                                                </div>
                                                                    @error('password')
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                    @enderror
                                                                <div class="w-100 text-center mt-1">
                                                                    @if (Route::has('password.request'))
                                                                        <a class="small-text-login" href="{{ route('password.request') }}">
                                                                            <small>Mot de passe oublié ?</small>
                                                                        </a>
                                                                    @endif
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade" id="pills-register" role="tabpanel" aria-labelledby="pills-register-tab">

                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <button class="navbar-profile-button" >
                                <span>Mon profil</span>
                            </button>
                        @endguest
                    </li>
                    <!-- Authentication Links -->
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </nav>
        <main class="">
            @yield('content')
        </main>
    </div>
</body>
</html>
