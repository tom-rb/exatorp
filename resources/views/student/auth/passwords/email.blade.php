@extends('layouts.base')
@section('title', trans('auth.recoverpassword'))

@section('main-content')
    <section class="section">
        <div class="container">
            <div class="heading">
                <h1 class="title">Recuperar senha</h1>
            </div>
            <div class="content">
                <p>Informe seu email cadastrado que iremos enviar um link para recuperar sua senha.</p>

                <form action="{{ route('student.auth.password.email') }}" method="post" accept-charset="UTF-8">
                    {{ csrf_field() }}

                    @include('form.input', ['id' => 'email', 'type' => 'email', 'icon' => 'envelope',
                                            'placeholder' => 'E-mail', 'autofocus' => true])

                    @include('form.button', ['label' => 'Enviar link por email', 'icon' => 'link', 'class' => 'is-primary'])
                </form>
            </div>

            @if (session('status'))
                <div class="notification is-primary">
                    {{ session('status') }}
                </div>
            @endif
        </div>
    </section>
@endsection
