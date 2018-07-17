@extends('layouts.base')
@section('title', trans('auth.resetpassword'))

@section('main-content')
    <section class="section">
        <div class="container">
            <div class="heading">
                <h1 class="title">Redefinição de senha</h1>
            </div>
            <div class="content">
                <p>Confirme seu email e defina uma nova senha.</p>

                <form action="{{ route('student.auth.password.reset') }}" method="post" accept-charset="UTF-8">
                    {{ csrf_field() }}
                    <input type="hidden" name="token" value="{{ $token }}">

                    @include('form.input', ['id' => 'email', 'type' => 'email', 'icon' => 'envelope',
                                            'placeholder' => 'E-mail', 'autofocus' => true])

                    @include('form.input', ['id' => 'password', 'type' => 'password', 'icon' => 'lock',
                                            'placeholder' => 'Digite uma senha'])

                    @include('form.input', ['id' => 'password_confirmation', 'type' => 'password', 'icon' => 'check',
                                            'placeholder' => 'Confirme a senha'])

                    @include('form.button', ['label' => 'Trocar a senha', 'icon' => 'check-square', 'class' => 'is-primary'])
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