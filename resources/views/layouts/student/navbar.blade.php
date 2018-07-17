<nav class="navbar has-shadow is-transparent">
    <div class="navbar-brand">
        <a class="navbar-item" href="{{ route('student.home') }}">
            <figure class="image is-24x24">
                <img src="{{ asset('img/logo_24px.png') }}">
            </figure>
            <span class="curso-exato">
                Curso <strong>Exato</strong>
            </span>
        </a>
        {{-- Hamburguer icon --}}
        <button class="button navbar-burger" :class="{ 'is-active' : showMobileMenu }" @click="showMobileMenu = !showMobileMenu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>

    <div class="navbar-menu" :class="{ 'is-active' : showMobileMenu }">
        <div class="navbar-end">
            @include('layouts.student.menu', ['classes' => 'navbar-item is-tab is-hidden-tablet'])

            @if (!Auth::guard('student')->guest())

                <p class="navbar-item">
                    <span class="icon is-small is-spaced-right-05"><i class="fa fa-user"></i></span>
                    {{ Auth::guard('student')->user()->name }}
                </p>

                <div class="navbar-item">
                    <a class="button is-small" href="{{ route('student.auth.logout') }}">
                        <span class="icon is-small"><i class="fa fa-times"></i></span><span>Sair</span>
                    </a>
                </div>

            @else
                <div class="navbar-item">
                    <a class="button is-small" href="{{ route('student.auth.login') }}">
                        <span class="icon is-small"><i class="fa fa-arrow-right"></i></span><span>Entrar</span>
                    </a>
                </div>
            @endif
        </div>
    </div>
</nav>