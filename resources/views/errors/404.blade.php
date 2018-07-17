@extends('layouts.base')
@section('title')
    404 - {{ trans('errors.pagenotfound') }}
@stop

@section('main-content')
    <section class="hero is-warning is-medium is-bold">
        <div class="hero-body">
            <div class="container">
                <h1 class="title">
                    <span class="icon is-medium"><i class="fa fa-warning"></i></span>
                    <span>404 - {{ trans('errors.pagenotfound') }}</span>
                </h1>
                <h2 class="subtitle">
                    {{ trans('errors.notfindpage') }}
                </h2>
            </div>
        </div>
    </section>
@endsection
