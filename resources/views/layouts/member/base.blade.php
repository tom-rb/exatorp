@extends('layouts.base')

@section('navbar')
    @include('layouts.member.navbar')
@stop

@section('main-content')
    <div class="columns is-gapless" style="flex: 1; margin-bottom: 0">
        <aside class="column is-narrow is-hidden-mobile">
            @include('layouts.member.sidebar')
        </aside>

        <div class="column app-content">
            @yield('app-content')
        </div>
    </div>
@stop
