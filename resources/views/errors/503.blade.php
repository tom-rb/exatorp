@extends('layouts.base')
@section('title')
    503 - {{ trans('errors.serviceunavailable') }}
@stop

@section('main-content')
    <section class="hero is-warning is-medium is-bold">
        <div class="hero-body">
            <div class="container">
                <h1 class="title">
                    <span class="icon is-medium"><i class="fa fa-warning"></i></span>
                    <span>503 - {{ trans('errors.serviceunavailable') }}</span>
                </h1>
                <h2 class="subtitle">
                    {{ trans('errors.tryagain') }}
                </h2>
            </div>
        </div>
    </section>
@endsection
