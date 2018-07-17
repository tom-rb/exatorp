@extends('layouts.base')
@section('title')
    403 - {{ trans('errors.forbidden') }}
@stop

@section('main-content')
    <section class="hero is-danger is-medium is-bold">
        <div class="hero-body">
            <div class="container">
                <h1 class="title">
                    <span class="icon is-medium"><i class="fa fa-ban"></i></span>
                    403 - {{ trans('errors.forbidden') }}
                </h1>
                <h2 class="subtitle">
                    {{ trans('errors.youcantdothis') }}
                </h2>
            </div>
        </div>
    </section>
@endsection