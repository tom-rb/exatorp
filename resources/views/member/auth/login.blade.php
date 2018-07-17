@extends('layouts.base')

@push('head')
    <meta name="description" content="{{ config('info.brand_name') }} - Um curso voluntário criado por alunos da Unicamp">
    <meta name="author" content="{{ config('info.author_name') }} - {{ config('info.author_site') }}">

    <meta property="og:title" content="{{ config('info.brand_name') }}" />
    <meta property="og:type" content="website" />
    <meta property="og:description" content="{{ config('info.brand_name') }} - Um curso voluntário criado por alunos da Unicamp" />
    <meta property="og:url" content="{{ config('app.url') }}" />
    {{--<meta property="og:image" content="http://demo.adminlte.acacha.org/img/AcachaAdminLTE.png" />--}}
    {{--<meta property="og:image" content="http://demo.adminlte.acacha.org/img/AcachaAdminLTE600x600.png" />--}}
    {{--<meta property="og:image" content="http://demo.adminlte.acacha.org/img/AcachaAdminLTE600x314.png" />--}}
@endpush

@section('title', 'Curso Exato - Seja bem vindo!')

@section('main-content')
    {{-- Chamada para processo seletivo de membros --}}
    @if (!is_null($selectionProcess))
        <section class="hero is-light is-bold">
            <div class="hero-body">
                <div class="columns is-vcentered">
                    <div class="column has-text-centered">
                        <div>
                            <h2 class="title is-2">Processo seletivo de voluntários</h2>

                            <p class="subtitle">
                                O processo seletivo para membros do {{ $selectionProcess->periodTitle }} está aberto.
                            </p>

                            <a class="button is-primary is-outlined"
                               href="{{ route('selection-process.application.create', $selectionProcess) }}">
                                Inscreva-se!
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif


    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container">
                <div class="columns is-vcentered">

                    <div class="column is-4 is-offset-1">
                        <h1 class="title is-1">
                            Curso Exato
                        </h1>
                        <p class="subtitle is-3">
                            Seja bem vindo ao sistema de gestão do curso.
                        </p>
                    </div>

                    <div class="column is-4 is-offset-1 has-text-centered">
                        <h2 class="title is-4">Inicie sua seção</h2>

                        {!! Form::open(['route' => 'member.auth.login']) !!}

                        @if($errors->has('email') || $errors->has('password'))
                            <b-tooltip class="is-danger" always
                                       label="{{ $errors->first('email') }} {{ $errors->first('password') }}">
                            </b-tooltip>
                        @endif

                        <div class="field">
                            <p class="control has-icons-left">
                                <input name="email" class="input" type="email" placeholder="E-mail" id="email"
                                       value="{{ old('email') }}">
                                <span class="icon is-small is-left"><i class="fa fa-envelope"></i></span>
                            </p>
                        </div>

                        <div class="field">
                            <p class="control has-icons-left">
                                <input name="password" class="input" type="password" placeholder="Senha">
                                <span class="icon is-small is-left"><i class="fa fa-lock"></i></span>
                            </p>
                        </div>

                        <div class="field has-addons">
                            <p class="control is-expanded">
                                <b-checkbox name="remember" :value="true">
                                    Lembrar de mim
                                </b-checkbox>
                            </p>

                            <p class="control">
                                <button class="button is-inverted">Entrar</button>
                            </p>
                        </div>

                        <a class="is-pulled-right" href="{{ route('member.auth.password.reset') }}">
                            {{ trans('auth.forgotpassword') }}
                        </a>

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <h3 class="title has-text-centered">Por um Exato mais ágil</h3>

        <div id="features">
            <div class="columns">
                <div class="column has-text-centered">
                    <img src="{{ asset('/img/intro01.png') }}" alt="Um balão estilo história em quadrinhos">
                    <h4 class="heading title is-4">Comunicação</h4>
                    <p>Troca de informações eficiente com um fórum personalizado para discussões da equipe.</p>
                </div>

                <div class="column has-text-centered">
                    <img src="{{ asset('/img/intro02.png') }}" alt="Uma folhinha de calendário">
                    <h4 class="heading title is-4">Planejamento</h4>
                    <p>Esteja sempre atualizado de nossas atividades e contribua facilmente para o planejamento.</p>
                </div>

                <div class="column has-text-centered">
                    <img src="{{ asset('/img/intro03.png') }}" alt="Um celular moderno">
                    <h4 class="heading title is-4">Agilidade</h4>
                    <p>Um sistema que funciona em qualquer lugar, inclusive no seu smartphone.</p>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script type="text/javascript">
    var isTouchDevice = 'ontouchstart' in document.documentElement;
    if (!isTouchDevice) {
        document.getElementById('email').focus();
    }
</script>
@endpush