<nav class="navbar has-shadow is-transparent">
    <div class="navbar-brand">
        <a class="navbar-item" href="{{ route('member.home') }}">
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
            @include('layouts.member.menu', ['classes' => 'navbar-item is-tab is-hidden-tablet'])

            @if (!Auth::guest())

                <a class="navbar-item" href="{{ route('member.show', Auth::user()) }}">
                    <span class="icon is-small is-spaced-right-05"><i class="fa fa-user"></i></span>
                    {{ Auth::user()->name }}
                </a>

                <div class="navbar-item">
                    @if (Auth::user()->isImpersonating())
                        <a class="button is-small is-danger" href="{{ route('impersonate.stop') }}">
                            <span class="icon is-small"><i class="fa fa-times"></i></span> <strong>Parar de atuar</strong>
                        </a>
                    @else
                        <a class="button is-small" href="{{ route('member.auth.logout') }}">
                            <span class="icon is-small"><i class="fa fa-times"></i></span><span>Sair</span>
                        </a>
                    @endif
                </div>

            @else
                <div class="navbar-item">
                    <a class="button is-small" href="{{ route('member.welcome') }}">
                        <span class="icon is-small"><i class="fa fa-arrow-right"></i></span><span>Entrar</span>
                    </a>
                </div>
            @endif
        </div>
    </div>
</nav>