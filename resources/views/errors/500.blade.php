@extends('layouts.base')
@section('title')
    500 - {{ trans('errors.servererror') }}
@stop

@section('main-content')
    <section class="hero is-warning is-medium is-bold">
        <div class="hero-body">
            <div class="container">
                <h1 class="title">
                    <span class="icon is-medium"><i class="fa fa-warning"></i></span>
                    <span>500 - {{ trans('errors.somethingwrong') }}</span>
                </h1>
                <h2 class="subtitle">
                    {{ trans('errors.wewillwork') }}
                </h2>
            </div>
        </div>
    </section>
@endsection
